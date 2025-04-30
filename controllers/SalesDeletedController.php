<?php

namespace app\controllers;

use Yii;
use app\models\SalesDeleted;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SalesDeletedController implements the CRUD actions for SalesDeleted model.
 */
class SalesDeletedController extends Controller
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
        ];
    }

    /**
     * Lists all SalesDeleted models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => SalesDeleted::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SalesDeleted model.
     * 
     * @return mixed
     */
    public function actionView()
    {
        $model = $this->findModel();
        $providerSalesDetailsDeleted = new \yii\data\ArrayDataProvider([
            'allModels' => $model->salesDetailsDeleteds,
        ]);
        return $this->render('view', [
            'model' => $this->findModel(),
            'providerSalesDetailsDeleted' => $providerSalesDetailsDeleted,
        ]);
    }

    /**
     * Creates a new SalesDeleted model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SalesDeleted();

        if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            return $this->redirect(['view', ]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Updates an existing SalesDeleted model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * 
     * @return mixed
     */
    public function actionUpdate()
    {
        $model = $this->findModel();

        if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            return $this->redirect(['view', ]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing SalesDeleted model.
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
     * Finds the SalesDeleted model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @return SalesDeleted the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel()
    {
        if (($model = SalesDeleted::findOne([])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for SalesDetailsDeleted
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddSalesDetailsDeleted()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('SalesDetailsDeleted');
            if (!empty($row)) {
                $row = array_values($row);
            }
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formSalesDetailsDeleted', ['row' => $row]);
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
