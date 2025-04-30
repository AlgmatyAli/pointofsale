<?php

namespace app\controllers;

use Yii;
use app\models\Safe;
use app\models\SafeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * SafeController implements the CRUD actions for Safe model.
 */
class SafeController extends Controller
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
                        'actions' => ['create', 'view'],
                        'roles' => ['createSafe'],
                    ],
                    [ 
                        'allow' => true,
                        'actions' => ['update', 'view'],
                        'roles' => ['updateSafe'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deleteSafe'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['indexSafe'],
                    ],
                ],
            ],
        ];
        
    }

    /**
     * Lists all Safe models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SafeSearch();
        $searchModel->type =99;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // $total2 = Safe::find()->where(['=', 'type', 2])->sum('value');
        // $total1 = Safe::find()->where(['=', 'type', 1])->sum('value *-1');
        // $total = $total2 + $total1;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            //'total' => $total
        ]);
    }

    /**
     * Displays a single Safe model.
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
     * Creates a new Safe model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Safe();

        if ($model->load(Yii::$app->request->post())) {
           
            $model->created_at	= date('Y-m-d H:i:s');
            $model->user_insert = Yii::$app->user->id;
            $model->save();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Safe model.
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
     * Deletes an existing Safe model.
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
     * Finds the Safe model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Safe the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Safe::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
