<?php

namespace app\controllers;

use Yii;
use app\models\SalesDetailsDeleted;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SalesDetailsDeletedController implements the CRUD actions for SalesDetailsDeleted model.
 */
class SalesDetailsDeletedController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'save-as-new'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => false
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all SalesDetailsDeleted models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => SalesDetailsDeleted::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SalesDetailsDeleted model.
     * 
     * @return mixed
     */
    public function actionView()
    {
        $model = $this->findModel();
        return $this->render('view', [
            'model' => $this->findModel(),
        ]);
    }

    /**
     * Creates a new SalesDetailsDeleted model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SalesDetailsDeleted();

        if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            return $this->redirect(['view', ]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SalesDetailsDeleted model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * 
     * @return mixed
     */
    public function actionUpdate()
    {
        if (Yii::$app->request->post('_asnew') == '1') {
            $model = new SalesDetailsDeleted();
        }else{
            $model = $this->findModel();
        }

        if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            return $this->redirect(['view', ]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing SalesDetailsDeleted model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @return mixed
     */
    public function actionDelete()
    {
        $this->findModel()->deleteWithRelated();

        return $this->redirect(['index']);
    }

    /**
    * Creates a new SalesDetailsDeleted model by another data,
    * so user don't need to input all field from scratch.
    * If creation is successful, the browser will be redirected to the 'view' page.
    *
    * @param mixed $id
    * @return mixed
    */
    public function actionSaveAsNew() {
        $model = new SalesDetailsDeleted();

        if (Yii::$app->request->post('_asnew') != '1') {
            $model = $this->findModel();
        }
    
        if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            return $this->redirect(['view', ]);
        } else {
            return $this->render('saveAsNew', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the SalesDetailsDeleted model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @return SalesDetailsDeleted the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel()
    {
        if (($model = SalesDetailsDeleted::findOne([])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
