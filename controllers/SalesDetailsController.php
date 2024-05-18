<?php

namespace app\controllers;

use app\models\CompanyInfo;
use Yii;
use app\models\SalesDetails;
use app\models\Sales;
use app\models\SalesDetailsSearch;
use app\models\Stocks;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SalesDetailsController implements the CRUD actions for SalesDetails model.
 */
class SalesDetailsController extends Controller
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
        ];
    }

    /**
     * Lists all SalesDetails models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SalesDetailsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SalesDetails model.
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
     * Creates a new SalesDetails model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SalesDetails();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SalesDetails model.
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
     * Deletes an existing SalesDetails model.
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
     * Finds the SalesDetails model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SalesDetails the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SalesDetails::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
    public function actionBack($id)
    {

        $model = $this->findModel($id);
        $sale = new Sales();
        $data = SalesDetails::find()->where(['id' => $id])->one();
        // die(var_dump($data));
        $old_quantity = $model->quantity;
        $old_sale =  Sales::find()->where(['id' => $data->salesId])->one();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->quantity > $old_quantity) {
                Yii::$app->session->setFlash('error', Yii::t('app', "You can NOT return a larger quantity than what is on the invoice"));
                return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
            }
            $bill = Sales::find()->where(['type' => 2])->max('billId') + 1;
            $sale->billId = $bill;

            $sale->billId = $bill;
            $sale->payWay = $old_sale->payWay;
            $sale->at = Date('Y-m-d');
            $sale->clinet = $old_sale->clinet;
            $sale->branch = Yii::$app->user->identity->branch;
            $sale->total = $data->salePrice * $model->quantity;
            $sale->type = 2;
            $sale->user_insert = Yii::$app->user->identity->id;
            $sale->user_update = Yii::$app->user->identity->id;
            $sale->created_at = Date('Y-m-d h:m:s');
            // $sale->updated_at = Date('Y-m-d hh:mm:ss');

            $sale->save(false);

            $quantity = $model->quantity;
            $model = new SalesDetails();
            $model->salesId = $sale->id;
            $model->category = $data->category;
            $model->salePrice = $data->salePrice;
            $model->salePrice = $data->salePrice;
            $model->costPrice = $data->costPrice;
            $model->quantity = $quantity;

            $model->expire = $data->expire;
            $model->box = $data->box;

            $model->save();
            return $this->redirect(['sales/print', 'id' => $sale->id]);
        }

        return $this->render('back', [
            'model' => $model,
        ]);
    }

    public function actionAddsale($categoryid, $salesId)
    {
        $model = new SalesDetails();
        
        $company = CompanyInfo::find()->one();
        if($company->criteriaـvalue != 0) {
            $criteriaـvalue = $company->criteriaـvalue;
        }else{
            $criteriaـvalue = 1;
        }

        if($company->rate != 0) {
            $rate = ($company->rate/100) ;
            $maxPrice = 'CASE
            WHEN maxPrice >= '. $criteriaـvalue .' THEN round(maxPrice * "' . $rate . '" + maxPrice)
            ELSE maxPrice
            END  as maxPrice';
            
            $minPrice = 'CASE
            WHEN maxPrice >= '. $criteriaـvalue .' THEN round(minPrice * "' . $rate . '" + minPrice)
            ELSE minPrice
            END  as minPrice';
        }else{
            $maxPrice = 'maxPrice';
            $minPrice = 'minPrice';
        }

        $secript = [
            'stocks.category','stocks.quantity as quantity', $maxPrice, 'costPrice as costPrice', $minPrice,
            'CASE
                WHEN `type` =1
                THEN "متوفر"
                WHEN `type` =2
                THEN "متوفر"
                ELSE "قريبا" END as type'
        ];

        $category = Stocks::find()
            ->select($secript)
            ->joinWith('prices')
            ->where(['prices.category' => $categoryid])->one();


        $temp = SalesDetails::find()->where([
            'category' => $categoryid,
            'salesId' => $salesId,
        ])->one();

        $sale = Sales::find()->where(['id' => $salesId])->one();
        if (!isset($temp)) {
        if($company->criteriaـvalue != 0) {
         $maxP = floatval($category->maxPrice);
         $maxP = round($maxP);
         $diff=0;
         $len = strlen($maxP);
         $value = substr($maxP, $len-1, $len-1);
         
         if(intval($value) < 5 && intval($value) != 0){
          $diff = 5 - intval($value);
          $maxP += $diff;
         }
         if (intval($value) > 5) {
          $diff = 10 - intval($value);
          $maxP += $diff;
         }
        }else{
            $maxP = $category->maxPrice;
        }
            $model->quantity = 1;
            $model->salePrice = $maxP;
            $model->costPrice = $category->costPrice;
            $model->box = 1;
            $model->salesId = $salesId;
            $model->category = $categoryid;
            $model->save(false);
        } else {
            $temp->quantity = $temp->quantity + 1;
            // $model->salePrice = $category->maxPrice;
            // $model->costPrice = $category->costPrice;
            // $model->box = 1;
            // $model->salesId = $salesId;
            // $model->category = $categoryid;
            $temp->save(false);
        }
        // ========
        $stocks = Stocks::find()->where(['category' => $categoryid])
            ->andWhere(['branch' => Yii::$app->user->identity->branch])
            ->andWhere(['type' => $sale->type])->one();
        if ($sale->type == 3) {
            $stocks->quantity = ($stocks->quantity + 1);
            $stocks->save(true);
        } elseif ($sale->type == 1 || $sale->type == 2) {
            $stocks->quantity = ($stocks->quantity - 1);
            $stocks->save(true);
        }
        // =========
        $sales = Sales::find()->where(['id' => $salesId])->one();
        $sales->total = $sales->total + ($model->quantity * $model->salePrice);
        $sales->save(false);
        return $this->redirect(['sales/update', 'id' => $salesId]); {
            return $this->redirect(['sales/update', 'id' => $salesId]);
        }
    }
}
