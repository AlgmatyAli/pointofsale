<?php

namespace app\controllers;

use app\models\Employee;
use app\models\EmpSalary;
use app\models\EmpSalarySearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EmpSalaryController implements the CRUD actions for EmpSalary model.
 */
class EmpSalaryController extends Controller
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
                        'actions' => ['create', 'c-discount', 'view', 'get-drawing',
                         'histrans', 'get-salary', 'c-extra-job'],
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
     * Lists all EmpSalary models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmpSalarySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EmpSalary model.
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
     * Creates a new EmpSalary model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EmpSalary();

        if ($model->load(Yii::$app->request->post())) {

            $salary = Employee::find()->where(['id' => $model->employee])->one();

            $value = EmpSalary::find()
                ->select('sum(value) as drawing')
                ->where(['employee' => $model->employee])
                ->andWhere(['month' => $model->month])
                ->andWhere(['year' => $model->year])
                ->andWhere("type IN('1,2')")
                ->asArray()
                ->one();

            if (($value["drawing"] + $model->value) > $salary->salary) {
                Yii::$app->session->setFlash('error', Yii::t('app', "This Employee End The Amount Of Slary"));
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            $model->created_at = date('Y-m-d H:i:s');
            $model->created_by = Yii::$app->user->identity->id;
            $model->type = 1;
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EmpSalary model.
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
     * Deletes an existing EmpSalary model.
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
     * Finds the EmpSalary model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmpSalary the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EmpSalary::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionGetDrawing($month, $employee)
    {
        $value = EmpSalary::find()
            ->select('sum(value) as drawing')
            ->where(['id' => $employee])
            ->andWhere(['month' => $month])
            ->andWhere(['year' => $month])
            ->asArray()
            ->one();
        echo json::encode($value);
    }

    /**
     * Creates a new EmpSalary model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCDiscount()
    {
        $model = new EmpSalary();

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = date('Y-m-d H:i:s');
            $model->created_by = Yii::$app->user->identity->id;
            $model->type = 2;
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('cdiscount', [
            'model' => $model,
        ]);
    }

    public function actionCExtraJob()
    {
        $model = new EmpSalary();

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = date('Y-m-d H:i:s');
            $model->created_by = Yii::$app->user->identity->id;
            $model->type = 3;
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('cextrajob', [
            'model' => $model,
        ]);
    }

    public function actionHistrans()
    {
        $model = new EmpSalary();
        if ($model->load(Yii::$app->request->post())) {

            $sql = " SELECT emp_salary.at, user.username as name, emp_salary.employee as employee,
                     emp_salary.value as wared, emp_salary.id as id, emp_salary.why as kind,
                     emp_salary.value as sader
                     FROM emp_salary, user
                     where  emp_salary.employee = user.id and emp_salary.employee = " . $model->employee . "
                     and emp_salary.at between '" . $model->min_date . "' and '" . $model->max_date . "'
                     order by emp_salary.at";

            $connection = Yii::$app->db;
            $data = $connection->createCommand($sql);
            $info = $data->queryAll();
            if ($info == null) {
                die("Sorry no thing to preview");
            }

            return $this->render('histransrep', [
                'models' => $info,
                'min_date' => $model->min_date,
                'max_date' => $model->max_date,
            ]);
        }

        return $this->render('histrans', [
            'model' => $model,
        ]);
    }

    public function actionGetSalary($employee, $year, $month)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $sql = "SELECT max(`employee`.`salary`) AS `salary`, sum(`emp_salary`.`value`) as value,
         max(`emp_salary`.`at`) as at,
	   (select emp_salary.value from emp_salary where emp_salary.type = 1
        and emp_salary.employee =   $employee   and month = $month and year = $year
        and id =(select max(id) from emp_salary where emp_salary.type = 1
        and emp_salary.employee =   $employee   and month = $month and year = $year)
        ORDER BY `emp_salary`.`id` DESC ) as lastpay
        FROM `emp_salary` LEFT JOIN `employee` ON employee.id = emp_salary.employee
        where emp_salary.type = 1 and emp_salary.employee = $employee   and month = $month and year = $year
        ORDER BY `emp_salary`.`id` DESC LIMIT 1";
        
        $connection = Yii::$app->db;
        $data = $connection->createCommand($sql);
        $info = $data->queryAll();
       
        foreach ($info as $value) {
            $val = $value;
    }
        if ($val['salary'] == null) {
           
           $sql = "SELECT salary FROM  employee  where  id = $employee ";
           $connection = Yii::$app->db;
           $data = $connection->createCommand($sql);
           $info = $data->queryAll();
           foreach ($info as $value) {
                $val = $value;
             }
        }
         return $val;
    }
}
