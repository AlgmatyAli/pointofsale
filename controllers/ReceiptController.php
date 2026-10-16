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
use yii\web\ForbiddenHttpException;
use yii\web\Response;

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
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],

            'access' => [
                'class' => AccessControl::class,
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
                        'actions' => ['index'],
                        'roles' => ['indexReciept'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index_'],
                        'roles' => ['canCreateVendorReceipt'],
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
        $model->scenario = Receipt::SCENARIO_SAVE;

        // كان الكود يقرأ $_GET['type'] مباشرة (خطأ إن غاب)؛ والفحص الحقيقي أدناه على النوع المُرسَل فعلاً
        if (Yii::$app->request->get('type') == 2) {
            $this->requireVendorReceiptPermission();
        }

        if ($model->load(Yii::$app->request->post())) {
            // النوع المُرسَل في النموذج هو الذي يُحفظ، لذلك عليه تُبنى الصلاحية (لا على رابط الصفحة)
            if ($model->type == 2) {
                $this->requireVendorReceiptPermission();
            }

            $exist = Receipt::find()->where(['=', 'value', $model->value])
                ->andWhere(['=', 'at', $model->at])
                ->andWhere(['=', 'clinet', $model->clinet])
                ->andWhere(['=', 'type', $model->type])
                ->one();
            if ($exist <> null) {
                Yii::$app->session->setFlash('error', Yii::t('app', "Sorry, this customer recorded the value today"));
            }

            $model->created_at  = date('Y-m-d H:i:s');
            $model->user_insert = Yii::$app->user->id;
            $model->branch      = Yii::$app->user->identity->branch;

            if ($model->type == 2 && (float)$model->value > (float)Yii::$app->user->identity->maxReceipt) {
                Yii::$app->session->setFlash('error', Yii::t('app', "Sorry You Do Not Have Permession To Discount This Value"));
            } else {
                // رقم السند التالي: قفل قصير كي لا يأخذ مستخدمان الرقم نفسه في اللحظة نفسها
                $db = Yii::$app->db;
                $lockName = 'receipt_rid_' . (int)$model->type;
                $gotLock = (int)$db->createCommand('SELECT GET_LOCK(:n, 5)', [':n' => $lockName])->queryScalar() === 1;
                if (!$gotLock) {
                    Yii::$app->session->setFlash('error', 'النظام مشغول حالياً، الرجاء إعادة المحاولة');
                } else {
                    try {
                        $model->rId = (int)Receipt::find()->where(['=', 'type', $model->type])->max('rId') + 1;
                        $saved = $model->save();
                    } finally {
                        $db->createCommand('SELECT RELEASE_LOCK(:n)', [':n' => $lockName])->execute();
                    }
                    if ($saved) {
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                    // فشل التحقق: يعاد عرض النموذج وفيه رسائل الأخطاء (كان يُحوَّل إلى view بمعرّف فارغ)
                }
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
        $model->scenario = Receipt::SCENARIO_SAVE;
        $this->guardVendorReceipt($model);
        $originalType = $model->type;

        if ($model->load(Yii::$app->request->post())) {
            // لا يُسمح بتحويل سند قبض إلى صرف أو العكس عبر التعديل
            $model->type = $originalType;

            $model->update_at = date('Y-m-d H:i:s');
            $model->user_update = Yii::$app->user->id;
            $model->branch = Yii::$app->user->identity->branch;

            // حد سند الصرف كان يُفحص عند الإنشاء فقط
            if ($model->type == 2 && $model->isAttributeChanged('value', false)
                && (float)$model->value > (float)Yii::$app->user->identity->maxReceipt) {
                Yii::$app->session->setFlash('error', Yii::t('app', "Sorry You Do Not Have Permession To Discount This Value"));
            } elseif ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
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
        $model = $this->findModel($id);
        $this->guardVendorReceipt($model);
        $model->delete();

        return $this->redirect(['index']);
    }

    /** سندات الصرف للموردين تتطلب صلاحية canCreateVendorReceipt */
    private function requireVendorReceiptPermission()
    {
        if (!Yii::$app->user->can('canCreateVendorReceipt')) {
            throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to access this page'));
        }
    }

    private function guardVendorReceipt(Receipt $model)
    {
        if ((int)$model->type === 2) {
            $this->requireVendorReceiptPermission();
        }
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
        $model = $this->findModel($id);

        return $this->render('printRecipt', [
            'model' => $model,
            'balance' => $this->clientBalance($model),
        ]);
    }

    public function actionPrintRecieptReciver($id)
    {
        $model = $this->findModel($id);

        return $this->render('printReciptReciver', [
            'model' => $model,
            'balance' => $this->clientBalance($model),
        ]);
    }

    private function clientBalance(Receipt $model)
    {
        $type = $model->c->type ?? null;
        if ($type == 0 || $type == 2) {
            return Dept::find()->where(['id' => $model->clinet])->sum('credt');
        }
        return DeptSupp::find()->where(['id' => $model->clinet])->sum('credt');
    }

    public function actionGetBalance($client, $currancy)
    {
        $clientType = Client::find()->where(['=', 'id', $client])->one();
        if ($clientType === null) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return 0;
        }

        if ($clientType['type'] == 0 || $clientType['type'] == 2) {
            $balance = Dept::find()->where(['id' => $client])->andWhere(['in', 'currency', ['0', $currancy]])->sum('credt');
        } else {
            $balance = DeptSupp::find()->where(['id' => $client])->andWhere(['in', 'currency', ['0', $currancy]])->sum('credt');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $balance;
    }

    public function actionTransferToSafe($receiptId)
    {
        $model = new Safe();

        if ($model->load(Yii::$app->request->post())) {
            $data = $this->findModel($receiptId);
            $this->guardVendorReceipt($data);
            if (!$model->validate(['safeNo']) || empty($model->safeNo)) {
                Yii::$app->session->setFlash('error', 'الرجاء اختيار الخزينة');
                return $this->redirect(['view', 'id' => $data->id]);
            }
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
