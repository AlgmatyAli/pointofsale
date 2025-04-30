<?php

namespace app\controllers;

use Yii;
use app\models\Image;
use app\models\ImageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ImageController implements the CRUD actions for Image model.
 */
class ImageController extends Controller
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
        ];
    }

    /**
     * Lists all Image models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ImageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Image model.
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
     * Creates a new Image model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($claimId)
    {
        $model = new Image();

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at	 = date('Y-m-d H:i:s');
            $model->user_insert = Yii::$app->user->id;
            $model->update_at = date('Y-m-d H:i:s');
            $model->user_update = Yii::$app->user->id;
            $model->claimId = $claimId;
            // =========

            if (!is_dir("img/claim/".$model->claimId)) {
                mkdir("img/claim/".$model->claimId);
                $path = "img/claim/".$model->claimId;
            }else{
                $path = "img/claim/".$model->claimId;
            }

            
            $model->file = UploadedFile::getInstance($model,'file');
            $ext = substr(strrchr($model->file,'.'),1);

           if($ext != null)
            {        
              $uniqid= uniqid(); 
              $model->file->saveAs($path.'/'.$uniqid.'.'.$model->file->extension);   
              $model->path=$path.'/'.$uniqid.'.'.$model->file->extension;
            } 
            // =========
            $model->save();

            return $this->redirect(['claim-form/view', 'id' => $model->claimId]);
        }elseif (Yii::$app->request->isAjax) {
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
     * Updates an existing Image model.
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
     * Deletes an existing Image model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $claimId)
    {
        $this->findModel($id)->delete();

        
        return $this->redirect(['claim-form/view', 'id' => $claimId]);
    }

    /**
     * Finds the Image model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Image the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Image::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
