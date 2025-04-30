<?php

namespace app\controllers;

use Yii;
use app\models\AuthItem;
use app\models\AuthItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\AuthItemChild;
use app\models\AuthItemChildSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * AuthItemController implements the CRUD actions for AuthItem model.
 */
class AuthItemController extends Controller
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
                        'roles' => ['createUsers'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'view'],
                        'roles' => ['updateUsers'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deleteUsers'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['indexUsers'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all AuthItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AuthItem model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $authItemChildSearchModel = new AuthItemChildSearch();
        $authItemChildSearchModel->parent = $id;
        $authItemChildProvider = $authItemChildSearchModel->search(Yii::$app->request->queryParams);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'authItemChildProvider' => $authItemChildProvider
        ]);
    }

    /**
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AuthItem();
       $userPermission = ArrayHelper::map(AuthItem::find()
       ->where(['=','type','2'])->asArray()->all(), 'name', 'description');
        if ($model->load(Yii::$app->request->post())) {
        
            $max = AuthItem::find()->select("type)")->max('type');
            $model->type = $max+1;
            $model->save(false);
            //=====================
            foreach ($model->permission as $permit) {
                $AuthItemChild = new AuthItemChild(); 
                $AuthItemChild->parent = $model->name;   
                $AuthItemChild->child = $permit;
                $AuthItemChild->save(false);
            } 
            return $this->redirect(['view', 'id' => $model->name]);
        }else {
        
        return $this->render('create', [
                    'model' => $model,
                    'userPermission'=>$userPermission,
                    'giving'=>null
         ]);
    }
    }

    /**
     * Updates an existing AuthItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $giving = ArrayHelper::map(AuthItemChild::find()->where(['parent'=>$model->name])->all()
       , 'child', 'child');
        $userPermission = ArrayHelper::map(AuthItem::find()
         ->where(['=','type','2'])->asArray()->all(), 'name', 'description');

        if ($model->load(Yii::$app->request->post())  ) {
            $model->save();

            AuthItemChild::deleteAll(['parent'=>$model->name]);
            foreach ($model->permission as $permit) {
                $AuthItemChild = new AuthItemChild(); 
                $AuthItemChild->parent = $model->name;   
                $AuthItemChild->child = $permit;
                $AuthItemChild->save(false);
            } 
            return $this->redirect(['view', 'id' => $model->name]);
        }

        return $this->render('update', [
            'model' => $model,
            'userPermission'=>$userPermission,
            'giving' => $giving
        ]);
    }

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthItem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
