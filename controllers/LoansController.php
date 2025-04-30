<?php

namespace app\controllers;

use Yii;
use app\models\Loans;
use app\models\LoansSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;

/**
 * LoansController implements the CRUD actions for Loans model.
 */
class LoansController extends Controller
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
                        'actions' => ['create', 'c-discount', 'view', 'get-drawing', 'histrans', 'get-salary', 'c-extra-job'],
                        'roles' => ['createSalary'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'c-discount', 'view', 'get-drawing', 'get-salary', 'c-extra-job'],
                        'roles' => ['updateSalary'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deleteSalary'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['indexSalary'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Loans models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LoansSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Loans model.
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
     * Creates a new Loans model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Loans();

        if ($model->load(Yii::$app->request->post())) {
            $exsit = Loans::find()->where(['employee' => $model->employee])
            ->andWhere(['status' => 0])->one();
            if($exsit <> null){
                Yii::$app->session->setFlash('error', Yii::t('app', "هذا الموظف لديه سلفة غير منتهية"));
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            $model->created_at = date('Y-m-d H:i:s');
            $model->created_by = Yii::$app->user->identity->id;
            $model->status = 0;
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Loans model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Loans model.
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
     * Finds the Loans model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Loans the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Loans::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionState($id)
    {
        $state = $this->findModel($id);
        if($state->status == 0){
            Loans::updateAll(['status' => 1], ['=', 'id', $id]);
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionGetTotal($employee)
    {
        $sql = "SELECT loanValue, kestValue 
        FROM `loans` 
        where loans.status = 0 and loans.employee = " . $employee . "
        ORDER BY `loans`.`id` DESC LIMIT 1";

        $connection = Yii::$app->db;
        $data = $connection->createCommand($sql);
        $info = $data->queryAll();
        if ($info == null) {
            die("Sorry no thing to preview");
        }

        foreach ($info as $value) {
            $val = $value;
        }

        echo Json::encode($val);
    }
}
