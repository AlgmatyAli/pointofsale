<?php

namespace app\controllers;

use Yii;
use app\models\TempReorder;
use app\models\TempReorderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Category;
use yii\helpers\Json;
use yii\filters\AccessControl;


/**
 * TempReorderController implements the CRUD actions for TempReorder model.
 */
class TempReorderController extends Controller
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
                        'actions' => ['create', 'view', 'delete-all', 'delete'],
                        'roles' => ['createReorder'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all TempReorder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TempReorder();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if(Yii::$app->request->post('hasEditable'))
        {
        $model =new TempReorder();
        $bookId = Yii::$app->request->post('editableKey');
        $model = TempReorder::findOne($bookId);
        
        $post = [];
        $posted = current($_POST['TempReorder']);
        $post['TempReorder'] = $posted;
        // Load model like any single model validation
        if ($model->load($post))
        {
        // When doing $result = $model->save(); I get a return value of false
        if($model->save())
        {
        if (isset($posted['quantity']))
        {
        $output = $model->quantity;
        }
        $out = Json::encode(['output'=>$output, 'message'=>'']);
        }
        }
        // Return AJAX JSON encoded response and exit
        echo $out;
        die; // write die instead of fo return and check it.
        }
        return $this->render('index', [
        
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TempReorder model.
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
     * Creates a new TempReorder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TempReorder();

        $searchModel = new TempReorderSearch();
       
        $searchModel->created_by = Yii::$app->user->identity->id;
        $searchModel->state = 0;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (yii::$app->request->post('hasEditable')) {
            $id = Yii::$app->request->post('editableKey');
            $result=TempReorder::findOne($id);
            $out=Json::encode(['output'=>'','message'=>'']);
            $post=[];
            $posted=current($_POST['TempReorder']);
            $post['TempReorder']=$posted ;
            if ($result->load($post)) {
                $result->save(false);
                if (isset($posted['quantity'])){
                   $outMessage =$result->quantity;
                }
                $output=$outMessage;
                $out=Json::encode(['output'=>$output]);
                return $out;
            }
         }

        if ($model->load(Yii::$app->request->post()) ) {
           if($model->category == null){
            Yii::$app->session->setFlash('error', Yii::t('app',"Sorry You Can not Add Empty Model"));
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
           }

            $model->state= 0;
            $model->branch = Yii::$app->user->identity->branch;
            $model->created_at = date('Y-m-d H:i:s');
            $model->created_by = Yii::$app->user->identity->id;
            $model->save();
           
        }
        return $this->render('create', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing TempReorder model.
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
     * Deletes an existing TempReorder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['create']);
    }

    /**
     * Finds the TempReorder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TempReorder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TempReorder::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionDeleteAll()
    {
        TempReorder::deleteAll(['state'=> 0,'created_by' => Yii::$app->user->identity->id]);

        // $this->findModel($id)->deleteWithRelated();
        return $this->redirect(['create']);
    }
}
