<?php

namespace app\controllers;

use Yii;
use app\models\CustomsOffice;
use app\models\CustomsOfficeSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CustomsOfficeController implements the CRUD actions for CustomsOffice model.
 */
class CustomsOfficeController extends Controller
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
                        'actions' => ['index'],
                        'roles' => ['indexUsers'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'view', 'histrans'],
                        'roles' => ['createUsers'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'view'],
                        'roles' => ['updateUsers'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deleteUsers'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['changepassword'],
                        'roles' => ['changePassword'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all CustomsOffice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CustomsOfficeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CustomsOffice model.
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
     * Creates a new CustomsOffice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CustomsOffice();

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at	 = date('Y-m-d H:i:s');
            $model->user_insert = Yii::$app->user->id;
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CustomsOffice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->update_at	 = date('Y-m-d H:i:s');
            $model->user_update = Yii::$app->user->id;
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing CustomsOffice model.
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
     * Finds the CustomsOffice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CustomsOffice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomsOffice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionHistrans()  
    {
        $model = new CustomsOffice();
        if ($model->load(Yii::$app->request->post())) {
            if($model->allData != 0){
                $model->min_date = '2023-01-30';
                $model->max_date = date('Y-m-d');
            }

            $sqlSum = " SELECT 
            sum(v_customsOffice.value) as wared, sum(v_customsOffice.paid) as sader
            FROM v_customsOffice
            where v_customsOffice.customsOffice = ".$model->customId." 
            and v_customsOffice.at < '".$model->min_date."' 
            and v_customsOffice.currancy = '".$model->currancy."'";
            $connection = Yii::$app->db;
            $data = $connection->createCommand($sqlSum);
            $lastBalance = $data->queryAll();

            $sql = " SELECT a.id, a.customsOffice, a.paid as paid, a.value as value, a.currancy, a.notes, a.at, a.phone, a.name 
            FROM v_customsOffice a
            where a.customsOffice = ".$model->customId." 
            and a.at between '".$model->min_date."' and '".$model->max_date."' 
            and a.currancy = '".$model->currancy."'
            order by at, id";    
        

            $connection = Yii::$app->db;
            $data = $connection->createCommand($sql);
            $info = $data->queryAll();
            
            if (count($info) === 0) {
                Yii::$app->session->setFlash('error', Yii::t('app',"Pardon There is no data to view"));
                return $this->redirect(['histrans', 'model' => $model]);
             }
             return $this->render('histransrep', [
                'models' => $info,
                'min_date' => $model->min_date,
                'max_date'=>$model->max_date,
                'sumwared' => 0,
                'sumsader' => 0,
                'sum' => 0,
                'coun' => 1,
                'count' => 0,
                'lastBalance' => $lastBalance,
                'balance' => 0,
                'id' => null,
                'deleviried' =>0,
            ]);  
        }

        return $this->render('histrans', [
            'model' => $model,
        ]);
    }
}
