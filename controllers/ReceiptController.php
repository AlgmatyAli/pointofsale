<?php

namespace app\controllers;

use app\models\Client;
use Yii;
use app\models\Receipt;
use app\models\ReceiptSearch;
use app\models\ReceiptSearch_;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\Dept;
use app\models\DeptSupp;
use app\models\CompanyInfo;
use app\models\Safe;
use yii\helpers\Json;

/**
 * ReceiptController implements the CRUD actions for Receipt model.
 */
class ReceiptController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],

            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'view', 'print-reciept', 'print-reciept-reciver', 'get-balance', 'transfer-to-safe'],
                        'roles' => ['createReciept'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'view', 'print-reciept', 'print-reciept-reciver'],
                        'roles' => ['updateReciept'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deleteReciept'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['index', 'index_'],
                        'roles' => ['indexReciept'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Receipt models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReceiptSearch();
        $searchModel->type = 99;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Receipt models.
     * @return mixed
     */
    public function actionIndex_()
    {
        $searchModel = new ReceiptSearch_();
        $searchModel->type = 99;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index_', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Receipt model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Receipt model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Receipt();

        if ($model->load(Yii::$app->request->post())) {
            $company = CompanyInfo::find()->one();
            $exist = Receipt::find()->where(['=', 'value', $model->value])
                ->andWhere(['=', 'at', $model->at])
                ->andWhere(['=', 'clinet', $model->clinet])->one();
            if ($exist <> null) {
                Yii::$app->session->setFlash('error', Yii::t('app', "Sorry, this customer recorded the value today"));
            }

            $model->rId = Receipt::find()->where(['=', 'type', $model->type])->max('rId') + 1;
            $model->created_at    = date('Y-m-d H:i:s');
            $model->user_insert = Yii::$app->user->id;
            $model->branch = Yii::$app->user->identity->branch;
            //$model->currancy = $company['currancy'];
            if ($model->value > Yii::$app->user->identity->maxReceipt && $model->type  == 2) {
                Yii::$app->session->setFlash('error', Yii::t('app', "Sorry You Do Not Have Permession To Discount This Value"));
            } else {
                $model->save();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Receipt model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->update_at = date('Y-m-d H:i:s');
            $model->user_update = Yii::$app->user->id;
            $model->branch = Yii::$app->user->identity->branch;
            $model->save();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Receipt model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Receipt model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Receipt the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Receipt::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionPrintReciept($id)
    {
        $client = $this->findModel($id);

        if ($client->c->type == 0 || $client->c->type == 2) {
            $balance = Dept::find()->where(['id' => $client->clinet])->sum('credt');
        } else {
            $balance = DeptSupp::find()->where(['id' => $client->clinet])->sum('credt');
        }

        return $this->render('printRecipt', [
            'model' => $this->findModel($id),
            'balance' => $balance,
            // 'numtoarb'=>$this->numtoarb($total['value'])
        ]);
    }

    public function actionPrintRecieptReciver($id)
    {
        $client = $this->findModel($id);

        if ($client->c->type == 0 || $client->c->type == 2) {
            $balance = Dept::find()->where(['id' => $client->clinet])->sum('credt');
        } else {
            $balance = DeptSupp::find()->where(['id' => $client->clinet])->sum('credt');
        }


        return $this->render('printReciptReciver', [
            'model' => $this->findModel($id),
            'balance' => $balance,
            // 'numtoarb'=>$this->numtoarb($total['value'])
        ]);
    }

    public function actionGetBalance($client, $currancy)
    {
        $clientType = Client::find()->where(['=', 'id', $client])->one();

        if ($clientType['type'] == 0 || $clientType['type'] == 2) {
            $balance = Dept::find()->where(['id' => $client])->andWhere(['in', 'currency', ['0', $currancy]])->sum('credt');
        } else {
            $balance = DeptSupp::find()->where(['id' => $client])->andWhere(['in', 'currency', ['0', $currancy]])->sum('credt');
        }
        Yii::$app->response->format = Yii\web\Response::FORMAT_JSON;
        return $balance;
    }

    public function actionTransferToSafe($receiptId)
    {
        $model = new Safe();

        if ($model->load(Yii::$app->request->post())) {
            $data = Receipt::find()->where(['=', 'id', $receiptId])->one();
            if ($data->type == 1) {
                $type = 2;
            } else {
                $type = 1;
            }
            $command = Yii::$app->db->createCommand("INSERT INTO safe 
         (`branch`, `safeNo`, `value`, `at`, `type`, `why`, `user_insert`, `created_at`)
         VALUES 
         (:branch, :safeNo, :value, :at, :type, :why, :user_insert, :created_at  )");
            $command->bindValue(':branch', Yii::$app->user->identity->branch);
            $command->bindValue(':safeNo', $model->safeNo);
            $command->bindValue(':value', $data->value);
            $command->bindValue(':at', date('Y-m-d'));
            $command->bindValue(':type', $type);
            $command->bindValue(':why', $data->why);
            $command->bindValue(':user_insert', Yii::$app->user->identity->id);
            $command->bindValue(':created_at', date('Y-m-d'));
            $command->execute();
            return $this->redirect(['safe/index']);
        } elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('transferToSafe', [
                'model' => $model,
            ]);
        } else {
            return $this->render('transferToSafe', [
                'model' => $model,
            ]);
        }
    }
}
