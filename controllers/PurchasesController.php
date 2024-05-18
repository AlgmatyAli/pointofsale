<?php

namespace app\controllers;

use app\models\CompanyInfo;
use Yii;
use app\models\Purchases;
use app\models\PurchasesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\PurchasesDetails;
use app\models\TempInvoicePurchase;
use yii\data\ActiveDataProvider;
use app\models\Prices;
use app\models\Inventory;
use app\models\PurchasesDetailsSearch;
use app\models\Stocks;
use yii\filters\AccessControl;
use yii\helpers\Json;


/**
 * PurchasesController implements the CRUD actions for Purchases model.
 */
class PurchasesController extends Controller
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
                        'actions' => ['create', 'view', 'print-bill', 'print-bill-with-out-price',
                         'print-bill-with-place', 'remove', 'add-purchases', 'date-of-arrival', 'noprice'],
                        'roles' => ['createPurchases'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'view', 'print-bill', 'print-bill-with-out-price',
                        'print-bill-with-place', 'remove', 'transfer-to-temp-invoice',
                        'add-purchases', 'date-of-arrival', 'noprice'],
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
     * Lists all Purchases models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PurchasesSearch();
        $searchModel->type = 99;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Purchases model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    public function actionPrintBill($id)
    {
        if (Yii::$app->user->identity->printPurtchaseInvoice == 1) {
            $dataProvider = new ActiveDataProvider([
                'query' => PurchasesDetails::find()
                    ->select('purchasesDetails.*, category.name, category.serialNo')
                    ->leftJoin('category', 'category.id = purchasesDetails.category')
                    ->where(['purchasesDetails.PurchasesId' => $id]),

                'pagination' => [
                    'pageSize' => false
                ],
                'sort' => false,

            ]);

            return $this->render('printBill', [
                'model' => $this->findModel($id),
                'dataProvider' => $dataProvider
            ]);
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', "Sorry You Do Not Have Permession to Print Invoice"));
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }
    }

    public function actionPrintBillWithOutPrice($id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => PurchasesDetails::find()
                ->select('purchasesDetails.*, category.name, category.serialNo')
                ->leftJoin('category', 'category.id = purchasesDetails.category')
                ->where(['purchasesDetails.PurchasesId' => $id]),

            'pagination' => [
                'pageSize' => false
            ],
            'sort' => false,

        ]);

        return $this->render('printBillWithOutPrice', [
            'model' => $this->findModel($id),
            // 'infos' => $infos,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionPrintBillWithPlace($id)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => PurchasesDetails::find()
                ->select('purchasesDetails.*, category.name, category.serialNo')
                ->leftJoin('category', 'category.id = purchasesDetails.category')
                ->where(['purchasesDetails.PurchasesId' => $id])
                ->orderBy('category.name'),

            'pagination' => ['pageSize' => 70],
            'sort' => false,
        ]);

        return $this->render('printBillWithPlace', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider

        ]);
    }

    /**
     * Updates an existing Purchases model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $searchModel = new PurchasesDetailsSearch();
        $searchModel->PurchasesId = $model->id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $oldType = $model->type;

        if (yii::$app->request->post('hasEditable')){
            $id = Yii::$app->request->post('editableKey');
            $old = PurchasesDetails::find()->where(['id' => $id])->one();
            $result = PurchasesDetails::findOne($id);

            Json::encode(['output' => '', 'message' => '']);
            $post = [];
            $posted = current($_POST['PurchasesDetails']);

            $post['PurchasesDetails'] = $posted;
            if ($result->load($post)) {

                if (isset($posted['quantity'])) {
                    $outMessage = $result->quantity;
                    $quantity = $posted['quantity'];
                    $q = $quantity - $old->quantity;

                    $purchase = Purchases::find()->where(['id' => $result->PurchasesId])->one();
                    $purchase->total = $purchase->total + ($q * $result->costPrice);
                    $purchase->save(false);
                }
                if (isset($posted['salePrice'])) {
                    $salePrice = $posted['salePrice'];
                    $p = $salePrice - $old->salePrice;
                    $outMessage = $result->salePrice;
                }
                if (isset($posted['salePrice_'])) {
                    $salePrice = $posted['salePrice_'];
                    $p = $salePrice - $old->salePrice_;
                    $outMessage = $result->salePrice_;
                }
                $result->save(false);
                $output = $outMessage;
                $out = Json::encode(['output' => $output]);
                return $out;
            }
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->type == 1) {
                if($oldType == 3 && $model->totalCost != 0){
                    $rate = ($model->total + $model->totalCost)/ $model->total;
                    $items =  PurchasesDetails::find()->where('PurchasesId = ' . $model->id)->all();
                    foreach ($items  as $value){
                        $value->totalCost = ($value->costPrice * $rate);
                        $value->save(false);
                    }
                }
                //============== read last cost and quantity from inventory
                $items =  PurchasesDetails::find()->where('PurchasesId = ' . $model->id)->all();
                //============== add to PurchasesDetails
                foreach ($items  as $value){
                    // ====================
                    $inventory =  Stocks::find()->select('sum(quantity) as quantity')->where('id=' . $value->category)
                        ->andWhere('type = 1')->one();
                    $prices = Prices::find()->where('category = ' . $value->category)->one();
                    //============== calculate avarage of cost
                    $invetCostPrice = floatval($inventory->quantity) * floatval($prices->costPrice);
                    is_float($invetCostPrice);
                    
                    $tempCostPrice = floatval($value->quantity) * floatval($value->totalCost);
                    is_float($tempCostPrice);

                    $total_cost = 0;
                    if ($inventory->quantity <= 0) {
                        $total_cost = floatval($value->totalCost);
                    }elseif ($inventory->quantity > 0){
                        if ($invetCostPrice == 0) {
                            $total_cost = floatval($value->totalCost);
                        }else{
                            $total_cost = (floatval($tempCostPrice) + floatval($invetCostPrice))/(floatval($inventory->quantity + $value->quantity));
                        }
                    }
                    Prices::deleteAll(['category' => $value->category]);
                    $prices = new Prices();
                    $prices->category = $value->category;
                    $prices->costPrice = $total_cost;
                    $prices->minPrice = $value->salePrice;
                    $prices->minPrice2 = $value->salePrice_2;
                    $prices->minPrice3 = $value->salePrice_3;
                    $prices->maxPrice = $value->salePrice;
                    $prices->save();
                    // ================
                    $value->save(false);
                }
            }elseif ($model->type == 3) {
                $items =  PurchasesDetails::find()->where('PurchasesId = ' . $model->id)->all();
                foreach ($items  as $value) {
                    $exsit = Stocks::find()->where(['=', 'branch', Yii::$app->user->identity->branch])
                        ->andWhere(['=', 'category', $value->category])
                        ->andWhere(['=', 'type', $model->type])
                        ->one();
                    if ($exsit == null) {
                        $command = Yii::$app->db->createCommand("INSERT INTO stocks
                        (`category`, `quantity`, `branch`, `type`)
                        VALUES
                        (:category, :quantity, :branch, :type)");
                        $command->bindValue(':category', $value->category);
                        $command->bindValue(':quantity', $value->quantity);
                        $command->bindValue(':branch', Yii::$app->user->identity->branch);
                        $command->bindValue(':type', $model->type);
                        $command->execute();
                    }
                }
            }
            $model->update_at = date('Y-m-d H:i:s');
            $model->user_update = Yii::$app->user->id;
            $model->total = PurchasesDetails::find()->where(['PurchasesId' => $model->id])->sum('costPrice * quantity');
            $model->save(false);
            // ====================
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
            'totalCost' =>  $model->totalCost,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Deletes an existing Purchases model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $purchasesDetails = PurchasesDetails::find()->where(['PurchasesId' => $id])->all();
        $purchases = Purchases::find()->where(['id' => $id])->one();
        foreach ($purchasesDetails as $data) {
            if ($purchases->type == 1) {
                Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  - $data->quantity 
                 WHERE category=:category
                 and branch = :branch
                 and type = :type")
                    ->bindValue(':category', $data->category)
                    ->bindValue(':branch', Yii::$app->user->identity->branch)
                    ->bindValue(':type', 1)
                    ->execute();
            } elseif ($purchases->type == 3) {
                Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  - $data->quantity
                WHERE category=:category
                and branch = :branch
                and type = :type")
                    ->bindValue(':category', $data->category)
                    ->bindValue(':branch', Yii::$app->user->identity->branch)
                    ->bindValue(':type', 3)
                    ->execute();
            } elseif ($purchases->type == 2) {
                Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  + $data->quantity
                WHERE category=:category
                and branch = :branch
                and type = :type")
                    ->bindValue(':category', $data->category)
                    ->bindValue(':branch', Yii::$app->user->identity->branch)
                    ->bindValue(':type', 1)
                    ->execute();
            }
        }
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Purchases model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Purchases the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Purchases::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionCreate($totalInvoice)
    {
        $model = new Purchases();

        $total = TempInvoicePurchase::find()
        ->where('created_by	=' . Yii::$app->user->identity->id)
        ->andWhere('state =0')
        ->sum('costPrice*quantity');

        if ($total == 0) {
            Yii::$app->session->setFlash('error', Yii::t('app', "Sorry There is no Items at Invoice"));
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }
        if ($totalInvoice != 0) {
            if ($total != $totalInvoice) {
                Yii::$app->session->setFlash('error', Yii::t('app', "Sorry Total Invoice not matched"));
                return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
            }
        }
        $company_currancy = CompanyInfo::find()->one();

        if ($model->load(Yii::$app->request->post())) {
            if ($company_currancy->id == $model->currancy) {

                $model->total_currancy = $model->total;
            }

            $model->billId = Purchases::find()->where(['in', 'type', $model->type])->max('billId') + 1;
            $model->created_at = date('Y-m-d H:i:s');
            $model->user_insert = Yii::$app->user->id;
            $model->branch = Yii::$app->user->identity->branch;
            $model->BuyFor = 1;
            $model->standBy = 0;

            $model->save(false);
            //============== read last cost and quantity from inventory
            $items =  TempInvoicePurchase::find()->where('created_by = ' . Yii::$app->user->identity->id)
                ->andWhere('state = 0')
                ->all();
            //============== add to PurchasesDetails
            foreach ($items  as $value) {
                $PurchasesDetails = new PurchasesDetails();

                $inventory =  Stocks::find()->select('sum(quantity) as quantity')->where('category =' . $value->category)
                    ->andWhere('type=1')->one();

                $prices =  Prices::find()->where('category = ' . $value->category)->one();

                $PurchasesDetails->PurchasesId = $model->id;
                $PurchasesDetails->category = $value->category;
                $PurchasesDetails->quantity = $value->quantity;
                $PurchasesDetails->costPrice = $value->costPrice;
                $PurchasesDetails->salePrice = $value->salePrice;
                $PurchasesDetails->salePrice_ = $value->salePrice_;
                $PurchasesDetails->salePrice_2 = $value->salePrice_2;
                $PurchasesDetails->salePrice_3 = $value->salePrice_3;
                $PurchasesDetails->totalCost = $value->costTotal;
                $PurchasesDetails->box = 1;
                $PurchasesDetails->expire = null;
                $PurchasesDetails->save(false);

                $value->state = 1;
                $value->save(false);
                //calculate varage of cost
                if ($model->type == 1) {
                    $invetCostPrice = floatval($inventory->quantity) * floatval($prices->costPrice);
                    is_float($invetCostPrice);

                    $tempCostPrice = floatval($value->quantity) * floatval($value->costTotal);
                    is_float($tempCostPrice);
                    $total_cost = 0;
                    if ($inventory->quantity <= 0) {
                        $total_cost = floatval($value->costTotal);
                    } elseif ($inventory->quantity > 0) {
                        if ($invetCostPrice == 0) {
                            $total_cost = floatval($value->totalCost);
                        } else {
                            $total_cost = (floatval($tempCostPrice + $invetCostPrice)) / floatval($value->quantity + $inventory->quantity);
                        }
                    }

                    Prices::deleteAll(['category' => $value->category]);
                    $prices = new Prices();
                    $prices->category = $value->category;
                    $prices->costPrice = $total_cost;
                    $prices->minPrice = $value->salePrice_;
                    $prices->minPrice2 = $value->salePrice_2;
                    $prices->minPrice3 = $value->salePrice_3;
                    $prices->maxPrice = $value->salePrice;
                    $prices->save(false);
                }
                if ($model->type == 1) {
                     Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  + $value->quantity
                     WHERE category=:category
                     and branch = :branch
                     and type = :type")
                        ->bindValue(':category', $value->category)
                        ->bindValue(':branch', Yii::$app->user->identity->branch)
                        ->bindValue(':type', 1)
                        ->execute();
                } elseif ($model->type == 2) {

                    Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  - $value->quantity
                    WHERE category=:category
                    and branch = :branch
                    and type = :type")
                        ->bindValue(':category', $value->category)
                        ->bindValue(':branch', Yii::$app->user->identity->branch)
                        ->bindValue(':type', 1)
                        ->execute();
                } elseif ($model->type == 3) {
                    $exsit = Stocks::find()->where(['=', 'branch', Yii::$app->user->identity->branch])
                        ->andWhere(['=', 'category', $value->category])
                        ->andWhere(['=', 'type', 3])
                        ->one();
                    if ($exsit == null) {
                        Yii::$app->db->createCommand('INSERT INTO stocks(category, quantity, branch, type) VALUES
                        (:category, :quantity, :branch, :type)')
                            ->bindValues([':category' => $value->category])
                            ->bindValues([':quantity' => $value->quantity])
                            ->bindValues([':branch' => Yii::$app->user->identity->branch])
                            ->bindValues([':type' => 3])
                            ->execute();
                    } else {
                        Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  + $value->quantity
                    WHERE category=:category
                    and branch = :branch
                    and type = :type")
                            ->bindValue(':category', $value->category)
                            ->bindValue(':branch', Yii::$app->user->identity->branch)
                            ->bindValue(':type', 3)
                            ->execute();
                    }
                }
            }

            return $this->redirect(['view', 'id' => $model->id]);
        } elseif (Yii::$app->request->isAjax) {
            $model->at = date('Y-m-d');
            $model->clinet = 1;
            $model->payWay = 0;
            $model->total = TempInvoicePurchase::find()
            ->where('created_by	=' . Yii::$app->user->identity->id)
            ->andWhere('state =0')
            ->sum('costPrice*quantity');
            $totalInvoice =
                $model->paid = 0;
            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        } else {

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionRemove($id)
    {
        $details = PurchasesDetails::find()->where(['id' => $id])->one();
        $value = $details->quantity * $details->costPrice;
        $purchase = Purchases::find()->where(['id' => $details->PurchasesId])->one();
        $purchase->total = $purchase->total - $value;
        $purchase->save(false);
        PurchasesDetails::deleteAll(['id' => $id]);
        return $this->redirect(['update', 'id' => $details->PurchasesId]);
    }

    public function actionSaveAsNew($oldId)
    {
        $model = new Purchases();
        if ($model->load(Yii::$app->request->post())) {
            $model->billId = Purchases::find()->where(['in', 'type', $model->type])->max('billId') + 1;
            $model->created_at     = date('Y-m-d H:i:s');
            $model->user_insert = Yii::$app->user->id;
            $model->branch = Yii::$app->user->identity->branch;
            $model->BuyFor = 1;
            $model->standBy    = 0;
            $model->save(false);
            //============== read last cost and quantity from inventory
            $items =  PurchasesDetails::find()->where('PurchasesId =' . $oldId)->all();
            //============== add to PurchasesDetails
            foreach ($items  as $value) {
                $purchasesDetails = new PurchasesDetails();

                $inventory =  Inventory::find()->select('sum(quantity) as quantity')->where('id=' . $value->category)
                    ->andWhere('type=1')->one();
                $prices =  Prices::find()->where('category = ' . $value->category)->one();
                $purchasesDetails->PurchasesId = $model->id;
                $purchasesDetails->category = $value->category;
                $purchasesDetails->quantity = $value->quantity;
                $purchasesDetails->costPrice = $value->costPrice;
                $purchasesDetails->salePrice = $value->salePrice;
                $purchasesDetails->salePrice_ = $value->salePrice_;
                $purchasesDetails->salePrice_2 = $value->salePrice_2;
                $purchasesDetails->salePrice_3 = $value->salePrice_3;
                $purchasesDetails->totalCost = $value->totalCost;
                $purchasesDetails->box = 1;
                $purchasesDetails->expire = null;
                $purchasesDetails->save(false);
                $value->save(false);
                //calculate varage of cost
                if ($model->type == 1) {
                    $invetCostPrice = floatval($inventory->quantity) * floatval($prices->costPrice);
                    is_float($invetCostPrice);

                    $tempCostPrice = floatval($value->quantity) * floatval($value->totalCost);
                    is_float($tempCostPrice);

                    $total_cost = 0;
                    if ($inventory->quantity < 0) {
                        $total_cost = floatval($value->totalCost);
                    } else {
                        $total_cost = (floatval($tempCostPrice + $invetCostPrice) / floatval($value->quantity + $inventory->quantity));
                    }

                    Prices::deleteAll(['category' => $value->category]);
                    $prices = new Prices();
                    $prices->category = $value->category;
                    $prices->costPrice = $total_cost;
                    $prices->minPrice = $value->salePrice_;
                    $prices->minPrice2 = $value->salePrice_2;
                    $prices->minPrice3 = $value->salePrice_3;
                    $prices->maxPrice = $value->salePrice;
                    $prices->save(false);
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } elseif (Yii::$app->request->isAjax) {
            $model->at = date('Y-m-d');
            $model->clinet = 1;
            $model->payWay = 0;
            $model->total = PurchasesDetails::find()->where('PurchasesId =' . $oldId)->sum('costPrice*quantity');
            $model->paid = 0;
            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        } else {

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionTransferToTempInvoice($id)
    {
        $details = PurchasesDetails::find()->where(['id' => $id])->one();
        $purchasesId = $details->PurchasesId;
        $purchasesDetails = PurchasesDetails::find()->where(['id' => $id])->all();
        $sale = Purchases::find()->where(['id' => $details->PurchasesId])->one();

        foreach ($purchasesDetails as $data) {
            $modelTempInvoice = new TempInvoicePurchase();
            $pId = TempInvoicePurchase::find()->max('id') + 1;
            $modelTempInvoice->id = $pId;
            $modelTempInvoice->category =  $data->category;
            $modelTempInvoice->quantity = $data->quantity;
            $modelTempInvoice->costPrice = $data->costPrice;
            $modelTempInvoice->costTotal = $data->totalCost;
            $modelTempInvoice->box = $data->box;
            $modelTempInvoice->expire = $data->expire;
            $modelTempInvoice->state = 0;
            $modelTempInvoice->salePrice = $data->salePrice;
            $modelTempInvoice->salePrice_ = $data->salePrice_;
            $modelTempInvoice->salePrice_2 = $data->salePrice_2;
            $modelTempInvoice->salePrice_3 = $data->salePrice_3;
            $modelTempInvoice->save(false);
            PurchasesDetails::deleteAll(['id' => $id]);
            if ($sale->type == 1) {
                Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  - $data->quantity
                WHERE category=:category
                and branch = :branch
                and type = :type")
                    ->bindValue(':category', $data->category)
                    ->bindValue(':branch', Yii::$app->user->identity->branch)
                    ->bindValue(':type', 1)
                    ->execute();
            } elseif ($sale->type == 3) {
                Yii::$app->db->createCommand("UPDATE  stocks  SET  quantity =  quantity  - $data->quantity
                WHERE category=:category
                and branch = :branch
                and type = :type")
                    ->bindValue(':category', $data->category)
                    ->bindValue(':branch', Yii::$app->user->identity->branch)
                    ->bindValue(':type', 3)
                    ->execute();
            }

            $sale->total =  PurchasesDetails::find()
            ->where(['PurchasesId' => $details->purchasesId])
            ->sum('costPrice * quantity');

            if ($sale->total == null) {
                Purchases::deleteAll(['id' => $purchasesId]);
                return $this->redirect(['temp-invoice-purchase/create']);
            } else {
                $sale->save(false);
                return $this->redirect(['update', 'id' => $purchasesId]);
            }
        }

        return $this->redirect(['update', 'id' => $purchasesId]);
    }

    public function actionDateOfArrival()
    {
        if (Yii::$app->user->identity->client == NULL) {
            $sql = " SELECT max(purchases.billId) as billId, max(purchases.at) as at, max(purchases.total) total
             , max(purchases.paid) as paid, max(purchases.dateOfArrival) as dateOfArrival
            , max(client.name) as clientName, sum(dept.credt) as credt, max(purchases.id) as id
            , max(purchases.clinet) as client FROM `purchases`
            LEFT JOIN `client` ON client.id = purchases.clinet
            LEFT JOIN `dept` ON client.id = dept.id
            WHERE purchases.dateOfArrival - CURRENT_DATE() <= 0 and purchases.type=3
            GROUP BY `purchases`.`id`, `purchases`.`dateOfArrival`";
        } else {
            $sql = " SELECT max(purchases.billId) as billId, max(purchases.at) as at, max(purchases.total) total
             , max(purchases.paid) as paid, max(purchases.dateOfArrival) as dateOfArrival
            , max(client.name) as clientName, sum(dept.credt) as credt, max(purchases.id) as id
            , max(purchases.clinet) as client FROM `purchases`
            LEFT JOIN `client` ON client.id = purchases.clinet
            LEFT JOIN `dept` ON client.id = dept.id
            WHERE purchases.dateOfArrival - CURRENT_DATE() <= 4 and purchases.type=3
            and purchases.clinet in(" . Yii::$app->user->identity->client . ")
            GROUP BY `purchases`.`id`, `purchases`.`dateOfArrival`";
        }


        $connection = Yii::$app->db;
        $data = $connection->createCommand($sql);
        $info = $data->queryAll();
        return $this->render('dateOfArrival', [
            'models' => $info,
            'count' => 0,
        ]);
    }

    public function actionNoprice($id)
    {
        $sql = "select purchases.id, purchases.at, purchases.payWay, purchases.total, purchases.paid, 
        purchases.type, purchases.billId, purchases.notes,
                purchasesDetails.quantity, purchasesDetails.costPrice, purchasesDetails.salePrice,
                Totalinventory.quantity as Qtotalinventory,
                client.name as client, client.mobile,
                category.name as category, category.serialNo, category.company, category.commCode,
                category_reservation.quantity as reservation
                FROM purchases
                JOIN purchasesDetails on purchases.id = purchasesDetails.purchasesId
                JOIN Totalinventory on purchasesDetails.category = Totalinventory.id
                JOIN client on purchases.clinet = client.id
                JOIN category on category.id = purchasesDetails.category
                left JOIN category_reservation on purchasesDetails.category = category_reservation.category
                where purchases.id = " . $id . " and Totalinventory.branch = " . Yii::$app->user->identity->branch . " and Totalinventory.type <> 3 
                ";
        $connection = Yii::$app->db;
        $data = $connection->createCommand($sql);
        $info = $data->queryAll();
        return $this->render('printBill_', [
            'infos' => $info,
            'models' => $info,
        ]);
    }
}
