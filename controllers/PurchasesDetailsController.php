<?php

namespace app\controllers;

use Yii;
use app\models\PurchasesDetails;
use app\models\PurchasesDetailsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Purchases;
use app\models\Category;
use app\models\Stocks;
use yii\filters\AccessControl;

/**
 * PurchasesDetailsController implements the CRUD actions for PurchasesDetails model.
 */
class PurchasesDetailsController extends Controller
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
                        'actions' => ['create', 'view', 'print-bill', 'print-bill-with-out-price', 'print-bill-with-place', 'remove', 'add-purchases'],
                        'roles' => ['createPurchases'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'view', 'print-bill', 'print-bill-with-out-price', 'print-bill-with-place', 'remove', 'transfer-to-temp-invoice', 'add-purchases'],
                        'roles' => ['updatePurchases'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deletePurchases'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['indexPurchases'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['save-as-new'],
                        'roles' => ['SavePurchasesAsNew'],
                    ],
                    // =============
                    [
                        'allow' => true,
                        'actions' => ['back-purchase-create', 'view', 'print-bill', 'print-bill-with-out-price'],
                        'roles' => ['createBackPurchase'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['back-purchase-update', 'view', 'print-bill', 'print-bill-with-out-price'],
                        'roles' => ['updateBackPurchase'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deleteBackPurchase'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['indexBackPurchase'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all PurchasesDetails models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PurchasesDetailsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PurchasesDetails model.
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
     * Creates a new PurchasesDetails model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PurchasesDetails();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PurchasesDetails model.
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
     * Deletes an existing PurchasesDetails model.
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
     * Finds the PurchasesDetails model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PurchasesDetails the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PurchasesDetails::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionAddPurchases($categoryid,$purchasesId)
    {
        $model = new PurchasesDetails();

        // if ($model->load(Yii::$app->request->post())){
            
            $category = new Category();
           
            $category =Stocks::find()
            ->joinWith('prices')
            ->where(['prices.category'=>$categoryid])->one();
         
            $temp = PurchasesDetails::find()->where([
                'category'=>$category,
                'PurchasesId'=>$purchasesId,
                ])->one();

                $purchase = Purchases::find()->where(['id'=>$purchasesId])->one();
                    if (!isset($temp)) {
                        $model->quantity = 1;
                        $model->salePrice = $category->prices->maxPrice;
                        $model->salePrice_ = $category->prices->minPrice;
                        $model->salePrice_2 = $category->prices->minPrice2;
                        $model->salePrice_3 = $category->prices->minPrice3; 
                        $model->costPrice = $category->prices->costPrice;
                        $model->totalCost = $category->prices->costPrice;
                        $model->box = 1;//$category->box;
                        $model->PurchasesId= $purchasesId;
                        $model->category = $categoryid;
                        $model->save(false);                    
                    }else{
                        $temp->quantity= $temp->quantity + 1;
                        $model->salePrice = $category->prices->maxPrice;
                        $model->salePrice_ = $category->prices->minPrice;
                        $model->salePrice_2 = $category->prices->minPrice2;
                        $model->salePrice_3 = $category->prices->minPrice3; 
                        $model->costPrice = $category->prices->costPrice;
                        $model->totalCost = $category->prices->costPrice;
                        $model->box = 1;//$category->box;
                        $model->PurchasesId= $purchasesId;
                        $model->category = $categoryid;
                        $model->save(false); 
                    }
                    // ========
                    //     $stocks = Stocks::find()->where(['category' => $categoryid])
                    //     ->andWhere(['branch' => Yii::$app->user->identity->branch])
                    //     ->andWhere(['type' => $purchase->type])->one();
                    // if ($purchase->type == 3) {
                    //     $stocks->quantity = ($stocks->quantity - $temp->quantity);
                    //     $stocks->save(true);
                    // } elseif ($purchase->type == 1 || $purchase->type == 2) {
                    //     $stocks->quantity = ($stocks->quantity + $temp->quantity);
                    //     $stocks->save(true);
                    // }
                    // =========
                    $purchase = Purchases::find()->where(['id'=>$purchasesId])->one();
                    $purchase->total = $purchase->total + ($model->quantity * $model->costPrice);
                    $purchase->save(false);
            return $this->redirect(['purchases/update','id' => $purchasesId ]);
        {
            return $this->redirect(['purchases/update','id' => $purchasesId
            ]);
        }
    }
}
