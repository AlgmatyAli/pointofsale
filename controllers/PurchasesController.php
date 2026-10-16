<?php

namespace app\controllers;

use app\models\base\TempTransferItems;
use app\models\CompanyInfo;
use app\models\Client;
use app\models\Dept;
use app\models\DeptSupp;
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
use app\models\TempTransferItemsSearch;
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
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'remove' => ['POST'],
                    'transfer-to-temp-invoice' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'create',
                            'view',
                            'print-bill',
                            'print-bill-with-out-price',
                            'print-d-bill',
                            'print-bill-with-place',
                            'remove',
                            'add-purchases',
                            'date-of-arrival',
                            'noprice'
                        ],
                        'roles' => ['createPurchases'],
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'update',
                            'view',
                            'print-bill',
                            'print-bill-with-out-price',
                            'print-bill-with-place',
                            'remove',
                            'transfer-to-temp-invoice',
                            'add-purchases',
                            'date-of-arrival',
                            'noprice',
                            'add-to-transfer-items'
                        ],
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
                    [
                        'allow' => true,
                        'actions' => ['purchases-details'],
                        'roles' => ['prices'],
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
        $searchModel = new PurchasesDetailsSearch();
        $searchModel->PurchasesId = $id;

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;

        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPrintBill($id)
    {
        $this->layout = 'invoice';
        if (Yii::$app->user->identity->printPurtchaseInvoice == 1) {
            $model = $this->findModel($id);
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


            if (($model->c->type ?? null) == 0 || ($model->c->type ?? null) == 2) {
                $balance = Dept::find()->where(['id' => $model->clinet])->andWhere(['in', 'currency', ['0', $model->currancy]])->sum('credt');
            } else {
                $balance = DeptSupp::find()->where(['id' => $model->clinet])->andWhere(['in', 'currency', ['0', $model->currancy]])->sum('credt');
            }
            $company = CompanyInfo::find()->one();
            return $this->render('printBill', [
                'model' => $model,
                'dataProvider' => $dataProvider,
                'balance' => $balance,
                'company' => $company,
            ]);
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', "Sorry You Do Not Have Permession to Print Invoice"));
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }
    }

    public function actionPrintBillWithOutPrice($id)
    {
        $this->layout = 'invoice';
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
        $this->layout = 'invoice';
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

        if (yii::$app->request->post('hasEditable')) {
            $key = Yii::$app->request->post('editableKey');
            // السطر يجب أن يتبع هذه الفاتورة
            $result = PurchasesDetails::findOne(['id' => $key, 'PurchasesId' => $model->id]);
            if ($result === null) {
                return Json::encode(['output' => '', 'message' => 'السطر غير موجود']);
            }
            $this->requireTypePermission($model->type, 'update');

            $posted = current((array)Yii::$app->request->post('PurchasesDetails', []));
            $posted = array_intersect_key((array)$posted, array_flip(['quantity', 'salePrice', 'salePrice_', 'costPrice', 'totalCost']));
            if (!$posted) {
                return Json::encode(['output' => '', 'message' => 'حقل غير مسموح بتعديله']);
            }

            $oldQuantity = (float)$result->quantity;
            $result->load(['PurchasesDetails' => $posted]);
            if (!$result->validate(array_keys($posted))) {
                return Json::encode(['output' => '', 'message' => implode(' ', $result->getErrorSummary(true))]);
            }
            $attr = key($posted);

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $result->save(false);
                // تغيير الكمية يغيّر المخزون بالفرق فقط (كانت triggers قاعدة البيانات تفعل ذلك)
                if (isset($posted['quantity'])) {
                    [$stockType, $signed] = $this->lineEffect($model->type, $result->quantity - $oldQuantity);
                    $this->changeStock($result->category, $model->branch, $stockType, $signed, $signed > 0);
                }
                if (isset($posted['costPrice']) || isset($posted['totalCost'])) {
                    $model->total = $this->detailsTotal($result->PurchasesId);
                    $model->save(false);
                }
                $transaction->commit();
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage(), __METHOD__);
                return Json::encode(['output' => '', 'message' => 'تعذّر الحفظ: ' . $e->getMessage()]);
            }
            return Json::encode(['output' => $result->$attr]);
        }
        if ($model->load(Yii::$app->request->post())) {
            $this->requireTypePermission($oldType, 'update');

            // التحويل الوحيد المسموح بين الأنواع: من «معلقة» (3) إلى «مشتريات» (1)؛ غير ذلك يفسد المخزون
            if ($model->type != $oldType && !($oldType == 3 && $model->type == 1)) {
                Yii::$app->session->setFlash('error', 'لا يمكن تغيير نوع الفاتورة إلى النوع المختار');
                $model->type = $oldType;
                return $this->redirect(['update', 'id' => $model->id]);
            }
            if (!$model->validate(['clinet', 'at', 'payWay', 'paid', 'payment_type', 'shippingType', 'currancy', 'total_currancy', 'type', 'notes'])
                || !$this->checkSupplier($model)) {
                return $this->render('update', [
                    'model' => $model,
                    'totalCost' => $model->totalCost,
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]);
            }
            $stockBranch = $model->branch;   // فرع الفاتورة (كان فرع المستخدم الحالي)

            $transaction = Yii::$app->db->beginTransaction();
            try {
            // متوسط التكلفة والأسعار يُحسبان مرة واحدة فقط، عند تأكيد فاتورة معلقة (3 → 1).
            // حفظ فاتورة مشتريات (1) موجودة أصلاً لا يعيد الحساب: كميتها داخل المخزون سلفاً فكانت تُحسب مرتين.
            if ($oldType == 3 && $model->type == 1) {
                if ($model->totalCost != 0 && $model->total != 0) {   // (total = 0 كانت تسبب قسمة على صفر)
                    $rate = ($model->total + $model->totalCost) / $model->total;
                    $items =  PurchasesDetails::find()->where(['PurchasesId' => $model->id])->all();
                    foreach ($items  as $value) {
                        $value->totalCost = ($value->costPrice * $rate);
                        $value->save(false);
                    }
                }
                //============== read last cost and quantity from inventory
                $items =  PurchasesDetails::find()->where(['PurchasesId' => $model->id])->all();
                //============== add to PurchasesDetails
                foreach ($items  as $value) {
                    // ====================
                    $inventory =  Stocks::find()->select('sum(quantity) as quantity')->where(['category' => $value->category])
                        ->andWhere(['type' => 1])->one();
                    $prices = Prices::find()->where(['category' => $value->category])->one();
                    //============== calculate avarage of cost
                    $invetCostPrice = floatval($inventory->quantity) * floatval($prices?->costPrice);
                    is_float($invetCostPrice);

                    $tempCostPrice = floatval($value->quantity) * floatval($value->totalCost);
                    is_float($tempCostPrice);

                    $total_cost = 0;
                    if ($inventory->quantity <= 0) {
                        $total_cost = floatval($value->totalCost);
                    } elseif ($inventory->quantity > 0) {
                        if ($invetCostPrice == 0) {
                            $total_cost = floatval($value->totalCost);
                        } else {
                            $total_cost = (floatval($tempCostPrice) + floatval($invetCostPrice)) / (floatval($inventory->quantity + $value->quantity));
                        }
                    }
                    // عند تأكيد فاتورة معلقة (3 → 1) تُحدَّث أسعار البيع ومتوسط التكلفة دائماً (بغض النظر عن مفتاح changeSalePrice)
                    Prices::deleteAll(['category' => $value->category]);
                    $prices = new Prices();
                    $prices->category = $value->category;
                    $prices->costPrice = $total_cost;
                    $prices->minPrice =  $value->salePrice_;
                    $prices->minPrice2 = $value->salePrice_2;
                    $prices->minPrice3 = $value->salePrice_3;
                    $prices->maxPrice = $value->salePrice;
                    $prices->save();
                    // ================
                    $value->save(false);
                }
            } elseif ($oldType == 1 && $model->type == 1 && $model->changeSalePrice == 1) {
                // فاتورة مشتريات عادية: تُحدَّث أسعار البيع فقط إن كان مفتاح «تعديل سعر البيع» مفعّلاً، وبدون لمس التكلفة
                foreach (PurchasesDetails::find()->where(['PurchasesId' => $model->id])->all() as $value) {
                    $prices = Prices::find()->where(['category' => $value->category])->one();
                    if ($prices === null) {
                        $prices = new Prices();
                        $prices->category = $value->category;
                        $prices->costPrice = floatval($value->totalCost);
                    }
                    $prices->minPrice = $value->salePrice_;
                    $prices->minPrice2 = $value->salePrice_2;
                    $prices->minPrice3 = $value->salePrice_3;
                    $prices->maxPrice = $value->salePrice;
                    $prices->save(false);
                }
            }

            // المخزون عند تأكيد فاتورة معلقة: ينتقل من مخزون «معلقة» (3) إلى مخزون «مشتريات» (1) (كانت triggers قاعدة البيانات تفعل ذلك).
            // يُنفَّذ بعد حساب المتوسط أعلاه ليبقى الحساب كما كان: المتوسط يُحسب قبل إضافة كمية هذه الفاتورة للمخزون.
            if ($oldType == 3 && $model->type == 1) {
                foreach (PurchasesDetails::find()->where(['PurchasesId' => $model->id])->all() as $line) {
                    $this->changeStock($line->category, $stockBranch, 3, -$line->quantity, false);
                    $this->changeStock($line->category, $stockBranch, 1, $line->quantity, true);
                }
            }
            $model->update_at = date('Y-m-d H:i:s');
            $model->user_update = Yii::$app->user->id;
            $model->total = $this->detailsTotal($model->id);
            $model->save(false);
            $transaction->commit();
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::error($e->getMessage() . "\n" . $e->getTraceAsString(), __METHOD__);
                Yii::$app->session->setFlash('error', 'تعذّر حفظ التعديلات ولم يتغير شيء: ' . $e->getMessage());
                return $this->redirect(['update', 'id' => $id]);
            }
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
        $purchases = $this->findModel($id);
        $this->requireTypePermission($purchases->type, 'delete');
        $purchasesDetails = PurchasesDetails::find()->where(['PurchasesId' => $id])->all();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // يُعكس المخزون على فرع الفاتورة نفسها (كان يُعكس على فرع المستخدم الحالي)
            foreach ($purchasesDetails as $data) {
                $this->reverseLineStock($purchases, $data);
            }
            PurchasesDetails::deleteAll(['PurchasesId' => $id]);
            $purchases->delete();
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return $this->redirect(['index']);
    }

    private function hasPerm($name)
    {
        $auth = Yii::$app->authManager;
        if ($auth === null || ($auth->getPermission($name) === null && $auth->getRole($name) === null)) {
            return true;
        }
        return Yii::$app->user->can($name);
    }

    /** فاتورة مسترجع المشتريات (type = 2) لها صلاحياتها الخاصة: createBackPurchase / updateBackPurchase / deleteBackPurchase */
    private function requireTypePermission($type, $op)
    {
        if ((int)$type === 2 && !$this->hasPerm($op . 'BackPurchase')) {
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', 'You are not allowed to access this page'));
        }
    }

    /**
     * تفعيل ضبط «عدم نزول المخزون عن صفر». كانت triggers قاعدة البيانات القديمة تفعل شيئاً مشابهاً (UPDATE ... SET quantity = 0 WHERE ...).
     * اضبطه true إن أردت ذلك، وبقي false لأنني لم أر نص الـ triggers المحذوفة.
     */
    const CLAMP_NEGATIVE_STOCK = false;

    /** أثر سطر فاتورة مشتريات على المخزون: [نوع المخزون، الكمية الموقّعة]. المسترجع ينقص من مخزون النوع 1. */
    private function lineEffect($invoiceType, $qty)
    {
        switch ((int)$invoiceType) {
            case 1:
                return [1, (float)$qty];
            case 2:
                return [1, -(float)$qty];
            case 3:
                return [3, (float)$qty];
        }
        return [null, 0.0];
    }

    /**
     * تغيير مخزون صنف بمقدار موقّع (موجب = زيادة، سالب = نقص).
     * $createIfMissing: ينشئ السجل إن لم يوجد؛ يُستعمل مع الزيادات فقط.
     * (حلّ هذا محل triggers قاعدة البيانات التي كانت تعدّل المخزون عند تعديل أو حذف الفواتير)
     */
    private function changeStock($category, $branch, $stockType, $delta, $createIfMissing = true)
    {
        if ($stockType === null || (float)$delta == 0.0) {
            return;
        }
        $db = Yii::$app->db;
        $exists = Stocks::find()->where(['branch' => $branch, 'category' => $category, 'type' => $stockType])->exists();
        if ($exists) {
            $db->createCommand(
                'UPDATE stocks SET quantity = quantity + :d WHERE category = :category AND branch = :branch AND type = :type',
                [':d' => $delta, ':category' => $category, ':branch' => $branch, ':type' => $stockType]
            )->execute();
            if (self::CLAMP_NEGATIVE_STOCK) {
                $db->createCommand(
                    'UPDATE stocks SET quantity = 0 WHERE quantity < 0 AND category = :category AND branch = :branch AND type = :type',
                    [':category' => $category, ':branch' => $branch, ':type' => $stockType]
                )->execute();
            }
        } elseif ($createIfMissing) {
            $db->createCommand()->insert('stocks', [
                'category' => $category,
                'quantity' => $delta,
                'branch' => $branch,
                'type' => $stockType,
            ])->execute();
        }
    }

    /** عكس أثر سطر فاتورة على المخزون (عند حذفه أو حذف الفاتورة أو نقله للمؤقت) */
    private function reverseLineStock(Purchases $purchase, PurchasesDetails $line)
    {
        [$stockType, $signed] = $this->lineEffect($purchase->type, $line->quantity);
        $this->changeStock($line->category, $purchase->branch, $stockType, -$signed, -$signed > 0);
    }

    private function acquireLock($name)
    {
        return (int)Yii::$app->db->createCommand('SELECT GET_LOCK(:n, 10)', [':n' => $name])->queryScalar() === 1;
    }

    private function releaseLock($name)
    {
        Yii::$app->db->createCommand('SELECT RELEASE_LOCK(:n)', [':n' => $name])->execute();
    }

    /** مجموع (سعر التكلفة × الكمية) لأسطر الفاتورة: نفس معادلة الحفظ والتعديل */
    private function detailsTotal($purchasesId)
    {
        return (float)PurchasesDetails::find()->where(['PurchasesId' => $purchasesId])->sum('costPrice * quantity');
    }

    /**
     * تغيير كمية الصنف في المخزن. إن لم يوجد سجل مخزون يُنشأ بكمية $insertQty، وإلا تُضاف $delta (موجبة أو سالبة).
     * كل القيم تمرّر كمعاملات (بدل دمجها في نص SQL).
     */
    private function applyStock($category, $branch, $stockType, $delta, $insertQty)
    {
        $db = Yii::$app->db;
        $exists = Stocks::find()->where(['branch' => $branch, 'category' => $category, 'type' => $stockType])->exists();
        if (!$exists) {
            $db->createCommand()->insert('stocks', [
                'category' => $category,
                'quantity' => $insertQty,
                'branch' => $branch,
                'type' => $stockType,
            ])->execute();
        } else {
            $db->createCommand(
                'UPDATE stocks SET quantity = quantity + :d WHERE category = :category AND branch = :branch AND type = :type',
                [':d' => $delta, ':category' => $category, ':branch' => $branch, ':type' => $stockType]
            )->execute();
        }
    }





    private function nextBillId($type)
    {
        return (int)Purchases::find()->where(['type' => $type])->max('billId') + 1;
    }

    /** حقول رأس الفاتورة التي يحدّدها الخادم لا المتصفح */
    private function stampNewInvoice(Purchases $model)
    {
        $model->created_at  = date('Y-m-d H:i:s');
        $model->user_insert = Yii::$app->user->id;
        $model->branch      = Yii::$app->user->identity->branch;
        $model->BuyFor      = 1;
        $model->standBy     = 0;
    }

    /** المورد يجب أن يكون من نوع مورد (1) أو زبون ومورد (2) */
    private function checkSupplier(Purchases $model)
    {
        if ($model->clinet && !Client::find()->where(['id' => $model->clinet, 'type' => [1, 2]])->exists()) {
            $model->addError('clinet', 'المورد المختار غير صالح');
            return false;
        }
        return true;
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

    public function actionCreate($totalInvoice = 0)
    {
        $model = new Purchases();
        $userId = Yii::$app->user->id;
        $branch = Yii::$app->user->identity->branch;

        // أسطر الفاتورة المؤقتة للمستخدم الحالي
        $tempQuery = function () use ($userId) {
            return TempInvoicePurchase::find()->where(['created_by' => $userId, 'state' => 0]);
        };
        $total = $tempQuery()->sum('costPrice*quantity');

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
            $this->requireTypePermission($model->type, 'create');

            // قفل قصير: يمنع تكرار رقم الفاتورة، ويمنع حفظ الفاتورة مرتين عند النقر المزدوج
            $lockName = 'purchases_create_' . (int)$model->type;
            if (!$this->acquireLock($lockName)) {
                Yii::$app->session->setFlash('error', 'النظام مشغول حالياً، الرجاء إعادة المحاولة');
                return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
            }

            try {
                // إعادة القراءة داخل القفل: إن حُفظت الفاتورة للتو من طلب آخر فلن تبقى أسطر
                $total = $tempQuery()->sum('costPrice*quantity');
                if ($total == 0) {
                    Yii::$app->session->setFlash('error', Yii::t('app', "Sorry There is no Items at Invoice"));
                    return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
                }

                // إجمالي الفاتورة يحسبه الخادم من الأسطر (كان يُؤخذ من الحقل القادم من المتصفح)
                $model->total = $total;
                if ($company_currancy !== null && $company_currancy->id == $model->currancy) {
                    $model->total_currancy = $model->total;
                }

                $model->billId = $this->nextBillId($model->type);
                $this->stampNewInvoice($model);

                $valid = $model->validate();
                $valid = $this->checkSupplier($model) && $valid;
                if (!$valid) {
                    Yii::$app->session->setFlash('error', 'لم تُحفظ الفاتورة: ' . implode(' | ', $model->getErrorSummary(true)));
                    return $this->render('create', ['model' => $model]);
                }

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $model->save(false);
                    //============== read last cost and quantity from inventory
                    $items = $tempQuery()->all();
                    //============== add to PurchasesDetails
                    foreach ($items as $value) {
                        $PurchasesDetails = new PurchasesDetails();

                        $inventory = Stocks::find()->select('sum(quantity) as quantity')
                            ->where(['category' => $value->category])
                            ->andWhere(['type' => 1])->one();

                        $prices = Prices::find()->where(['category' => $value->category])->one();

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
                            $invetCostPrice = floatval($inventory->quantity) * floatval($prices?->costPrice);
                            is_float($invetCostPrice);

                            $tempCostPrice = floatval($value->quantity) * floatval($value->costTotal);
                            is_float($tempCostPrice);
                            $total_cost = 0;
                            if ($inventory->quantity <= 0) {
                                $total_cost = floatval($value->costTotal);
                            } elseif ($inventory->quantity > 0) {
                                if ($invetCostPrice == 0) {
                                    // انتبه: هنا يُقرأ totalCost (خاصية افتراضية فارغة في TempInvoicePurchase) وليس costTotal. الحساب لم يُغيَّر عمداً.
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
                            $this->applyStock($value->category, $branch, 1, $value->quantity, $value->quantity);
                        } elseif ($model->type == 2) {
                            // مسترجع: ينقص من المخزن (وإن لم يوجد سجل يُنشأ بنفس سلوك الكود الأصلي)
                            $this->applyStock($value->category, $branch, 1, -$value->quantity, $value->quantity);
                        } elseif ($model->type == 3) {
                            $this->applyStock($value->category, $branch, 3, $value->quantity, $value->quantity);
                        }
                    }
                    $transaction->commit();
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    Yii::error($e->getMessage() . "\n" . $e->getTraceAsString(), __METHOD__);
                    Yii::$app->session->setFlash('error', 'تعذّر حفظ الفاتورة ولم يُسجَّل شيء: ' . $e->getMessage());
                    $model->isNewRecord = true;
                    $model->id = null;
                    return $this->render('create', ['model' => $model]);
                }
            } finally {
                $this->releaseLock($lockName);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        } elseif (Yii::$app->request->isAjax) {
            $model->at = date('Y-m-d');
            $model->clinet = 1;
            $model->payWay = 0;
            $model->total = $total;
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
        $details = PurchasesDetails::findOne($id);
        if ($details === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $purchase = $this->findModel($details->PurchasesId);
        $this->requireTypePermission($purchase->type, 'update');

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->reverseLineStock($purchase, $details);
            PurchasesDetails::deleteAll(['id' => $id]);
            // إجمالي الفاتورة يُعاد حسابه من الأسطر المتبقية (كان يُطرح منه سعر السطر)
            $purchase->total = $this->detailsTotal($purchase->id);
            $purchase->save(false);
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return $this->redirect(['update', 'id' => $details->PurchasesId]);
    }

    public function actionSaveAsNew($oldId)
    {
        $oldId = (int)$oldId;
        $model = new Purchases();
        if ($model->load(Yii::$app->request->post())) {
            $this->requireTypePermission($model->type, 'create');

            $lockName = 'purchases_create_' . (int)$model->type;
            if (!$this->acquireLock($lockName)) {
                Yii::$app->session->setFlash('error', 'النظام مشغول حالياً، الرجاء إعادة المحاولة');
                return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
            }

            try {
                $items = PurchasesDetails::find()->where(['PurchasesId' => $oldId])->all();
                if (!$items) {
                    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
                }

                $model->total = $this->detailsTotal($oldId);
                $model->billId = $this->nextBillId($model->type);
                $this->stampNewInvoice($model);

                $valid = $model->validate();
                $valid = $this->checkSupplier($model) && $valid;
                if (!$valid) {
                    Yii::$app->session->setFlash('error', 'لم تُحفظ الفاتورة: ' . implode(' | ', $model->getErrorSummary(true)));
                    return $this->render('create', ['model' => $model]);
                }

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $model->save(false);
                    //============== add to PurchasesDetails
                    foreach ($items  as $value) {
                        $purchasesDetails = new PurchasesDetails();

                        $inventory =  Inventory::find()->select('sum(quantity) as quantity')
                            ->where(['id' => $value->category])
                            ->andWhere(['type' => 1])->one();
                        $prices =  Prices::find()->where(['category' => $value->category])->one();
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
                        //calculate varage of cost
                        if ($model->type == 1) {
                            $invetCostPrice = floatval($inventory?->quantity) * floatval($prices?->costPrice);
                            is_float($invetCostPrice);

                            $tempCostPrice = floatval($value->quantity) * floatval($value->totalCost);
                            is_float($tempCostPrice);

                            $total_cost = 0;
                            $denominator = floatval($value->quantity + $inventory?->quantity);
                            if ($inventory?->quantity < 0 || $denominator == 0) {
                                // (القسمة على صفر كانت تُسقط الصفحة بخطأ؛ تُستعمل تكلفة السطر)
                                $total_cost = floatval($value->totalCost);
                            } else {
                                $total_cost = (floatval($tempCostPrice + $invetCostPrice) / $denominator);
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

                        // المخزون بنفس قواعد actionCreate (يُنفَّذ بعد حساب المتوسط ليبقى حسابه على المخزون قبل هذه الفاتورة)
                        if ($model->type == 1) {
                            $this->applyStock($value->category, $model->branch, 1, $value->quantity, $value->quantity);
                        } elseif ($model->type == 2) {
                            // مسترجع: ينقص من المخزن (وإن لم يوجد سجل يُنشأ بنفس سلوك actionCreate)
                            $this->applyStock($value->category, $model->branch, 1, -$value->quantity, $value->quantity);
                        } elseif ($model->type == 3) {
                            $this->applyStock($value->category, $model->branch, 3, $value->quantity, $value->quantity);
                        }
                    }
                    $transaction->commit();
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    Yii::error($e->getMessage() . "\n" . $e->getTraceAsString(), __METHOD__);
                    Yii::$app->session->setFlash('error', 'تعذّر حفظ الفاتورة ولم يُسجَّل شيء: ' . $e->getMessage());
                    $model->isNewRecord = true;
                    $model->id = null;
                    return $this->render('create', ['model' => $model]);
                }
            } finally {
                $this->releaseLock($lockName);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } elseif (Yii::$app->request->isAjax) {
            $model->at = date('Y-m-d');
            $model->clinet = 1;
            $model->payWay = 0;
            $model->total = $this->detailsTotal($oldId);
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
        $details = PurchasesDetails::findOne($id);
        if ($details === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $purchasesId = $details->PurchasesId;
        $sale = $this->findModel($purchasesId);
        $this->requireTypePermission($sale->type, 'update');

        // النقل إلى الفاتورة المؤقتة مسموح للفواتير المعلقة (3) فقط
        if ((int)$sale->type !== 3) {
            Yii::$app->session->setFlash('error', 'نقل الصنف إلى الفاتورة المؤقتة متاح للفواتير المعلقة فقط');
            return $this->redirect(['update', 'id' => $purchasesId]);
        }

        $lockName = 'temp_invoice_purchase_id';
        if (!$this->acquireLock($lockName)) {
            Yii::$app->session->setFlash('error', 'النظام مشغول حالياً، الرجاء إعادة المحاولة');
            return $this->redirect(['update', 'id' => $purchasesId]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $modelTempInvoice = new TempInvoicePurchase();
            $modelTempInvoice->id = (int)TempInvoicePurchase::find()->max('id') + 1;
            $modelTempInvoice->category = $details->category;
            $modelTempInvoice->quantity = $details->quantity;
            $modelTempInvoice->costPrice = $details->costPrice;
            $modelTempInvoice->costTotal = $details->totalCost;
            $modelTempInvoice->box = $details->box;
            $modelTempInvoice->expire = $details->expire;
            $modelTempInvoice->state = 0;
            $modelTempInvoice->salePrice = $details->salePrice;
            $modelTempInvoice->salePrice_ = $details->salePrice_;
            $modelTempInvoice->salePrice_2 = $details->salePrice_2;
            $modelTempInvoice->salePrice_3 = $details->salePrice_3;
            $modelTempInvoice->save(false);
            PurchasesDetails::deleteAll(['id' => $id]);

            $this->reverseLineStock($sale, $details);

            // كان الكود يقرأ $details->purchasesId (بحرف صغير) وهو غير موجود فينتج خطأ
            $sale->total = $this->detailsTotal($purchasesId);

            if (!PurchasesDetails::find()->where(['PurchasesId' => $purchasesId])->exists()) {
                Purchases::deleteAll(['id' => $purchasesId]);
                $transaction->commit();
                return $this->redirect(['temp-invoice-purchase/create']);
            }
            $sale->save(false);
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        } finally {
            $this->releaseLock($lockName);
        }

        return $this->redirect(['update', 'id' => $purchasesId]);
    }

    public function actionDateOfArrival()
    {
        // قائمة العملاء المسموحة للمستخدم تُحوَّل إلى أرقام صحيحة قبل دمجها في الاستعلام
        $allowedClients = implode(',', array_filter(array_map('intval', explode(',', (string)Yii::$app->user->identity->client))));
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
            and purchases.clinet in(" . ($allowedClients ?: '0') . ")
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
                where purchases.id = :id and Totalinventory.branch = :branch and Totalinventory.type <> 3 
                ";
        $connection = Yii::$app->db;
        $data = $connection->createCommand($sql, [':id' => (int)$id, ':branch' => (int)Yii::$app->user->identity->branch]);
        $info = $data->queryAll();
        return $this->render('printBill_', [
            'infos' => $info,
            'models' => $info,
        ]);
    }

    public function actionPurchasesDetails($id)
    {
        if (yii::$app->request->post('hasEditable')) {
            $key = Yii::$app->request->post('editableKey');
            $result = PurchasesDetails::findOne(['id' => $key, 'PurchasesId' => (int)$id]);
            if ($result === null) {
                return Json::encode(['output' => '', 'message' => 'السطر غير موجود']);
            }

            $posted = current((array)Yii::$app->request->post('PurchasesDetails', []));
            // المسموح تعديله من هذه الشاشة: سعرا البيع فقط
            $posted = array_intersect_key((array)$posted, array_flip(['salePrice', 'salePrice_']));
            if (!$posted) {
                return Json::encode(['output' => '', 'message' => 'حقل غير مسموح بتعديله']);
            }

            $result->load(['PurchasesDetails' => $posted]);
            if (!$result->validate(array_keys($posted))) {
                return Json::encode(['output' => '', 'message' => implode(' ', $result->getErrorSummary(true))]);
            }
            $attr = key($posted);
            $result->save(false);
            return Json::encode(['output' => $result->$attr]);
        }

        $searchModel = new PurchasesDetailsSearch();
        $searchModel->PurchasesId = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('_form_update_details', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPrintDBill($id)
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

            return $this->render('printDBill', [
                'model' => $this->findModel($id),
                'dataProvider' => $dataProvider
            ]);
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', "Sorry You Do Not Have Permession to Print Invoice"));
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }
    }

    public function actionAddToTransferItems($id)
    {
        $model = new Purchases();
        $dataProvider = new ActiveDataProvider([
            'query' => PurchasesDetails::find()
                ->select(
                    'purchasesDetails.category as id, purchasesDetails.salePrice, purchasesDetails.salePrice_, purchasesDetails.quantity, 
                    category.name as name, purchasesDetails.totalCost, category.serialNo, category.company'
                )
                ->leftJoin('category', 'category.id = purchasesDetails.category')
                ->where(['purchasesDetails.PurchasesId' => $id]),
            'sort' => [
                'defaultOrder' => [
                    'purchasesDetails.id' => SORT_DESC
                ]
            ],
            'pagination' => ['pageSize' => 70],
        ]);


        if ($model->load(Yii::$app->request->post())) {
            $textInputValues = Yii::$app->request->post('PurchaseInvoice')['textInputValues'];
            if (!empty($textInputValues)) {
                foreach ($textInputValues as $category => $textInputValue) {
                    if (Yii::$app->request->post('selection') && in_array($category, Yii::$app->request->post('selection'))) {
                        if (is_numeric($textInputValue) && $textInputValue > 0) {
                            $id = TempTransferItems::find()->max('id') + 1;
                            $newModel = new TempTransferItems();
                            $newModel->id = $id;
                            $newModel->category = $category;
                            $newModel->quantity = $textInputValue;
                            $newModel->created_by = Yii::$app->user->identity->id;
                            $newModel->created_at = date('Y-m-d');
                            $newModel->save(false);
                        }
                    }
                }
            }
            $searchModel = new TempTransferItemsSearch();
            $searchModel->created_by = Yii::$app->user->identity->id;
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            return $this->redirect([
                'temp-transfer-items/create',
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }

        return $this->renderAjax('createFast', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }
}
