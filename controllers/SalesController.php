<?php

namespace app\controllers;

use Yii;
use app\models\Sales;
use app\models\SalesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;
use app\models\SalesDetails;
use yii\helpers\Json;
use app\models\Ftran;
use app\models\TempInvoice;
use app\models\CompanyInfo;
use app\models\Totalinventory;
use app\models\Dept;
use app\models\Client;
use app\models\Category;
use app\models\Prices;
use app\models\SalesDetailsSearchWQ;
use app\models\SalesDetailsSearchWQClient;
use app\models\Stocks;
use app\models\TempBackSales;
use Mpdf\Mpdf; #Php 7.0
use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\web\Response;


/**
 * SalesController implements the CRUD actions for Sales model.
 */
class SalesController extends Controller
{
    use \app\components\SalesStockTrait;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'remove' => ['POST'],
                    'transfer-to-temp-invoice' => ['POST'],
                    'pdf' => ['POST'],
                    'delev' => ['POST'],
                    'stop-credit' => ['POST'],
                    'add-item' => ['POST'],
                    'back-items' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'view', 'print', 'print-with-place', 'noprice', 'itemlist', 'deserving', 'stop-credit', 'done', 'itemlistid', 'print-no-price', 'fast', 'save-pdf', 'delev', 'print-for-driver'],
                        'roles' => ['createSales'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['update', 'add-item', 'view', 'print', 'print-with-place', 'noprice', 'remove', 'pdf', 'itemlist', 'deserving', 'stop-credit', 'done', 'transfer-to-temp-invoice', 'itemlistid', 'print-no-price', 'save-pdf', 'delev', 'print-for-driver'],
                        'roles' => ['updateSales'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['deleteSales'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['save-as-new'],
                        'roles' => ['saveSalesAsNew'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['indexSales'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['profit', 'net-profit', 'wait-qnty', 'wait-qnty-client', 'summation-sales'],
                        'roles' => ['profit'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['ftran'],
                        'roles' => ['ftran'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['back-create', 'back-items'],
                        'roles' => ['createBackSales'],
                    ],

                    [
                        'allow' => true,
                        'actions' => ['histrans'],
                        'roles' => ['clientHistrans'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['category-histrans'],
                        'roles' => ['categoryHistrans'],
                    ],

                ],
            ],
        ];
    }

    /** اسم قفل MySQL المشترك لكل ما يغيّر المخزون أو يولّد أرقام فواتير (نفس القفل في النقل والجرد) */
    const STOCK_LOCK = 'pos_stock_change';

    /** حقول لا يجوز أن تأتي من النموذج المرسل: يحسبها الخادم دائماً */
    const PROTECTED_FIELDS = ['id', 'total', 'branch', 'billId', 'created_at', 'user_insert', 'update_at', 'user_update'];

    // =====================================================================
    // أدوات داخلية: القفل والمعاملة والمخزون
    // =====================================================================

    /** يملأ النموذج من البيانات المرسلة مع استبعاد الحقول التي يحسبها الخادم */
    private function loadSalesForm(Sales $model, array $extraProtected = [])
    {
        $post = Yii::$app->request->post($model->formName());
        if (!is_array($post)) {
            return false;
        }
        foreach (array_merge(self::PROTECTED_FIELDS, $extraProtected) as $key) {
            unset($post[$key]);
        }
        $model->setAttributes($post, false);
        if (empty($model->at)) {
            $model->at = date('Y-m-d');
        }
        return true;
    }

    /** يحسب مجموع الفاتورة من أسطرها ويحفظه، ويعيده (null إن لم يبق أسطر) */
    private function recalcTotal($salesId)
    {
        $sum = SalesDetails::find()->where(['salesId' => $salesId])->sum('salePrice * quantity');
        Sales::updateAll(['total' => (float) $sum], ['id' => $salesId]);
        return $sum;
    }

    /** فرع الفاتورة (للفواتير القديمة بلا فرع نستعمل فرع المستخدم) */
    private function saleBranch(Sales $sale)
    {
        return (int) ($sale->branch ?: Yii::$app->user->identity->branch);
    }

    /** الفاتورة يجب أن تكون من فرع المستخدم لتعديلها */
    private function ownBranchOrFail(Sales $sale)
    {
        if ($sale->branch != Yii::$app->user->identity->branch) {
            throw new \DomainException(Yii::t('app', "This invoice is not issued by the branch you work in, you cannot update it"));
        }
    }

    /** قيمة قابلة للقراءة في أخطاء التحقق */
    private function firstError(Sales $model)
    {
        $errors = $model->getFirstErrors();
        return $errors ? reset($errors) : 'تعذر حفظ الفاتورة.';
    }

    public function actionIndex()
    {
        $searchModel = new SalesSearch();
        $searchModel->type = 99;
        $searchModel->branch = Yii::$app->user->identity->branch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $providerSalesDetails = new Yii\data\ArrayDataProvider([
            'allModels' => $model->salesDetails,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ],
            'pagination' => ['pageSize' => 200],

        ]);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'providerSalesDetails' => $providerSalesDetails,
        ]);
    }

    public function actionUpdate($id)
    {
        $company = CompanyInfo::find()->one();
        $model = $this->findModel($id);
        $identity = Yii::$app->user->identity;
        if ($model->branch != $identity->branch) {
            Yii::$app->session->setFlash('error', Yii::t('app', "This invoice is not issued by the branch you work in, you cannot update it"));
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => SalesDetails::find()->where(['=', 'salesId', $model->id]),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
            'pagination' => false,
        ]);

        // التعديل المباشر داخل الجدول (الكمية / السعر / التغليف)
        if (Yii::$app->request->post('hasEditable')) {
            return $this->inlineEditDetail($model, $company);
        }

        if (Yii::$app->request->post($model->formName()) !== null) {
            $oldType = (int) $model->type;
            $oldDisscount = $model->disscount;
            $oldPaid = $model->paid;
            // ما كانت تسهم به هذه الفاتورة في رصيد العميل (الآجلة النهائية فقط)، لنطرحه عند فحص السقف
            $oldCreditNet = ($oldType === 1 && (int) $model->payWay === 1)
                ? ((float) $model->total - (float) $oldDisscount - (float) $oldPaid)
                : 0.0;

            $this->loadSalesForm($model, ['carpenter', 'upholstered', 'paintId', 'deleviryId', 'path']);
            $model->type = (int) $model->type;

            if (!in_array($model->type, [1, 2, 3, 4], true)) {
                Yii::$app->session->setFlash('error', 'نوع الفاتورة غير صحيح.');
                return $this->redirect(['sales/update', 'id' => $model->id]);
            }

            $client = Client::find()->where(['id' => $model->clinet])->one();
            if ($client === null) {
                Yii::$app->session->setFlash('error', 'العميل غير موجود.');
                return $this->redirect(['sales/update', 'id' => $model->id]);
            }
            if ($client->post_paid != 1 && $model->payWay != 0) {
                Yii::$app->session->setFlash('error', Yii::t('app', "عفوا هذا الزبون لايمكن البيع له بالآجل"));
                return $this->redirect(['sales/update', 'id' => $model->id]);
            }

            // الخصم لمن يملك الصلاحية فقط، والمدفوع لا يعدّله مستخدم العميل
            if ($identity->makeDiscount != 1) {
                $model->disscount = $oldDisscount;
            }
            if ($identity->client <> null) {
                $model->paid = $oldPaid;
            }
            $model->disscount = ($model->disscount == null) ? 0 : max(0, (float) $model->disscount);
            $model->paid = ($model->paid == null) ? 0 : max(0, (float) $model->paid);

            $model->total = SalesDetails::find()->where(['salesId' => $model->id])->sum('salePrice * quantity');
            if ((float) $model->disscount > (float) $model->total) {
                Yii::$app->session->setFlash('error', 'الخصم لا يمكن أن يزيد عن إجمالي الفاتورة.');
                return $this->redirect(['sales/update', 'id' => $model->id]);
            }
            if ($model->payWay == 1 && $model->type == 1
                && (float) $model->paid > (float) $model->total - (float) $model->disscount) {
                Yii::$app->session->setFlash('error', 'المدفوع لا يمكن أن يزيد عن صافي الفاتورة.');
                return $this->redirect(['sales/update', 'id' => $model->id]);
            }

            // سقف الدين: يُتجاوز بصلاحية خاصة مع تنبيه بعد الحفظ
            $overDebt = false;
            $debtRow = Client::find()->select('debt')->where(['=', 'id', $model->clinet])->one();
            $balance = Dept::find()->where(['id' => $model->clinet])->sum('credt');
            if ($debtRow !== null && $debtRow['debt'] <> 0 && $model->type == 1) {
                // رصيد العميل بعد طرح مبلغ هذه الفاتورة القديم + (الإجمالي − الخصم − المدفوع) الجديد
                $total = ((float) $balance - $oldCreditNet)
                    + ((float) $model->total - (float) $model->disscount - (float) $model->paid);
                if ((float) $total >= (float) $debtRow['debt'] && $model->payWay == 1) {
                    if (Yii::$app->user->can('userCansellOverDebt')) {
                        $overDebt = true;
                    } else {
                        Yii::$app->session->setFlash('error', Yii::t('app', "Sorry The Client Cross The Line About Debt"));
                        return $this->redirect(['sales/update', 'id' => $model->id]);
                    }
                }
            }

            try {
                $this->withStockLock(function () use ($model, $oldType, $identity, $company) {
                    $model->branch = $identity->branch;
                    $model->update_at = date('Y-m-d H:i:s');
                    $model->user_update = $identity->id;

                    // تغيير النوع يغيّر أثر الفاتورة على المخزون (ويحتاج رقماً في تسلسل النوع الجديد)
                    if ($oldType !== (int) $model->type) {
                        $model->billId = (int) Sales::find()->where(['type' => $model->type])->max('billId') + 1;
                    }
                    $model->save(false);

                    if ($oldType !== (int) $model->type) {
                        $branch = $this->saleBranch($model);
                        $checked = in_array((int) $model->type, [1, 3], true);   // النوع الجديد يخصم من المخزون
                        $kept = 0;
                        $skipped = [];
                        $reduced = [];
                        foreach (SalesDetails::find()->where(['salesId' => $model->id])->all() as $d) {
                            // أولاً يُعاد أثر النوع القديم ليُحتسب رصيد السطر نفسه ضمن المتاح
                            $this->applyDetail($oldType, $d->category, $branch, $d->quantity, true);
                            $qty = (float) $d->quantity;
                            if ($checked) {
                                $qty = $this->allowedQty($d->category, $qty, [(int) $model->type], $company);
                                if ($qty <= 0) {
                                    $skipped[] = $d->category;
                                    $d->delete();
                                    continue;
                                }
                                if ($qty < (float) $d->quantity) {
                                    $reduced[] = $d->category;
                                    $d->quantity = $qty;
                                    $d->save(false);
                                }
                            }
                            $this->applyDetail($model->type, $d->category, $branch, $qty, false);
                            $kept++;
                        }
                        if ($kept === 0) {
                            // يتراجع كل شيء (النوع والمخزون)
                            throw new \DomainException('لا يوجد رصيد متاح لأي صنف في الفاتورة، لم يتم تغيير النوع.');
                        }
                        $this->recalcTotal($model->id);
                        if ($skipped || $reduced) {
                            $parts = [];
                            if ($skipped) {
                                $parts[] = 'أصناف حُذفت لعدم توفر رصيد: ' . implode('، ', $skipped);
                            }
                            if ($reduced) {
                                $parts[] = 'أصناف قُلّصت كمياتها للمتاح: ' . implode('، ', $reduced);
                            }
                            Yii::$app->session->addFlash('error', implode(' | ', $parts) . ' (أرقام الأصناف)');
                        }
                    }
                });
            } catch (\DomainException $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect(['sales/update', 'id' => $model->id]);
            }

            if ($overDebt) {
                Yii::$app->session->addFlash('error', Yii::t('app', "عفوا هذا العميل تجاوز سقف الديون ! الرجاء المراجعة"));
            }
            return $this->redirect(['print', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'company' => $company,
        ]);
    }

    /**
     * التعديل المباشر لسطر داخل فاتورة محفوظة. السطر يجب أن يتبع هذه الفاتورة،
     * وتغيّر الكمية يُحسب أثره على المخزون بالفرق (جديد - قديم) من PHP.
     */
    private function inlineEditDetail(Sales $sale, $company)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $key = (int) Yii::$app->request->post('editableKey');
        $all = Yii::$app->request->post('SalesDetails');
        $posted = is_array($all) ? current($all) : null;

        $detail = SalesDetails::findOne(['id' => $key, 'salesId' => $sale->id]);
        if ($detail === null || !is_array($posted)) {
            return ['output' => '', 'message' => 'السطر غير موجود في هذه الفاتورة.'];
        }

        // ----- الكمية -----
        if (array_key_exists('quantity', $posted)) {
            if (!is_numeric($posted['quantity']) || (float) $posted['quantity'] <= 0) {
                return ['output' => '', 'message' => 'الكمية يجب أن تكون رقماً أكبر من صفر. لحذف السطر استخدم زر الحذف.'];
            }
            $new = abs((float) $posted['quantity']);
            try {
                $final = $this->withStockLock(function () use ($sale, $detail, $company, $new) {
                    $old = (float) $detail->quantity;
                    $limit = $new;
                    if (in_array((int) $sale->type, [1, 3], true) && $company->zeroQnty == 0) {
                        // الكمية القديمة محسومة أصلاً من المخزون فتُحتسب ضمن المتاح (نوع المخزون = نوع الفاتورة)
                        $avail = $this->availableQty($detail->category, [(int) $sale->type], $company) + $old;
                        $limit = min($new, max(0, $avail));
                        if ($limit <= 0) {
                            throw new \DomainException('لا يوجد رصيد متاح لهذا الصنف، بقيت الكمية كما هي.');
                        }
                    }
                    $detail->quantity = $limit;
                    $detail->save(false);
                    $this->applyDetail($sale->type, $detail->category, $this->saleBranch($sale), $limit - $old, false);
                    $this->recalcTotal($sale->id);
                    return $limit;
                });
            } catch (\DomainException $e) {
                return ['output' => '', 'message' => $e->getMessage()];
            }
            return ['output' => $final, 'message' => ''];
        }

        // ----- السعر -----
        if (array_key_exists('salePrice', $posted)) {
            if (!Yii::$app->user->can('userCanUpdateSalePriceAfterSave')) {
                return ['output' => '', 'message' => 'لايوجد لديك صلاحية لتعديل سعر البيع'];
            }
            if (!is_numeric($posted['salePrice']) || (float) $posted['salePrice'] <= 0) {
                return ['output' => '', 'message' => 'سعر البيع يجب أن يكون رقماً أكبر من صفر.'];
            }
            $detail->salePrice = (float) $posted['salePrice'];
            $detail->save(false);
            $this->recalcTotal($sale->id);
            return ['output' => $detail->salePrice, 'message' => ''];
        }

        // ----- التغليف -----
        if (array_key_exists('packing', $posted)) {
            $detail->packing = mb_substr(trim((string) $posted['packing']), 0, 100);
            $detail->save(false);
            return ['output' => $detail->packing, 'message' => ''];
        }

        return ['output' => '', 'message' => 'هذا الحقل غير قابل للتعديل.'];
    }

    public function actionDelete($id)
    {
        $sale = $this->findModel($id);
        try {
            $this->withStockLock(function () use ($sale) {
                $branch = $this->saleBranch($sale);
                foreach (SalesDetails::find()->where(['salesId' => $sale->id])->all() as $d) {
                    $this->applyDetail($sale->type, $d->category, $branch, $d->quantity, true);
                }
                SalesDetails::deleteAll(['salesId' => $sale->id]);
                Sales::deleteAll(['id' => $sale->id]);
            });
            Yii::$app->session->setFlash('success', 'تم حذف الفاتورة وإعادة الأصناف للمخزون.');
        } catch (\DomainException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['view', 'id' => $sale->id]);
        }
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        $model = Sales::findOne((int) $id);

        // مستخدم العميل لا يرى إلا فواتير عملائه
        if ($model !== null) {
            $client = Yii::$app->user->identity->client;
            if ($client !== null && $client !== ''
                && !in_array((string) $model->clinet, array_map('trim', explode(',', (string) $client)), true)) {
                $model = null;
            }
        }

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionGetPrices($id)
    {

        $info = Totalinventory::find()
            ->where(['name' => $id])
            ->one();
        echo json::encode($info);
    }

    public function actionFtran()
    {
        $model = new Ftran();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->today != 0) {
                $model->min_date = date('Y-m-d');
                $model->max_date = date('Y-m-d');
            }

            if ($model->min_date == null) {
                Yii::$app->session->setFlash('error', 'الرجاء تحديد تاريخ الحركة من');
                return $this->redirect(['ftran']);
            }
            if ($model->max_date == null) {
                Yii::$app->session->setFlash('error', 'الرجاء تحديد تاريخ الحركة إلى');
                return $this->redirect(['ftran']);
            }

            $db = Yii::$app->db;
            $branch = (int) Yii::$app->user->identity->branch;
            $dates = [':min' => $model->min_date, ':max' => $model->max_date];

            if ($model->user_insert != null) {
                $lastBalance = $db->createCommand(
                    "SELECT sum(ftran.sader) as sader, sum(ftran.wared) as wared FROM ftran
                     WHERE ftran.date_ < :min AND ftran.outBox = 0 AND ftran.branch = :b
                     AND currancy = :cur AND ftran.user_insert = :u",
                    [':min' => $model->min_date, ':b' => $branch, ':cur' => $model->currancy, ':u' => $model->user_insert]
                )->queryAll();

                $info = $db->createCommand(
                    "SELECT ftran.*, payment_types.name as paymentType FROM ftran
                     LEFT JOIN payment_types ON payment_types.id = ftran.payment_type
                     WHERE ftran.branch = :b AND ftran.date_ BETWEEN :min AND :max
                     AND ftran.outBox = 0 AND currancy = :cur AND ftran.user_insert = :u",
                    $dates + [':b' => $branch, ':cur' => $model->currancy, ':u' => $model->user_insert]
                )->queryAll();
            } else {
                $lastBalance = $db->createCommand(
                    "SELECT sum(ftran.sader) as sader, sum(ftran.wared) as wared FROM ftran
                     WHERE ftran.date_ < :min AND ftran.outBox = 0 AND currancy = :cur AND ftran.branch = :b",
                    [':min' => $model->min_date, ':cur' => $model->currancy, ':b' => $branch]
                )->queryAll();

                $info = $db->createCommand(
                    "SELECT ftran.*, payment_types.name as paymentType FROM ftran
                     LEFT JOIN payment_types ON payment_types.id = ftran.payment_type
                     WHERE ftran.branch = :b AND ftran.date_ BETWEEN :min AND :max AND ftran.outBox = 0
                     ORDER BY ftran.date_",
                    $dates + [':b' => $branch]
                )->queryAll();
            }

            if (!$info) {
                Yii::$app->session->setFlash('error', 'عفوا لا يوجد بيانات للعرض');
                return $this->redirect(['ftran']);
            }

            $info1 = $db->createCommand(
                "SELECT max(payment_types.name), sum(sader), sum(wared) FROM `ftran`
                 LEFT JOIN payment_types ON payment_types.id = ftran.payment_type
                 WHERE ftran.branch = :b AND ftran.date_ BETWEEN :min AND :max
                 GROUP BY ftran.payment_type",
                $dates + [':b' => $branch]
            )->queryAll();

            return $this->render('ftranRep', [
                'models' => $info,
                'min_date' => $model->min_date,
                'max_date' =>  $model->max_date,
                'sumwared' => 0,
                'sumsader' => 0,
                'disscount' => 0,
                'sum' => 0,
                'coun' => 1,
                'count' => 0,
                'lastBalance' => $lastBalance,
                'sumSql' => $info1,
            ]);
        }

        return $this->render('ftran', [
            'model' => $model,
        ]);
    }

    /** فاتورة بلا أسعار: لا تُجلب الأسعار أصلاً، والمخزون يُربط بـ LEFT JOIN فلا يسقط سطر لعدم وجود رصيد */
    public function actionNoprice($id)
    {
        $this->layout = 'invoice';

        $company = CompanyInfo::find()->one();
        $model = $this->findModel($id);
        $branch = (int) Yii::$app->user->identity->branch;

        $providerSalesDetails = new ActiveDataProvider([
            'query' => SalesDetails::find()
                ->select([
                    'salesDetails.id',
                    'salesDetails.salesId',
                    'salesDetails.category',
                    'salesDetails.quantity',
                    'branches.name as branchName',
                    'Totalinventory.quantity as Qtotalinventory',
                    'category_reservation.quantity as reservation',
                    '(SELECT SUM(quantity) from Totalinventory where branch <> ' . $branch . ' and type <> 3 and id = salesDetails.category) as otherQtotalinventory',
                ])
                ->leftJoin(
                    'Totalinventory',
                    'salesDetails.category = Totalinventory.id AND Totalinventory.branch = :tb AND Totalinventory.type <> 3',
                    [':tb' => $branch]
                )
                ->leftJoin('category_reservation', 'salesDetails.category = category_reservation.category')
                ->leftJoin('branches', 'branches.id = salesDetails.branch')
                ->where(['=', 'salesDetails.salesId', $model->id]),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
            'pagination' => false,
        ]);

        return $this->render('printBill', [
            'model' => $model,
            'providerSalesDetails' => $providerSalesDetails,
            'company' => $company,
        ]);
    }

    public function actionPrintWithPlace($id)
    {
        $this->layout = 'invoice';

        $model = $this->findModel($id);
        $totalInvoice = SalesDetails::find()
            ->where(['salesId' => $model->id])
            ->sum('salePrice * quantity');

        $company = CompanyInfo::find()->one();
        $providerSalesDetails = new ActiveDataProvider([
            'query' => SalesDetails::find()
                ->select([
                    'salesDetails.id',
                    'salesDetails.salesId',
                    'salesDetails.category',
                    'salesDetails.quantity',
                    'salesDetails.salePrice',
                    'branches.name as branchName',
                ])
                ->leftJoin('branches', 'branches.id = salesDetails.branch')
                ->where(['=', 'salesDetails.salesId', $model->id]),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            'pagination' => false,
        ]);

        $balance = Dept::find()->where(['id' => $model->clinet])->sum('credt');
        return $this->render('print_with_place', [
            'model' => $model,
            'providerSalesDetails' => $providerSalesDetails,
            'company' => $company,
            'balance' => $balance,
            'totalInvoice' => $totalInvoice,
        ]);
    }

    public function actionProfit()
    {
        $model = new Sales();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->min_date == null || $model->max_date == null) {
                Yii::$app->session->setFlash('error', 'الرجاء تحديد تاريخ الحركة من وإلى');
                return $this->redirect(['profit']);
            }

            $sql = "SELECT salesDetails.category, category.name, salesDetails.costPrice, salesDetails.salePrice,
                    (salesDetails.salePrice - salesDetails.costPrice) profit, salesDetails.quantity
                    FROM sales, salesDetails, category
                    WHERE sales.id = salesDetails.salesId and salesDetails.category = category.id
                    and sales.type = 1
                    and sales.at between :min and :max";
            $params = [':min' => $model->min_date, ':max' => $model->max_date];
            if (!Yii::$app->user->can('profit')) {
                $sql .= " and sales.branch = :b";
                $params[':b'] = (int) Yii::$app->user->identity->branch;
            }
            $sql .= " order by salesDetails.category";

            $info = Yii::$app->db->createCommand($sql, $params)->queryAll();
            if (!$info) {
                Yii::$app->session->setFlash('error', 'عفوا لا يوجد بيانات للعرض');
                return $this->redirect(['profit']);
            }

            return $this->render('profitRep', [
                'models' => $info,
                'min_date' => $model->min_date,
                'max_date' =>  $model->max_date,
                'coun' => 1,
                'count' => 0,
                'quantity' => 0,
                'costPrice' => 0,
                'salePrice' => 0,
                'total' => 0,
                'profit' => 0,
                'totalPrice' => 0,
            ]);
        }

        return $this->render('profit', [
            'model' => $model,
        ]);
    }

    public function actionNetProfit()
    {
        $model = new Sales();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->min_date == null || $model->max_date == null) {
                Yii::$app->session->setFlash('error', 'الرجاء تحديد تاريخ الحركة من وإلى');
                return $this->redirect(['net-profit']);
            }

            $isManager = Yii::$app->user->can('profit');
            $br = $isManager ? '' : ' and %s.branch = {BR}';

            // لكل جزء في UNION أسماء معاملات خاصة به
            $parts = [
                "SELECT 'ارباح المبيعات' AS DESCRIBTION, sum((salesDetails.salePrice - salesDetails.costPrice) * salesDetails.quantity) AS total,
                 sum((salesDetails.salePrice - salesDetails.costPrice) * salesDetails.quantity) AS NET
                 FROM sales, salesDetails, category
                 WHERE sales.id = salesDetails.salesId and salesDetails.category = category.id and sales.type = 1
                 and sales.at between {MIN} and {MAX}" . sprintf($br, 'sales'),

                "SELECT 'مسترجع المبيعات', sum((salesDetails.salePrice - salesDetails.costPrice) * salesDetails.quantity),
                 sum((salesDetails.salePrice - salesDetails.costPrice) * salesDetails.quantity) * -1
                 FROM sales, salesDetails, category
                 WHERE sales.id = salesDetails.salesId and salesDetails.category = category.id and sales.type = 2
                 and sales.at between {MIN} and {MAX}" . sprintf($br, 'sales'),

                "SELECT 'خصومات المبيعات', sum(sales.disscount), sum(sales.disscount) * -1
                 FROM sales WHERE sales.type = 1 and sales.at between {MIN} and {MAX}" . sprintf($br, 'sales'),

                "SELECT 'اجمالي المصروفات', COALESCE(sum(expenses.value),'0'), COALESCE(sum(expenses.value),'0') * -1
                 FROM expenses WHERE expenses.at between {MIN} and {MAX}" . sprintf($br, 'expenses'),

                "SELECT 'اجمالي المرتبات', COALESCE(sum(emp_salary.value),'0'), COALESCE(sum(emp_salary.value),'0') * -1
                 FROM emp_salary WHERE emp_salary.at between {MIN} and {MAX}",
            ];

            $sqlParts = [];
            $params = [];
            foreach ($parts as $i => $part) {
                $sqlPart = str_replace(['{MIN}', '{MAX}', '{BR}'], [":min$i", ":max$i", ":br$i"], $part);
                $sqlParts[] = $sqlPart;
                $params[":min$i"] = $model->min_date;
                $params[":max$i"] = $model->max_date;
                if (strpos($sqlPart, ":br$i") !== false) {
                    $params[":br$i"] = (int) Yii::$app->user->identity->branch;
                }
            }

            $info = Yii::$app->db->createCommand(implode(' UNION ', $sqlParts), $params)->queryAll();
            if (!$info) {
                Yii::$app->session->setFlash('error', 'عفوا لا يوجد بيانات للعرض');
                return $this->redirect(['net-profit']);
            }

            return $this->render('netProfitRep', [
                'models' => $info,
                'min_date' => $model->min_date,
                'max_date' =>  $model->max_date,
                'coun' => 1,
                'count' => 0,
                'quantity' => 0,
                'costPrice' => 0,
                'salePrice' => 0,
                'total' => 0,
                'profit' => 0,
            ]);
        }

        return $this->render('netProfit', [
            'model' => $model,
        ]);
    }

    public function actionSummationSales()
    {
        $model = new Sales();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->user_insert == null) {
                Yii::$app->session->setFlash('error', 'الرجاء تحديد اسم الموظف');
                return $this->redirect(['summation-sales']);
            }
            if ($model->min_date == null || $model->max_date == null) {
                Yii::$app->session->setFlash('error', 'الرجاء تحديد تاريخ الحركة من وإلى');
                return $this->redirect(['summation-sales']);
            }

            $sql = "SELECT 'ارباح المبيعات' AS DESCRIBTION, ifNull(sum((salesDetails.salePrice - salesDetails.costPrice) * salesDetails.quantity),0) AS total,
                    ifNull(sum((salesDetails.salePrice - salesDetails.costPrice) * salesDetails.quantity),0) AS NET
                    FROM sales, salesDetails
                    WHERE sales.id = salesDetails.salesId and sales.type = 1
                    and sales.currancy = :cur1 and sales.user_insert = :u1 and sales.at between :min1 and :max1
                    UNION
                    SELECT 'مسترجع المبيعات', ifNull(sum((salesDetails.salePrice - salesDetails.costPrice) * salesDetails.quantity),0),
                    ifNull(sum((salesDetails.salePrice - salesDetails.costPrice) * salesDetails.quantity) * -1,0)
                    FROM sales, salesDetails
                    WHERE sales.id = salesDetails.salesId and sales.type = 2
                    and sales.currancy = :cur2 and sales.user_insert = :u2 and sales.at between :min2 and :max2";
            $params = [
                ':cur1' => $model->currancy, ':u1' => (int) $model->user_insert, ':min1' => $model->min_date, ':max1' => $model->max_date,
                ':cur2' => $model->currancy, ':u2' => (int) $model->user_insert, ':min2' => $model->min_date, ':max2' => $model->max_date,
            ];

            $info = Yii::$app->db->createCommand($sql, $params)->queryAll();
            if (!$info) {
                Yii::$app->session->setFlash('error', 'عفوا لا يوجد بيانات للعرض');
                return $this->redirect(['summation-sales']);
            }

            return $this->render('summation', [
                'models' => $info,
                'model' => $model,
                'min_date' => $model->min_date,
                'max_date' =>  $model->max_date,
                'coun' => 1,
                'count' => 0,
                'total' => 0,
                'disscount' => 0,
            ]);
        }

        return $this->render('_summation', [
            'model' => $model,
        ]);
    }

    public function actionPdf($id)
    {
        $model = $this->findModel($id);

        $totalInvoice = SalesDetails::find()->where(['salesId' => $model->id])
            ->sum('salePrice * quantity');

        $company = CompanyInfo::find()->one();
        $providerSalesDetails = new Yii\data\ArrayDataProvider([
            'allModels' => $model->salesDetails,
            'pagination' => false,
        ]);

        $balance = Dept::find()->where(['id' => $model->clinet])->sum('credt');

        $email = $model->c ? trim((string) $model->c->email) : '';
        if ($email === '') {
            Yii::$app->session->setFlash('error', Yii::t('app', "Sorry This Client Has Not Email"));
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }

        // الملف المؤقت خارج المجلد العام (runtime) ويُحذف بعد الإرسال
        $dir = Yii::getAlias('@runtime/invoices');
        FileHelper::createDirectory($dir);
        $file = $dir . DIRECTORY_SEPARATOR . 'invoice-' . $model->id . '-' . Yii::$app->security->generateRandomString(8) . '.pdf';

        try {
            $mpdf = new Mpdf();
            $mpdf->WriteHTML($this->render('print', [
                'model' => $model,
                'providerSalesDetails' => $providerSalesDetails,
                'company' => $company,
                'balance' => $balance,
                'totalInvoice' => $totalInvoice,
            ]));
            $mpdf->Output($file, 'F');

            $send = Yii::$app->mailer->compose()
                ->setFrom('algmatyali@gmail.com')
                ->setTo($email)
                ->setSubject('فاتورة مبيعات ')
                ->setTextBody('Plain text content. YII2 Application')
                ->setHtmlBody('<b>السلام عليكم ورحمة الله وبركاته.</b><br>
        السيـد: ' . ' ' . Html::encode($model->c->name) . '<br>' . ' أرفق لك مع رسالتي هذه نسخة من فاتورة رقم ' . ' ' . Html::encode($model->billId))
                ->attach($file)
                ->send();
        } finally {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        if ($send) {
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully Sent The Invoice To Customer'));
        }
        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }

    public function actionDeserving()
    {
        $client = Yii::$app->user->identity->client;

        if ($client == NULL) {
            $sql = " SELECT max(sales.billId) as billId, max(sales.at) as at, max(sales.total) total
            , max(sales.disscount) as disscount, max(sales.paid) as paid, max(sales.deserving) as deserving
            , max(client.name) as clientName, sum(dept.credt) as credt, max(sales.id) as id
            , max(sales.clinet) as client, max(client.post_paid) as post_paid FROM `sales`
            LEFT JOIN `client` ON client.id = sales.clinet
            LEFT JOIN `dept` ON client.id = dept.id
            WHERE sales.deserving - CURRENT_DATE() <= 4
            GROUP BY `sales`.`id`, `sales`.`deserving`";
        } else {
            // أرقام العملاء المسموحة تُحوَّل لأعداد صحيحة قبل دخولها في النص
            $ids = array_filter(array_map('intval', explode(',', (string) $client)));
            $in = $ids ? implode(',', $ids) : '0';
            $sql = " SELECT max(sales.billId) as billId, max(sales.at) as at, max(sales.total) total
            , max(sales.disscount) as disscount, max(sales.paid) as paid, max(sales.deserving) as deserving
            , max(client.name) as clientName, sum(dept.credt) as credt, max(sales.id) as id
            , max(sales.clinet) as client FROM `sales`
            LEFT JOIN `client` ON client.id = sales.clinet
            LEFT JOIN `dept` ON client.id = dept.id
            WHERE sales.deserving - CURRENT_DATE() <= 4 and sales.clinet in(" . $in . ")
            GROUP BY `sales`.`id`, `sales`.`deserving`";
        }

        $info = Yii::$app->db->createCommand($sql)->queryAll();

        $this->layout = 'invoice';
        return $this->render('deserving', [
            'models' => $info,
            'count' => 0,
        ]);
    }

    public function actionHistrans($client, $allData, $type)
    {
        $client = (int) $client;
        $type = (int) $type;
        $min_date = '2020-01-30';
        $max_date = date('Y-m-d');
        $db = Yii::$app->db;

        if ($type === 0 || $type === 2) {
            $lastBalance = $db->createCommand(
                "SELECT sum(histrans_client.dept) as sader, sum(histrans_client.credt) as wared, max(histrans_client.type) as type
                 FROM histrans_client
                 WHERE histrans_client.Type = :t AND histrans_client.id = :c AND histrans_client.trandate < :min",
                [':t' => $type, ':c' => $client, ':min' => $min_date]
            )->queryAll();

            $info = $db->createCommand(
                "SELECT histrans_client.id, histrans_client.trandate, histrans_client.name as name,
                 histrans_client.dept as sader, histrans_client.billId as billId, histrans_client.kind as kind,
                 histrans_client.credt as wared, histrans_client.printId as printId, histrans_client.type as type, histrans_client.deleviried as deleviried
                 FROM histrans_client
                 WHERE histrans_client.Type in(0,2) AND histrans_client.id = :c
                 AND histrans_client.trandate between :min AND :max
                 AND histrans_client.sort <> 1
                 ORDER BY histrans_client.trandate, histrans_client.billId",
                [':c' => $client, ':min' => $min_date, ':max' => $max_date]
            )->queryAll();
        } elseif ($type === 1) {
            $lastBalance = $db->createCommand(
                "SELECT sum(histrans_client.dept) as sader, sum(histrans_client.credt) as wared, max(histrans_client.type) as type
                 FROM histrans_client
                 WHERE histrans_client.Type = 1 AND histrans_client.id = :c AND histrans_client.trandate < :min",
                [':c' => $client, ':min' => $min_date]
            )->queryAll();

            $info = $db->createCommand(
                "SELECT histrans_supplier.id, histrans_supplier.kind, histrans_supplier.trandate, histrans_supplier.name as name,
                 histrans_supplier.dept as wared, histrans_supplier.credt as sader, histrans_supplier.billId as billId,
                 histrans_supplier.printId as printId, histrans_supplier.type as type
                 FROM histrans_supplier
                 WHERE histrans_supplier.Type = 1 AND histrans_supplier.id = :c
                 AND histrans_supplier.trandate between :min AND :max
                 AND histrans_supplier.sort <> 1
                 ORDER BY histrans_supplier.trandate, histrans_supplier.billId",
                [':c' => $client, ':min' => $min_date, ':max' => $max_date]
            )->queryAll();
        } else {
            throw new \yii\web\BadRequestHttpException('نوع غير صحيح.');
        }

        if (!$info) {
            Yii::$app->session->setFlash('error', 'عفوا لايوجد بيانات للعرض');
            return $this->redirect(['client/histrans']);
        }

        return $this->render('/client/histransrep', [
            'models' => $info,
            'min_date' => $min_date,
            'max_date' => $max_date,
            'sumwared' => 0,
            'sumsader' => 0,
            'sum' => 0,
            'coun' => 1,
            'count' => 0,
            'lastBalance' => $lastBalance,
            'balance' => 0,
            'id' => null,
        ]);
    }

    public function actionSavePdf($id)
    {
        $model = $this->findModel($id);
        $identity = Yii::$app->user->identity;

        $balance = Dept::find()->where(['id' => $model->clinet])->sum('credt');

        // التكلفة لا تُجلب لمن لا يملك صلاحية رؤيتها
        $costCol = ($identity->seeCostPrice == 1) ? 'salesDetails.costPrice' : 'NULL AS costPrice';
        $sql = "select sales.id, sales.at, sales.payWay, sales.total, sales.disscount, sales.paid, sales.type, sales.billId, sales.notes, sales.deleviried,
                salesDetails.quantity, $costCol, salesDetails.salePrice,
                Totalinventory.quantity as Qtotalinventory,
                client.name as client, client.mobile,
                category.name as category, category.serialNo, category.company,
                category_reservation.quantity as reservation
                FROM sales
                JOIN salesDetails on sales.id = salesDetails.salesId
                LEFT JOIN Totalinventory on salesDetails.category = Totalinventory.id
                     AND Totalinventory.branch = :b AND Totalinventory.type <> 3
                JOIN client on sales.clinet = client.id
                JOIN category on category.id = salesDetails.category
                left JOIN category_reservation on salesDetails.category = category_reservation.category
                where sales.id = :id";
        $info = Yii::$app->db->createCommand($sql, [':b' => (int) $identity->branch, ':id' => $model->id])->queryAll();

        $content = $this->renderPartial('printPdf', [
            'model' => $model,
            'infos' => $info,
            'models' => $info,
            'balance' => $balance,
        ]);
        Yii::$app->response->format = Response::FORMAT_RAW;

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_DOWNLOAD,
            'filename' => ($model->c ? $model->c->name : 'invoice') . '-' . $model->billId,
            'content' => $content,
            'cssFile' => ['@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css', '@vendor/bower-asset/bootstrap-rtl/dist/css/bootstrap-rtl.css'],
            'defaultFont' => 'Sans-serif',
            'options' => ['title' => 'فاتورة مبيعات'],
            'methods' => [
                'SetHeader' => [''],
            ]
        ]);

        return $pdf->render();
    }

    public function actionCategoryHistrans($category, $allData)
    {
        $category = Category::find()->select('id')->where(['id' => (int) $category])->one();
        if ($category === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        $min_date = '2010-01-01';
        $max_date = date('Y-m-d');
        $db = Yii::$app->db;

        $lastBalance = $db->createCommand(
            "SELECT sum(category_histrans.quantity) as quantity
             FROM category_histrans
             WHERE category_histrans.id = :c AND category_histrans.tranDate < :min",
            [':c' => (int) $category->id, ':min' => $min_date]
        )->queryAll();

        $info = $db->createCommand(
            "SELECT category_histrans.kind_id, category_histrans.id, category_histrans.printId, category_histrans.name as name,
             category_histrans.quantity as quantity, category_histrans.unit as unit,
             category_histrans.box as box, category_histrans.class as class, category_histrans.client as client,
             branches.name as branch, category_histrans.kind as kind, category_histrans.trandate as trandate, category_histrans.billId as billId,
             category_histrans.deleviried as deleviried
             FROM category_histrans, branches
             WHERE category_histrans.branch = branches.id AND category_histrans.id = :c
             AND category_histrans.trandate between :min AND :max
             ORDER BY category_histrans.trandate",
            [':c' => (int) $category->id, ':min' => $min_date, ':max' => $max_date]
        )->queryAll();

        if (!$info) {
            Yii::$app->session->setFlash('error', 'عفوا لايوجد بيانات للعرض');
            return $this->redirect(['category/index']);
        }

        return $this->render('/category/histransrep', [
            'models' => $info,
            'min_date' => $min_date,
            'max_date' => $max_date,
            'sumsader' => 0,
            'sumwared' => 0,
            'sum' => 0,
            'coun' => 1,
            'count' => 0,
            'name' => null,
            'id' => 0,
            'lastBalance' => $lastBalance,
            'sumPurchase' => 0,
            'sumBackPurchase' => 0,
            'sumSales' => 0,
            'sumBackSales' => 0,
        ]);
    }

    public function actionItemlistid($q = null, $id = null)
    {
        $identity = Yii::$app->user->identity;
        if ($identity === null) {
            throw new \yii\web\ForbiddenHttpException();
        }
        $isClient = ($identity->client != null);
        $company = CompanyInfo::find()->one();

        $criteria = ($company->criteriaـvalue != 0) ? (float) $company->criteriaـvalue : 1.0;
        if ($company->rate != 0) {
            $crit = sprintf('%.6F', $criteria);
            $rate = sprintf('%.6F', (float) $company->rate / 100);
            $maxPrice = "CASE WHEN maxPrice >= $crit THEN ROUND(maxPrice * $rate + maxPrice) ELSE maxPrice END AS maxPrice";
            $minPrice = "CASE WHEN maxPrice >= $crit THEN ROUND(minPrice * $rate + minPrice) ELSE minPrice END AS minPrice";
        } else {
            $maxPrice = 'maxPrice';
            $minPrice = 'minPrice';
        }

        $branchCondition = ($identity->seeOtherBranchQ == 0)
            ? ['stocks.branch' => (int) $identity->branch]
            : ['in', 'stocks.branch', $this->allBranchIds()];

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q)) {
            $select = [
                'category.id',
                'category.name AS text',
                'company AS company',
                'stocks.quantity AS quantity',
                $maxPrice,
                'serialNo AS serialNo',
                'place',
                'commCode',
                "CASE WHEN `type` = 1 THEN 'متوفر' WHEN `type` = 2 THEN 'متوفر' ELSE 'قريبا' END AS type",
                'branches.name AS BRNAME',
            ];
            if (!$isClient) {
                $select[] = $minPrice;
                if ($identity->seeCostPrice == 1) {
                    $select[] = 'costPrice AS costPrice';
                }
            }

            $out['results'] = array_values((new Query())
                ->select($select)
                ->from('stocks')
                ->leftJoin('category', 'stocks.category = category.id')
                ->leftJoin('prices', 'prices.category = category.id')
                ->leftJoin('branches', 'branches.id = stocks.branch')
                ->where(['=', 'category.id', (string) $q])
                ->andWhere($branchCondition)
                ->andWhere(['=', 'category.status', 0])
                ->andWhere(['!=', 'stocks.quantity', 0])
                ->limit(60)
                ->orderBy(['category.id' => SORT_ASC])
                ->all());
        } elseif ($id > 0) {
            $item = Totalinventory::find()->where(['id' => (int) $id])->one();
            if ($item !== null) {
                $result = [
                    'id' => (int) $id,
                    'text' => $item->name,
                    'company' => $item->company,
                    'quantity' => $item->quantity,
                    'maxPrice' => $item->maxPrice,
                    'serialNo' => $item->serialNo,
                    'type' => $item->type,
                    'commCode' => $item->commCode,
                ];
                if (!$isClient) {
                    $result['minPrice'] = $item->minPrice;
                }
                $out['results'] = $result;
            }
        }
        return $out;
    }

    public function actionCreate()
    {
        $model = new Sales();
        $company = CompanyInfo::find()->one();
        $identity = Yii::$app->user->identity;
        $userId = (int) $identity->id;

        $model->payWay = ($company->payWayCash == 1) ? 0 : 1;

        if (Yii::$app->request->post($model->formName()) !== null) {
            $this->loadSalesForm($model);
            $model->type = (int) $model->type;
            $back = ['temp-invoice/create', 'id' => 1];

            if (!in_array($model->type, [1, 3, 4], true)) {
                Yii::$app->session->setFlash('error', 'نوع الفاتورة غير صحيح.');
                return $this->redirect($back);
            }

            $client = Client::find()->where(['id' => $model->clinet])->one();
            if ($client === null) {
                Yii::$app->session->setFlash('error', 'اختر العميل.');
                return $this->redirect($back);
            }
            if ($identity->client <> null && !in_array((string) $model->clinet, array_map('trim', explode(',', (string) $identity->client)), true)) {
                Yii::$app->session->setFlash('error', 'هذا العميل غير مسموح لك.');
                return $this->redirect($back);
            }
            if ($client->post_paid != 1 && $model->payWay != 0) {
                Yii::$app->session->setFlash('error', Yii::t('app', "عفوا هذا الزبون لايمكن البيع له بالآجل"));
                return $this->redirect($back);
            }

            $rows = TempInvoice::find()->where(['created_by' => $userId, 'state' => 1])->all();
            if (!$rows) {
                Yii::$app->session->setFlash('error', Yii::t('app', "عفوا الفاتورة لاتحتوي على اصناف الرجاء الغاءها"));
                return $this->redirect($back);
            }

            $model->total = TempInvoice::find()->where(['created_by' => $userId, 'state' => 1])->sum('salePrice * quantity');

            // الخصم لمن يملك الصلاحية فقط
            if ($identity->makeDiscount != 1) {
                $model->disscount = 0;
            }
            $model->disscount = ($model->disscount == null) ? 0 : max(0, (float) $model->disscount);
            $model->paid = ($model->paid == null) ? 0 : max(0, (float) $model->paid);

            if ($identity->client <> null) {
                $model->deleviried = 0;
                $model->wholesale = 0;
                $model->paid = 0;
            }

            if ((float) $model->disscount > (float) $model->total) {
                Yii::$app->session->setFlash('error', 'الخصم لا يمكن أن يزيد عن إجمالي الفاتورة.');
                return $this->redirect($back);
            }
            // في الآجل يتحول المتبقي إلى دين، فلا يجوز أن يزيد المدفوع عن صافي الفاتورة
            if ($model->payWay == 1 && $model->type != 4
                && (float) $model->paid > (float) $model->total - (float) $model->disscount) {
                Yii::$app->session->setFlash('error', 'المدفوع لا يمكن أن يزيد عن صافي الفاتورة.');
                return $this->redirect($back);
            }

            // سقف الدين: يُتجاوز بصلاحية خاصة مع تنبيه
            $overDebt = false;
            $balance = Dept::find()->where(['id' => $model->clinet])->sum('credt');
            $debtRow = Client::find()->select('debt')->where(['=', 'id', $model->clinet])->one();
            if ($debtRow !== null && $debtRow['debt'] <> 0 && $model->type == 1) {
                // المبلغ المقارَن: رصيد العميل + (إجمالي الفاتورة − الخصم − المدفوع)
                $total = $balance + ((float) $model->total - (float) $model->disscount - (float) $model->paid);
                if ((float) $total >= (float) $debtRow['debt'] && $model->payWay == 1) {
                    if (Yii::$app->user->can('userCansellOverDebt')) {
                        $overDebt = true;
                    } else {
                        Yii::$app->session->setFlash('error', Yii::t('app', "Sorry The Client Cross The Line About Debt"));
                        return $this->redirect($back);
                    }
                }
            }

            try {
                $id = $this->withStockLock(function () use ($model, $rows, $company, $identity, $userId) {
                    $branch = (int) $identity->branch;
                    $model->branch = $branch;
                    $model->billId = (int) Sales::find()->where(['=', 'type', $model->type])->max('billId') + 1;
                    $model->created_at = date('Y-m-d H:i:s');
                    $model->user_insert = Yii::$app->user->id;
                    $model->save(false);
                    $id = $model->id;
                    $saved = 0;
                    $skipped = [];
                    $reduced = [];

                    foreach ($rows as $data) {
                        $modelDetails = new SalesDetails();
                        $modelDetails->salesId = $id;
                        $modelDetails->category = $data->category;
                        $modelDetails->waitQnty = $data->waitQnty;
                        $modelDetails->costPrice = $data->costPrice;
                        $modelDetails->salePrice = $data->salePrice;
                        $modelDetails->box = $data->box;
                        $modelDetails->expire = $data->expire;
                        $modelDetails->type = $data->type;
                        $modelDetails->original_price = $data->salePrice;
                        $modelDetails->serial_number = $data->serial_number;
                        $modelDetails->branch = $data->branch;

                        if ($model->type != 4) {
                            // نوع المخزون الذي يُفحص رصيده: نفس نوع الفاتورة (1 نهائية، 3 حجز)
                            $qty = $this->allowedQty($data->category, $data->quantity, [$model->type], $company);
                            if ($qty <= 0) {
                                // لا رصيد متبقٍ: لا يُدرج السطر في الفاتورة
                                $skipped[] = $data->category;
                                continue;
                            }
                            if ($qty < (float) $data->quantity) {
                                $reduced[] = $data->category;
                            }
                            $modelDetails->quantity = $qty;
                            $modelDetails->save(false);
                            // الخصم من فرع المستخدم دائماً، وينشأ الصف برصيد سالب إن لم يوجد
                            $this->applyDetail($model->type, $data->category, $branch, $modelDetails->quantity, false);
                        } else {
                            $modelDetails->quantity = $data->quantity;
                            $modelDetails->save(false);
                        }
                        $saved++;
                    }

                    if ($saved === 0) {
                        // يتراجع الحفظ كله (الفاتورة والمخزون)، وتبقى الفاتورة المؤقتة كما هي
                        throw new \DomainException('لا يوجد رصيد متاح لأي صنف في الفاتورة، لم يتم الحفظ.');
                    }
                    if ($skipped || $reduced) {
                        $parts = [];
                        if ($skipped) {
                            $parts[] = 'أصناف حُذفت لعدم توفر رصيد: ' . implode('، ', $skipped);
                        }
                        if ($reduced) {
                            $parts[] = 'أصناف قُلّصت كمياتها للمتاح: ' . implode('، ', $reduced);
                        }
                        Yii::$app->session->addFlash('error', implode(' | ', $parts) . ' (أرقام الأصناف)');
                    }
                    $this->recalcTotal($id);
                    TempInvoice::deleteAll(['created_by' => $userId, 'state' => 1]);
                    return $id;
                });
            } catch (\DomainException $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect($back);
            }

            if ($overDebt) {
                Yii::$app->session->addFlash('error', Yii::t('app', "عفوا هذا العميل تجاوز سقف الديون ! الرجاء المراجعة"));
            }
            return $this->redirect(['print', 'id' => $id]);
        } elseif (Yii::$app->request->isAjax) {
            $model->at = date('Y-m-d');
            $model->clinet = 1;
            $model->total = TempInvoice::find()->where(['created_by' => $userId, 'state' => 1])
                ->sum('salePrice * quantity');
            $model->paid = 0;
            $model->disscount = 0;

            return $this->renderAjax('_form', [
                'model' => $model,
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionPrint($id)
    {
        $this->layout = 'invoice';
        $totalInvoice = SalesDetails::find()->where(['salesId' => $id])
            //->andWhere(['=', 'branch', Yii::$app->user->identity->branch])
            ->sum('salePrice * quantity');

        $company = CompanyInfo::find()->one();
        $model = $this->findModel($id);
        $providerSalesDetails = new ActiveDataProvider([
            'query' => SalesDetails::find()
                ->where(['=', 'salesId', $id]),
            //->andWhere(['=', 'branch', Yii::$app->user->identity->branch]),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            'pagination' => false,
        ]);

        $balance = Dept::find()->where(['id' => $model->clinet])->sum('credt');
        return $this->render('print', [
            'model' => $this->findModel($id),
            'providerSalesDetails' => $providerSalesDetails,
            'company' => $company,
            'balance' => $balance,
            'totalInvoice' => $totalInvoice,
        ]);
    }

    public function actionPrintNoPrice($id)
    {
        $this->layout = 'invoice';

        $company = CompanyInfo::find()->one();
        $providerSalesDetails = new ActiveDataProvider([
            'query' => SalesDetails::find()->where(['=', 'salesId', $id]),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
            'pagination' => false,
        ]);

        return $this->render('noPrice', [
            'model' => $this->findModel($id),
            'providerSalesDetails' => $providerSalesDetails,
            'company' => $company,
        ]);
    }

    public function actionPrintForDriver($id)
    {
        $this->layout = 'invoice';

        $totalInvoice = SalesDetails::find()->where(['salesId' => $id])
            //->andWhere(['=', 'branch', Yii::$app->user->identity->branch])
            ->sum('salePrice * quantity');

        $company = CompanyInfo::find()->one();
        $model = $this->findModel($id);
        $providerSalesDetails = new ActiveDataProvider([
            'query' => SalesDetails::find()
                ->where(['=', 'salesId', $id]),
            //->andWhere(['=', 'branch', Yii::$app->user->identity->branch]),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            'pagination' => false,
        ]);
        // $this->layout = false;
        return $this->render('print_for_driver', [
            'model' => $this->findModel($id),
            'providerSalesDetails' => $providerSalesDetails,
            'company' => $company,
            'totalInvoice' => $totalInvoice,
        ]);
    }

    public function actionRemove($id)
    {
        $detail = SalesDetails::findOne((int) $id);
        if ($detail === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $sale = $this->findModel($detail->salesId);

        try {
            $this->ownBranchOrFail($sale);
            $left = $this->withStockLock(function () use ($sale, $detail) {
                $this->applyDetail($sale->type, $detail->category, $this->saleBranch($sale), $detail->quantity, true);
                SalesDetails::deleteAll(['id' => $detail->id]);
                $total = $this->recalcTotal($sale->id);
                if ($total === null) {
                    Sales::deleteAll(['id' => $sale->id]);
                    return false;
                }
                return true;
            });
        } catch (\DomainException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['update', 'id' => $sale->id]);
        }

        return $left ? $this->redirect(['update', 'id' => $sale->id]) : $this->redirect(['sales/index']);
    }

    public function actionItemlist($q = null, $id = null)
    {
        $identity = Yii::$app->user->identity;
        if ($identity === null) {
            throw new \yii\web\ForbiddenHttpException();
        }
        $isClient = ($identity->client != null);

        $company = CompanyInfo::find()->one();

        // أي قيمة تدخل في نص SQL تُحوَّل لرقم أولاً
        $criteria = ($company->criteriaـvalue != 0) ? (float) $company->criteriaـvalue : 1.0;

        if ($company->rate != 0) {
            $crit = sprintf('%.6F', $criteria);
            $rate = sprintf('%.6F', (float) $company->rate / 100);
            $maxPrice = "CASE WHEN maxPrice >= $crit THEN ROUND(maxPrice * $rate + maxPrice) ELSE maxPrice END AS maxPrice";
            $minPrice = "CASE WHEN maxPrice >= $crit THEN ROUND(minPrice * $rate + minPrice) ELSE minPrice END AS minPrice";
        } else {
            $maxPrice = 'maxPrice';
            $minPrice = 'minPrice';
        }

        // الفرع: من يرى كل الفروع
        $branchCondition = ($identity->seeOtherBranchQ == 0)
            ? ['stocks.branch' => (int) $identity->branch]
            : ['in', 'stocks.branch', $this->allBranchIds()];

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q)) {
            // نحدد طول النص المدخل بـ 100 حرف
            $q = mb_substr((string)$q, 0, 100);

            // إذا كان النص فارغاً تماماً (بدون مسافات)، نرجع مصفوفة فارغة
            if ($q === '') {
                $out['results'] = [];
                return $out;
            }

            // تحويل المسافات إلى % للبحث المفتوح
            $qSearch = str_replace(' ', '%', trim($q));

            // إذا قام المستخدم بإدخال مسافات فقط، نجعل قيمة البحث % لاسترجاع كل الأصناف
            if ($qSearch === '') {
                $qSearch = '%';
            }

            // الأعمدة المتاحة للجميع
            $select = [
                'category.id',
                'category.name AS text',
                'company AS company',
                'stocks.quantity AS quantity',
                $maxPrice,
                'serialNo AS serialNo',
                'place',
                'commCode',
                "CASE WHEN `type` = 1 THEN 'متوفر' WHEN `type` = 2 THEN 'متوفر' ELSE 'قريبا' END AS type",
                'branches.name AS BRNAME',
                'branches.id AS branchId',
            ];

            // الموظف فقط يرى السعر الأدنى، ومن يملك الصلاحية يرى التكلفة
            if (!$isClient) {
                $select[] = $minPrice;
                if ($identity->seeCostPrice == 1) {
                    $select[] = 'costPrice AS costPrice';
                }
            }

            $query = (new Query())
                ->select($select)
                ->from('stocks')
                ->leftJoin('category', 'stocks.category = category.id')
                ->leftJoin('prices', 'prices.category = category.id')
                ->leftJoin('branches', 'branches.id = stocks.branch')
                ->where([
                    'or',
                    ['like', 'category.name', '%' . $qSearch . '%', false],
                    ['like', "REPLACE(serialNo, '-', '')", str_replace('-', '', $qSearch)],
                    ['like', 'commCode', $qSearch],
                    ['like', 'category.id', $qSearch],
                    ['like', 'place', $qSearch],
                    ['like', 'company', $qSearch],
                ])
                ->andWhere($branchCondition)
                ->limit(60)
                ->orderBy(['category.id' => SORT_ASC]);

            // شرط عرض/إخفاء الأصناف ذات الرصيد الصفري بناءً على صلاحيات الشركة
            if ($company->zeroQnty == 0) {
                $query->andWhere(['<>', 'stocks.quantity', 0]);
            } else {
                // إذا كانت صلاحية الرصيد الصفري مفعلة، نستبعد القيم الفارغة (NULL) فقط ونعرض الكل (بما في ذلك 0)
                $query->andWhere(['not', ['stocks.quantity' => null]]);
            }

            $out['results'] = array_values($query->createCommand()->queryAll());
        }
        // elseif ($id > 0) {
        //     $item = TotalInventory::findOne((int)$id);
        //     if ($item !== null) {
        //         $result = [
        //             'id' => (int) $id,
        //             'text' => $item->name,
        //             'company' => $item->company,
        //             'quantity' => $item->quantity,
        //             'maxPrice' => $item->maxPrice,
        //             'serialNo' => $item->serialNo,
        //             'type' => $item->type,
        //             'commCode' => $item->commCode,
        //         ];
        //         if (!$isClient) {
        //             $result['minPrice'] = $item->minPrice;
        //         }
        //         $out['results'] = $result;
        //     }
        // }

        return $out;
    }

    public function actionBackCreate($client = null)
    {
        $model = new Sales();
        $model->payWay = 1;
        $identity = Yii::$app->user->identity;
        $userId = (int) $identity->id;

        if (Yii::$app->request->post($model->formName()) !== null) {
            $this->loadSalesForm($model, ['type', 'disscount', 'paid']);

            if (Client::find()->where(['id' => $model->clinet])->one() === null) {
                Yii::$app->session->setFlash('error', 'اختر العميل.');
                return $this->redirect(['temp-back-sales/create']);
            }
            if ($identity->client <> null && !in_array((string) $model->clinet, array_map('trim', explode(',', (string) $identity->client)), true)) {
                Yii::$app->session->setFlash('error', 'هذا العميل غير مسموح لك.');
                return $this->redirect(['temp-back-sales/create']);
            }

            $rows = TempBackSales::find()->where(['created_by' => $userId, 'state' => 1])->all();
            if (!$rows) {
                Yii::$app->session->setFlash('error', Yii::t('app', "عفوا الفاتورة لاتحتوي على اصناف الرجاء الغاءها"));
                return $this->redirect(['temp-back-sales/create']);
            }

            $model->total = TempBackSales::find()->where(['created_by' => $userId, 'state' => 1])->sum('salePrice * quantity');
            $model->type = 2;
            $model->disscount = 0;
            $model->paid = 0;

            try {
                $id = $this->withStockLock(function () use ($model, $rows, $identity, $userId) {
                    $branch = (int) $identity->branch;
                    $model->branch = $branch;
                    $model->billId = (int) Sales::find()->where(['=', 'type', $model->type])->max('billId') + 1;
                    $model->created_at = date('Y-m-d H:i:s');
                    $model->user_insert = Yii::$app->user->id;
                    if (!$model->save()) {
                        throw new \DomainException($this->firstError($model));
                    }
                    $id = $model->id;

                    foreach ($rows as $data) {
                        $modelDetails = new SalesDetails();
                        $modelDetails->salesId = $id;
                        $modelDetails->category = $data->category;
                        $modelDetails->quantity = $data->quantity;
                        $modelDetails->costPrice = $data->costPrice;
                        $modelDetails->salePrice = $data->salePrice;
                        $modelDetails->box = $data->box;
                        $modelDetails->expire = $data->expire;
                        $modelDetails->original_price = $data->salePrice;
                        $modelDetails->serial_number = $data->serial_number;
                        $modelDetails->branch = $branch;
                        $modelDetails->save(false);
                        // المسترجع يعيد الكمية إلى مخزون فرع المستخدم
                        $this->applyDetail(2, $data->category, $branch, $data->quantity, false);
                    }
                    TempBackSales::deleteAll(['created_by' => $userId, 'state' => 1]);
                    return $id;
                });
            } catch (\DomainException $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect(['temp-back-sales/create']);
            }

            return $this->redirect(['print', 'id' => $id]);
        } elseif (Yii::$app->request->isAjax) {
            $model->at = date('Y-m-d');
            $model->clinet = ($client == null) ? 1 : $client;
            $model->total = TempBackSales::find()->where(['created_by' => $userId, 'state' => 1])
                ->sum('salePrice * quantity');
            $model->paid = 0;
            $model->disscount = 0;

            return $this->renderAjax('back', [
                'model' => $model,
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionSaveAsNew($oldId)
    {
        $old = $this->findModel($oldId);
        $company = CompanyInfo::find()->one();
        $identity = Yii::$app->user->identity;

        $model = new Sales();
        $model->payWay = 1;

        if (Yii::$app->request->post($model->formName()) !== null) {
            $this->loadSalesForm($model);
            $model->type = (int) $model->type;
            $back = ['view', 'id' => $old->id];

            if (!in_array($model->type, [1, 3, 4], true)) {
                Yii::$app->session->setFlash('error', 'نوع الفاتورة غير صحيح.');
                return $this->redirect($back);
            }
            if (Client::find()->where(['id' => $model->clinet])->one() === null) {
                Yii::$app->session->setFlash('error', 'اختر العميل.');
                return $this->redirect($back);
            }

            $details = SalesDetails::find()->where(['salesId' => $old->id])->all();
            if (!$details) {
                Yii::$app->session->setFlash('error', Yii::t('app', "عفوا الفاتورة لاتحتوي على اصناف الرجاء الغاءها"));
                return $this->redirect($back);
            }

            $model->total = SalesDetails::find()->where(['salesId' => $old->id])->sum('salePrice * quantity');
            if ($identity->makeDiscount != 1) {
                $model->disscount = 0;
            }
            $model->disscount = ($model->disscount == null) ? 0 : max(0, (float) $model->disscount);
            $model->paid = ($model->paid == null) ? 0 : max(0, (float) $model->paid);
            if ($identity->client <> null) {
                $model->paid = 0;
            }

            try {
                $id = $this->withStockLock(function () use ($model, $details, $company, $identity) {
                    $branch = (int) $identity->branch;
                    $model->branch = $branch;
                    $model->billId = (int) Sales::find()->where(['=', 'type', $model->type])->max('billId') + 1;
                    $model->created_at = date('Y-m-d H:i:s');
                    $model->user_insert = Yii::$app->user->id;
                    if (!$model->save()) {
                        throw new \DomainException($this->firstError($model));
                    }
                    $id = $model->id;

                    foreach ($details as $data) {
                        $modelDetails = new SalesDetails();
                        $modelDetails->salesId = $id;
                        $modelDetails->category = $data->category;
                        $modelDetails->waitQnty = $data->waitQnty;
                        $modelDetails->costPrice = $data->costPrice;
                        $modelDetails->salePrice = $data->salePrice;
                        $modelDetails->box = $data->box;
                        $modelDetails->expire = $data->expire;
                        $modelDetails->type = $data->type;
                        $modelDetails->original_price = $data->salePrice;
                        $modelDetails->serial_number = $data->serial_number;
                        $modelDetails->branch = $data->branch;
                        $modelDetails->quantity = ($model->type != 4)
                            ? $this->allowedQty($data->category, $data->quantity, [$model->type], $company)
                            : $data->quantity;
                        $modelDetails->save(false);
                        $this->applyDetail($model->type, $data->category, $branch, $modelDetails->quantity, false);
                    }
                    $this->recalcTotal($id);
                    return $id;
                });
            } catch (\DomainException $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect($back);
            }

            return $this->redirect(['print', 'id' => $id]);
        } elseif (Yii::$app->request->isAjax) {
            $model->at = date('Y-m-d');
            $model->clinet = 1;
            $model->total = SalesDetails::find()->where(['salesId' => $old->id])
                ->sum('salePrice * quantity');
            $model->paid = 0;
            $model->disscount = 0;

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
     * ينقل سطراً من فاتورة محفوظة إلى قائمة الفاتورة المؤقتة للمستخدم ويعيد كميته للمخزون.
     * @return array [رقم الفاتورة، هل حُذفت الفاتورة لأنها فرغت]
     */
    private function moveDetailToTemp($detailId)
    {
        $detail = SalesDetails::findOne((int) $detailId);
        if ($detail === null) {
            throw new \DomainException('السطر غير موجود.');
        }
        $sale = $this->findModel($detail->salesId);
        $this->ownBranchOrFail($sale);
        if ($sale->type == 2) {
            throw new \DomainException('لا يمكن نقل سطر مسترجع إلى فاتورة بيع.');
        }

        return $this->withStockLock(function () use ($sale, $detail) {
            $temp = new TempInvoice();
            $temp->category = $detail->category;
            $temp->quantity = $detail->quantity;
            $temp->costPrice = $detail->costPrice;
            $temp->salePrice = $detail->salePrice;
            $temp->box = $detail->box;
            $temp->expire = $detail->expire;
            $temp->type = $detail->type;
            $temp->branch = $detail->branch;
            $temp->waitQnty = $detail->waitQnty;
            $temp->state = 1;
            $temp->created_by = Yii::$app->user->identity->id;
            $temp->save(false);

            $this->applyDetail($sale->type, $detail->category, $this->saleBranch($sale), $detail->quantity, true);
            SalesDetails::deleteAll(['id' => $detail->id]);

            $total = $this->recalcTotal($sale->id);
            if ($total === null) {
                Sales::deleteAll(['id' => $sale->id]);
                return [$sale->id, true];
            }
            return [$sale->id, false];
        });
    }

    public function actionTransferToTempInvoice($id)
    {
        try {
            list($salesId, $deleted) = $this->moveDetailToTemp($id);
        } catch (\DomainException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }

        return $deleted
            ? $this->redirect(['temp-invoice/create'])
            : $this->redirect(['update', 'id' => $salesId]);
    }

    /**
     * إضافة صنف (كمية 1) إلى فاتورة محفوظة من مربع البحث في شاشة التعديل. POST فقط.
     * الأسعار من قاعدة البيانات، والمخزون يتأثر من PHP حسب نوع الفاتورة، والتكرار حسب repeatCategory.
     */
    public function actionAddItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $categoryId = (int) Yii::$app->request->post('categoryid');
        $salesId = (int) Yii::$app->request->post('salesId');

        try {
            $sale = $this->findModel($salesId);
            $this->ownBranchOrFail($sale);
            $company = CompanyInfo::find()->one();

            $message = $this->withStockLock(function () use ($sale, $company, $categoryId) {
                $category = Category::findOne($categoryId);
                $prices = Prices::find()->where(['category' => $categoryId])->one();
                if ($category === null || $prices === null) {
                    throw new \DomainException('الصنف غير موجود أو لا يوجد له سعر.');
                }

                $exists = SalesDetails::find()->where(['salesId' => $sale->id, 'category' => $categoryId])->exists();
                if ($exists && $company->repeatCategory == 0) {
                    throw new \DomainException(Yii::t('app', "Sorry The Item You Insert already exists"));
                }

                // يخصم المخزون فقط للنهائية والحجز، ويُقيَّد بالمتاح إلا إن كان البيع بلا رصيد مفعلاً
                if (in_array((int) $sale->type, [1, 3], true) && $company->zeroQnty == 0
                    && $this->allowedQty($categoryId, 1, [(int) $sale->type], $company) <= 0) {
                    throw new \DomainException(Yii::t('app', "عفوا لقد تجاوزت الكمية الموجودة لايمكنك الاستمرار"));
                }

                $detail = new SalesDetails();
                $detail->salesId = $sale->id;
                $detail->category = $categoryId;
                $detail->quantity = 1;
                $detail->salePrice = $this->defaultSalePrice($prices, $company);
                $detail->original_price = $detail->salePrice;
                $detail->costPrice = $prices->costPrice;
                $detail->box = 1;
                $detail->branch = $this->saleBranch($sale);
                $detail->save(false);

                $this->applyDetail($sale->type, $categoryId, $this->saleBranch($sale), 1, false);
                $this->recalcTotal($sale->id);
                return '';
            });
        } catch (\DomainException $e) {
            Yii::$app->response->statusCode = 400;
            return ['ok' => false, 'message' => $e->getMessage()];
        } catch (NotFoundHttpException $e) {
            Yii::$app->response->statusCode = 404;
            return ['ok' => false, 'message' => 'الفاتورة غير موجودة.'];
        }

        return ['ok' => true];
    }

    public function actionFast($id)
    {
        $sale = $this->findModel($id);
        $model = new Sales();
        $dataProvider = new ActiveDataProvider([
            'query' => SalesDetails::find()->where(['salesId' => $sale->id]),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ],
            'pagination' => [
                'pageSize' => 100
            ],
        ]);

        if ($model->load(Yii::$app->request->post())) {
            $selection = Yii::$app->request->post('selection');
            if (is_array($selection)) {
                foreach ($selection as $select) {
                    try {
                        $detail = SalesDetails::findOne((int) $select);
                        // لا ينقل إلا أسطر هذه الفاتورة
                        if ($detail === null || (int) $detail->salesId !== (int) $sale->id) {
                            continue;
                        }
                        list(, $deleted) = $this->moveDetailToTemp($detail->id);
                        if ($deleted) {
                            break;
                        }
                    } catch (\DomainException $e) {
                        Yii::$app->session->setFlash('error', $e->getMessage());
                        break;
                    }
                }
            }
        }

        return $this->renderAjax('createFast', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionBackItems($id)
    {
        $model = SalesDetails::find()->where(['=', 'id', $id])->one();
        if ($model === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        // يمرّ بفحص الفاتورة (مستخدم العميل لا يصل إلا لفواتير عملائه)
        $this->findModel($model->salesId);

        $tempInvoice = new TempBackSales();
        $tempInvoice->category = $model->category;
        $tempInvoice->quantity = $model->quantity;
        $tempInvoice->costPrice = $model->costPrice;
        $tempInvoice->salePrice = $model->salePrice;
        $tempInvoice->box = $model->box;
        $tempInvoice->state = 1;
        $tempInvoice->created_by = Yii::$app->user->identity->id;
        $tempInvoice->created_at = date('Y-m-d H:i:s');
        $tempInvoice->save(false);

        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }

    public function actionDone($id)
    {
        Sales::updateAll(['deserving' => null,], ['=', 'id', $id]);

        return $this->redirect(['deserving']);
    }

    public function actionWaitQnty()
    {
        $searchModel = new SalesDetailsSearchWQ();
        $searchModel->commCode = '99';
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('waitByCat', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionWaitQntyClient()
    {
        $searchModel = new SalesDetailsSearchWQClient();
        $searchModel->commCode = '99';
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('waitByCatClient', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDelev($id)
    {
        Sales::updateAll(['deleviried' => 1, 'deleviryAt' => date('Y-m-d')], ['=', 'id', $id]);

        Yii::$app->session->setFlash('success', Yii::t('app', "تمت عملية تغيير حالة الفاتورة بنجاح"));

        return $this->redirect(['print', 'id' => $id]);
    }

    public function actionStopCredit($id)
    {
        Client::updateAll(['post_paid' => 0], ['=', 'id', $id]);

        Yii::$app->session->setFlash('success', Yii::t('app', "تمت عملية ايقاف التعامل بالدين بنجاح"));

        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }

}
