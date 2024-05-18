<?php

namespace app\controllers;

use Yii;
use app\models\CompanyInfo;
use app\models\CompanyInfoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\ForbiddenHttpException;
/**
 * CompanyInfoController implements the CRUD actions for CompanyInfo model.
 */
class CompanyInfoController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['companyInfo'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'view'],
                        'roles' => ['companyInfo'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'view'],
                        'roles' => ['companyInfo'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['companyInfo'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],

            
        ];
    }

    /**
     * Lists all CompanyInfo models.
     * @return mixed
     */

     public $exists;
    public function actionIndex()
    {
        if (Yii::$app->user->can('companyInfo')){
            $values = CompanyInfo::find()->asArray()->all();
            $searchModel = new CompanyInfoSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'values' => $values,
            ]);
        }else
        throw new ForbiddenHttpException;
        
    }

    /**
     * Displays a single CompanyInfo model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    /**
     * Creates a new CompanyInfo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $model = new CompanyInfo();
       
        $exist = CompanyInfo::find()->exists(); 
            if($exist){
                echo '<script type="text/javascript"> 
                alert("عفوا البيانات تم تسجيلها مسبقا");
                
               </script>';
            }else{
            if ($model->load(Yii::$app->request->post())) {
                if (!is_dir("img/c_info")) {
                    mkdir("img/c_info");
                    $path = "img/c_info";
                }else{
                    $path = "img/c_info";
                }
                
                if($model->file != null){
                $model->file = UploadedFile::getInstance($model,'file');
                $ext = substr(strrchr($model->file,'.'),1);
                
               if($ext != null)
                {        
                  $uniqid= uniqid(); 
                  $model->file->saveAs($path.'/'.$uniqid.'.'.$model->file->extension );   
                  $model->path=$path.'/'.$uniqid.'.'.$model->file->extension;
                } 
            }
                $model->save();

                return $this->redirect(['view', 'id' => $model->id]);
             
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
             }
             
            
            }
    
    }
    /**
     * Updates an existing CompanyInfo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if (!is_dir("img/c_info")) {
                mkdir("img/c_info");
                $path = "img/c_info";
            }else{
                $path = "img/c_info";
            }
            if($model->file != null){
            $model->file = UploadedFile::getInstance($model,'file');
            $ext = substr(strrchr($model->file,'.'),1);
           if($ext != null)
            {        
              $uniqid= uniqid(); 
              $model->file->saveAs($path.'/'.$uniqid.'.'.$model->file->extension );   
              $model->path=$path.'/'.$uniqid.'.'.$model->file->extension;
            } 
        }
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CompanyInfo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CompanyInfo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CompanyInfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CompanyInfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionExist(){
        $exists = CompanyInfo::find()->one();    
         echo Json::encode($exists);
        }

}
