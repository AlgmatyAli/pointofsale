<?php

namespace app\controllers;

use Yii;
use app\models\Reorder;
use app\models\ReorderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\TempReorder;
use app\models\ReorderDetails;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;


/**
 * ReorderController implements the CRUD actions for Reorder model.
 */
class ReorderController extends Controller
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
                        'actions' => ['create', 'view', 'print'],
                        'roles' => ['createReorder'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'view', 'print'],
                        'roles' => ['updateReorder'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deleteReorder'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['indexReorder'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Reorder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReorderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Reorder model.
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
     * Creates a new Reorder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Reorder();

        if ($model->load(Yii::$app->request->post())) {
            $model->branch = Yii::$app->user->identity->branch;
            $model->created_at = date('Y-m-d H:i:s');
            $model->created_by = Yii::$app->user->identity->id;
            $model->save();
            $id = $model->id;

            $temp_reorder = new TempReorder();
            $temp_reorder = TempReorder::find()->where(['created_by'=>Yii::$app->user->identity->id, 'state'=> 0])->all();

                foreach ($temp_reorder as $data) {
                 $modelDetails = new ReorderDetails();
                 $modelDetails->reorder = $id  ;
                 $modelDetails->category =  $data->category;
                 $modelDetails->quantity = $data->quantity;
                 $modelDetails->save(false);
                }
                $temp_invoice = TempReorder::deleteAll(['created_by'=>Yii::$app->user->identity->id, 'state'=> 0]);


            return $this->redirect(['view', 'id' => $model->id]);

        }elseif (Yii::$app->request->isAjax){
        $model->at = date('Y-m-d');
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
     * Updates an existing Reorder model.
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
     * Deletes an existing Reorder model.
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
     * Finds the Reorder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Reorder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Reorder::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionPrint($id)
    {
            
        $dataProvider = new ActiveDataProvider([
            'query' => ReorderDetails::find()
            ->select('reorderDetails.*, category.name, category.serialNo')
            ->leftJoin('category', 'category.id = reorderDetails.category')
            ->where(['reorderDetails.reorder' => $id])
            ->orderBy('category.name'),
                     
            'pagination' => ['pageSize' => false],
                'sort'=>false,
            
        ]);
        
        return $this->render('print', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider
        
        ]);
    }
}
