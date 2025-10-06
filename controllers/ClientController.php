<?php

namespace app\controllers;

use Yii;
use app\models\Client;
use app\models\ClientSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Dept;
use app\models\HistransClient;

/**
 * ClientController implements the CRUD actions for Client model.
 */
class ClientController extends Controller
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
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'view', 'create-client'],
                        'roles' => ['createClient'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'view', 'state', 'get-client'],
                        'roles' => ['updatClient'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deleteClient'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['index', 'index_'],
                        'roles' => ['indexClient'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['credts'],
                        'roles' => ['clientDepts'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['histrans', 'type'],
                        'roles' => ['clientHistrans'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Client models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Lists all Client models.
     * @return mixed
     */
    public function actionIndex_()
    {
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index_', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Client model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $providerPurchases = new \yii\data\ArrayDataProvider([
            'allModels' => $model->purchases,
        ]);
        $providerCredit = new \yii\data\ArrayDataProvider([
            'allModels' => $model->credit,

        ]);
        $providerDebit = new \yii\data\ArrayDataProvider([
            'allModels' => $model->debit,
        ]);
        $providerSales = new \yii\data\ArrayDataProvider([
            'allModels' => $model->sales,
        ]);
        $providerInitsales = new \yii\data\ArrayDataProvider([
            'allModels' => $model->initsales,
        ]);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'providerPurchases' => $providerPurchases,
            'providerCredit' => $providerCredit,
            'providerDebit' => $providerDebit,
            'providerSales' => $providerSales,
            'providerInitsales' => $providerInitsales,
        ]);
    }
    /**
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Client();

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at     = date('Y-m-d H:i:s');
            $model->user_insert = Yii::$app->user->id;
            $model->branch = Yii::$app->user->identity->branch;
            $model->save();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->update_at     = date('Y-m-d H:i:s');
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
     * Deletes an existing Client model.
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
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateClient()
    {
        $model = new Client();

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at     = date('Y-m-d H:i:s');
            $model->user_insert = Yii::$app->user->id;
            $model->branch = Yii::$app->user->identity->branch;
            $model->save();

            return $this->redirect(Yii::$app->request->referrer);
        } elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        } else {

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new Dept model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCredts()
    {
        $model = new Dept();
        if ($model->load(Yii::$app->request->post())) {

            if ($model->type == 0) {
                $model->type = '0,2';
                $sql = " SELECT dept.id, dept.type as type, MAX(dept.name) as name, SUM(dept.credt) as credt, 
                MAX(dept.Phone) as phone, max(dept.deserving) as deserving, max(post_paid) as post_paid FROM dept
                where dept.Type in(" . $model->type . ")  and dept.currency in( 0, " . $model->currency . ")
                GROUP BY dept.id, dept.type having SUM(dept.credt)<>0";
                $connection = Yii::$app->db;
                $model = $connection->createCommand($sql);
                $info = $model->queryAll();
                if ($info == null) {
                    Yii::$app->session->setFlash('error', Yii::t('app', "There is Nothing to Show !"));
                    return $this->redirect(Yii::$app->request->referrer);
                }

                return $this->render('creditsrep', [
                    'models' => $info,
                    'dept' => 0,
                    'credt' => 0,
                    'coun' => 1,
                    'count' => 0,
                ]);
            } elseif ($model->type == 1) {
                $model->type = '1';
                $sql = " SELECT dept_supp.id, dept_supp.type as type, MAX(dept_supp.name) as name,  SUM(dept_supp.credt) as credt,
                MAX(dept_supp.Phone) as phone, '1' as post_paid FROM dept_supp
                where dept_supp.Type in(" . $model->type . ") and dept_supp.currency in( 0, " . $model->currency . ")
                GROUP BY dept_supp.id, dept_supp.type having SUM(dept_supp.credt)<>0";
                $connection = Yii::$app->db;
                $model = $connection->createCommand($sql);
                $info = $model->queryAll();
                if ($info == null) {
                    Yii::$app->session->setFlash('error', Yii::t('app', "There is Nothing to Show !"));
                    return $this->redirect(Yii::$app->request->referrer);
                }

                return $this->render('creditsrep', [
                    'models' => $info,
                    'dept' => 0,
                    'credt' => 0,
                    'coun' => 1,
                    'count' => 0,
                ]);
            }
        }
        return $this->render('credits', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new HistransClient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionHistrans()
    {
        $model = new HistransClient();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->allData != 0) {
                $model->min_date = '2020-01-30';
                $model->max_date = date('Y-m-d');
            }

            if ($model->type == 0 || $model->type == 2) {
                $types = '0,2';
                $sqlSum = " SELECT 
            sum(histrans_client.dept) as sader, sum(histrans_client.credt) as wared, max(histrans_client.type) as type,
            max(histrans_client.deleviried) as deleviried
            FROM histrans_client
            where histrans_client.Type in(" . $types . ") and histrans_client.id = " . $model->id . " 
            and histrans_client.trandate < '" . $model->min_date . "' and histrans_client.currency in( 0, " . $model->currency . ")";
                $connection = Yii::$app->db;
                $data = $connection->createCommand($sqlSum);
                $lastBalance = $data->queryAll();
            }

            if ($model->type == 1) {
                $sqlSum = " SELECT
            sum(histrans_supplier.dept) as sader, sum(histrans_supplier.credt) as wared, max(histrans_supplier.type) as type FROM histrans_supplier
            where histrans_supplier.Type in(" . $model->type . ") and histrans_supplier.id = " . $model->id . "
            and histrans_supplier.trandate < '" . $model->min_date . "' and histrans_supplier.currency in( 0, " . $model->currency . ")";
                $connection = Yii::$app->db;
                $data = $connection->createCommand($sqlSum);
                $lastBalance = $data->queryAll();
            }

            if ($model->type == 0 || $model->type == 2) {
                $types = '0,2';
                $sql = " SELECT histrans_client.id, histrans_client.trandate, histrans_client.name as name,
        histrans_client.dept as sader, histrans_client.billId as billId, histrans_client.kind as kind,
        histrans_client.credt as wared, histrans_client.printId as printId, histrans_client.type as type, histrans_client.deleviried as deleviried 
        FROM histrans_client
        where histrans_client.Type in(" . $types . ") and histrans_client.id = " . $model->id . "
        and histrans_client.trandate between '" . $model->min_date . "' and '" . $model->max_date . "'
        and histrans_client.sort <> 1  and histrans_client.currency in( 0, " . $model->currency . ")
        order by  histrans_client.trandate, histrans_client.billId";
            } elseif ($model->type == 1) {
                $sql = " SELECT histrans_supplier.id, histrans_supplier.kind, histrans_supplier.trandate, histrans_supplier.name as name, 
            histrans_supplier.dept as wared, histrans_supplier.credt as sader, histrans_supplier.billId as billId,
            histrans_supplier.printId as printId, histrans_supplier.type as type
             FROM histrans_supplier
            where histrans_supplier.Type in(" . $model->type . ") and histrans_supplier.id = " . $model->id . " and histrans_supplier.trandate 
            between '" . $model->min_date . "' and '" . $model->max_date . "'
            and histrans_supplier.sort <> 1 and histrans_supplier.currency in( 0, " . $model->currency . ")
            order by histrans_supplier.trandate, histrans_supplier.billId";
            }

            $connection = Yii::$app->db;
            $data = $connection->createCommand($sql);
            $info = $data->queryAll();

            if (count($info) === 0) {
                Yii::$app->session->setFlash('error', Yii::t('app', "Pardon There is no data to view"));
                return $this->redirect(['histrans', 'model' => $model]);
            }
            return $this->render('histransrep', [
                'models' => $info,
                'min_date' => $model->min_date,
                'max_date' => $model->max_date,
                'sumwared' => 0,
                'sumsader' => 0,
                'sum' => 0,
                'coun' => 1,
                'count' => 0,
                'lastBalance' => $lastBalance,
                'balance' => 0,
                'id' => null,
                'deleviried' => 0,
            ]);
        }

        return $this->render('histrans', [
            'model' => $model,
        ]);
    }

    public function actionType()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $type_id = $parents[0];
                $out = self::getClient($type_id);
                return ['output' => $out, 'selected' => '$selected'];
            }
        }
        return ['output' => '', 'selected' => 'select Client'];
    }

    public function getClient($type_id)
    {
        if (Yii::$app->user->identity->client == null) {
            $data = Client::find()
                ->where(['type' => $type_id])
                //->andWhere(['branch' => Yii::$app->user->identity->branch])
                ->select(['id', 'name'])->asArray()->all();
            return $data;
        } else {
            $data = Client::find()
                ->where(['type' => $type_id])
                //->andWhere(['branch' => Yii::$app->user->identity->branch])
                ->andWhere(['in', 'id', explode(',', Yii::$app->user->identity->client)])
                ->select(['id', 'name'])->asArray()->all();
            return $data;
        }
    }
}
