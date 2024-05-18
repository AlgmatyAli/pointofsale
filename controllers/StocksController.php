<?php

namespace app\controllers;

use Yii;
use app\models\Stocks;
use app\models\StocksSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StocksController implements the CRUD actions for Stocks model.
 */
class StocksController extends Controller
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
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'list', 'price-list', 'stock-taking', 'zero-q'],
                        'roles' => ['inventory']
                    ],
                    [
                        'allow' => false
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all Stocks models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StocksSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'quantity' => 0,
            'costPrice'=> 0,
            'salePrice' =>0,
        ]);
    }

    /**
     * Displays a single Stocks model.
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
     * Creates a new Stocks model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Stocks();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Stocks model.
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
     * Deletes an existing Stocks model.
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
     * Finds the Stocks model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Stocks the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Stocks::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionZeroQ()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Stocks::find()
           ->select('category.id, max(category.name) name, max(category.serialNo) serialNo,  max(category.company) company, 
                     sum(stocks.quantity) quantity, max(branches.name) branchName')
           ->leftJoin('category', 'category.id = stocks.category')
           ->leftJoin('branches','branches.id = stocks.branch')
           ->groupBy('category.id, stocks.branch')
           ->having('sum(stocks.quantity) < 0')
           ->orderBy('category.id'),
                 
        'pagination' => [
            'pageSize' => 70],
      ]);

        return $this->render('zeroQ', [
            'dataProvider' => $dataProvider,
        ]);
    }
}
