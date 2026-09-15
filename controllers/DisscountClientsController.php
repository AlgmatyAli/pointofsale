<?php

namespace app\controllers;

use app\models\DisscountClients;
use app\models\DisscountClientsSearch;
use app\models\DisscountClientsSearch_;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DisscountClientsController implements the CRUD actions for DisscountClients model.
 */
class DisscountClientsController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all DisscountClients models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DisscountClientsSearch();
        $searchModel->type = 1;
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndex_()
    {
        $searchModel = new DisscountClientsSearch_();
        $searchModel->type = 2;
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index_', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DisscountClients model.
     * @param int $id رقم التسلسل
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new DisscountClients model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreateClients()
    {
        $model = new DisscountClients();
        if ($_GET['type'] == 2 && !Yii::$app->user->can('canCreateVendorReceipt')) {
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You are not allowed to access this page'));
        }
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $exist = DisscountClients::find()->where(['=', 'value', $model->value])
                    ->andWhere(['=', 'at', $model->at])
                    ->andWhere(['=', 'client', $model->client])
                    ->andWhere(['=', 'type', $model->type])
                    ->one();
                if ($exist <> null) {
                    Yii::$app->session->setFlash('error', Yii::t('app', "Sorry, this customer recorded the value today"));
                }
                $model->created_at    = date('Y-m-d H:i:s');
                $model->created_by = Yii::$app->user->id;
                $model->branch = Yii::$app->user->identity->branch;
                $model->save();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create_clients', [
            'model' => $model,
        ]);
    }

    public function actionCreateSuppliers()
    {
        $model = new DisscountClients();
        if ($_GET['type'] == 2 && !Yii::$app->user->can('canCreateVendorReceipt')) {
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You are not allowed to access this page'));
        }
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $exist = DisscountClients::find()->where(['=', 'value', $model->value])
                    ->andWhere(['=', 'at', $model->at])
                    ->andWhere(['=', 'client', $model->client])
                    ->andWhere(['=', 'type', $model->type])
                    ->one();
                if ($exist <> null) {
                    Yii::$app->session->setFlash('error', Yii::t('app', "Sorry, this customer recorded the value today"));
                }
                $model->created_at    = date('Y-m-d H:i:s');
                $model->created_by = Yii::$app->user->id;
                $model->branch = Yii::$app->user->identity->branch;
                $model->save();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create_suppliers', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DisscountClients model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id رقم التسلسل
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->updated_at = date('Y-m-d H:i:s');
            $model->updated_by = Yii::$app->user->id;
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DisscountClients model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id رقم التسلسل
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DisscountClients model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id رقم التسلسل
     * @return DisscountClients the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DisscountClients::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
