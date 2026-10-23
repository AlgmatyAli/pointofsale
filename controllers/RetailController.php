<?php

namespace app\controllers;

use Yii;
use app\components\SalesStockTrait;
use app\models\Category;
use app\models\Client;
use app\models\CompanyInfo;
use app\models\Dept;
use app\models\Prices;
use app\models\Sales;
use app\models\SalesDetails;
use app\models\TempInvoice;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * شاشة نقطة البيع (التجزئة: بقالة + صيدلية).
 *
 * القواعد:
 *  - كل عمليات التغيير POST، وكل سطر يُعدَّل يجب أن يكون في فاتورة المستخدم الحالية (state = 1، created_by).
 *  - الأسعار والتكلفة من جدول prices على الخادم (السعر الافتراضي بنسبة rate والتقريب)، ولا يصل للمتصفح سعر التكلفة ولا الحد الأدنى.
 *  - مسح الباركود: تكرار نفس الصنف يزيد كمية السطر نفسه (بغض النظر عن repeatCategory)، و«3*باركود» تضيف 3.
 *  - الرصيد: يُقيَّد بالمتاح إلا إن كان البيع بلا رصيد مفعلاً (zeroQnty != 0) كما في الفاتورة المؤقتة.
 *  - الحفظ النهائي: نفس منطق SalesController::actionCreate (قفل + معاملة، الخصم من فرع المستخدم، سقف الدين) للنوع 1 فقط.
 *  - مستخدم العميل (حقل client غير فارغ) لا يدخل هذه الشاشة.
 *  - باركود الميزان: مؤجل (انظر parseScaleBarcode).
 */
class RetailController extends Controller
{
    use SalesStockTrait;

    const HOLD_LOCK = 'pos_temp_invoice_hold';

    /** رقم العميل النقدي الافتراضي (يمكن تغييره من params['retailDefaultClient']) */
    private function defaultClientId()
    {
        return (int) (Yii::$app->params['retailDefaultClient'] ?? 1);
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'scan' => ['POST'],
                    'add' => ['POST'],
                    'qty' => ['POST'],
                    'price' => ['POST'],
                    'minus' => ['POST'],
                    'delete' => ['POST'],
                    'delete-all' => ['POST'],
                    'hold' => ['POST'],
                    'unhold' => ['POST'],
                    'checkout' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['createSales'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        // شاشة الكاشير للموظفين فقط، لا لمستخدم العميل
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->client !== null && Yii::$app->user->identity->client !== '') {
            throw new ForbiddenHttpException('هذه الشاشة غير متاحة لهذا المستخدم.');
        }
        return true;
    }

    // =====================================================================
    // الشاشة
    // =====================================================================

    public function actionIndex()
    {
        return $this->redirect(['create']);
    }

    public function actionCreate()
    {
        $this->layout = 'pos';
        $company = CompanyInfo::current();
        $identity = Yii::$app->user->identity;

        $classes = (new Query())->select('class')->from('category')
            ->where(['qShow' => 1, 'status' => 0])->distinct()->orderBy('class')->column();

        $paymentTypes = (new Query())->select(['id', 'name'])->from('payment_types')->orderBy('id')->all();

        $defaultClient = Client::findOne($this->defaultClientId());

        return $this->render('create', [
            'company' => $company,
            'classes' => $classes,
            'paymentTypes' => $paymentTypes,
            'defaultClient' => $defaultClient,
            'canDiscount' => (int) $identity->makeDiscount === 1,
            'canEditPrice' => (int) $identity->editSalePrice === 1,
            'cart' => $this->cartPayload(),
        ]);
    }

    // =====================================================================
    // قراءة: الأصناف والعملاء والسلة
    // =====================================================================

    /** الأصناف للشبكة والبحث بالاسم/الباركود. لا يُرجع تكلفة ولا حداً أدنى */
    public function actionProducts($q = '', $class = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $company = CompanyInfo::current();
        $branch = (int) Yii::$app->user->identity->branch;

        $query = (new Query())
            ->select([
                'c.id', 'c.name', 'c.serialNo', 'c.company', 'c.path',
                'maxPrice' => 'MAX(p.maxPrice)',
                'qty' => 'COALESCE(SUM(s.quantity), 0)',
            ])
            ->from(['c' => 'category'])
            ->innerJoin(['p' => 'prices'], 'p.category = c.id')
            ->leftJoin(['s' => 'stocks'], 's.category = c.id AND s.type = 1 AND s.branch = :b', [':b' => $branch])
            ->where(['c.status' => 0, 'c.qShow' => 1])
            ->groupBy('c.id')
            ->orderBy('c.name')
            ->limit(60);

        if ($company->zeroQnty == 0) {
            $query->having(['>', 'COALESCE(SUM(s.quantity), 0)', 0]);
        }

        $class = trim((string) $class);
        if ($class !== '') {
            $query->andWhere(['c.class' => $class]);
        }
        $q = trim(mb_substr((string) $q, 0, 100));
        if ($q !== '') {
            $like = '%' . strtr($q, ['\\' => '\\\\', '%' => '\\%', '_' => '\\_']) . '%';
            $query->andWhere(['or', ['like', 'c.name', $like, false], ['like', 'c.serialNo', $like, false]]);
        }

        $out = [];
        foreach ($query->all() as $r) {
            $out[] = [
                'id' => (int) $r['id'],
                'name' => $r['name'],
                'serialNo' => $r['serialNo'],
                'company' => $r['company'],
                'path' => $r['path'],
                'qty' => (float) $r['qty'],
                'price' => $this->defaultSalePrice((object) ['maxPrice' => $r['maxPrice']], $company),
            ];
        }
        return $out;
    }

    /** عملاء الأجل: بالاسم أو الهاتف، ضمن العملاء المسموحين للمستخدم */
    public function actionClients($q = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $q = trim(mb_substr((string) $q, 0, 100));
        if ($q === '') {
            return [];
        }
        $like = '%' . strtr($q, ['\\' => '\\\\', '%' => '\\%', '_' => '\\_']) . '%';
        $rows = (new Query())->select(['id', 'name', 'phone', 'mobile', 'post_paid'])->from('client')
            ->where(['post_paid' => 1])
            ->andWhere(['or', ['like', 'name', $like, false], ['like', 'phone', $like, false], ['like', 'mobile', $like, false]])
            ->orderBy('name')->limit(15)->all();
        return array_map(function ($r) {
            return ['id' => (int) $r['id'], 'name' => $r['name'], 'phone' => $r['mobile'] ?: $r['phone']];
        }, $rows);
    }

    public function actionCart()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success' => true, 'cart' => $this->cartPayload()];
    }

    // =====================================================================
    // تعديل السلة (كلها POST)
    // =====================================================================

    /** مسح باركود: «باركود» أو «كمية*باركود» */
    public function actionScan()
    {
        return $this->respond(function ($company) {
            $raw = trim((string) Yii::$app->request->post('barcode'));
            if ($raw === '') {
                throw new \DomainException('الباركود فارغ.');
            }
            $qty = 1.0;
            if (preg_match('/^(\d+(?:\.\d+)?)\*(.+)$/u', $raw, $m)) {
                $qty = (float) $m[1];
                $raw = trim($m[2]);
            }

            $scale = $this->parseScaleBarcode($raw);
            if ($scale !== null) {
                $categoryId = $scale['category'];
                $qty = $scale['qty'];
            } else {
                $categoryId = (int) (new Query())->select('id')->from('category')
                    ->where(['serialNo' => $raw, 'status' => 0])->scalar();
            }
            if ($categoryId <= 0) {
                throw new \DomainException('الباركود غير مسجل: ' . $raw);
            }
            return $this->addToCart($categoryId, $qty, $company);
        });
    }

    /** إضافة صنف بالنقر على بطاقته */
    public function actionAdd()
    {
        return $this->respond(function ($company) {
            $qty = (float) Yii::$app->request->post('qty', 1);
            return $this->addToCart((int) Yii::$app->request->post('categoryid'), $qty, $company);
        });
    }

    /** تعيين كمية سطر (تُقلَّص للمتاح) */
    public function actionQty()
    {
        return $this->respond(function ($company) {
            $row = $this->findRow(Yii::$app->request->post('id'));
            $qty = Yii::$app->request->post('qty');
            if (!is_numeric($qty) || (float) $qty <= 0) {
                throw new \DomainException('الكمية يجب أن تكون أكبر من صفر.');
            }
            $qty = round((float) $qty, 3);
            $msg = '';
            if ($company->zeroQnty == 0) {
                $balance = $this->visibleStock($row->category) - $this->enteredQty($row->category, $row->id);
                if ($balance <= 0) {
                    throw new \DomainException('عفوا لقد تجاوزت الكمية الموجودة لايمكنك الاستمرار');
                }
                if ($qty > $balance) {
                    $qty = $balance;
                    $msg = 'تم تقليص الكمية إلى المتاح (' . $this->fmtQty($balance) . ').';
                }
            }
            $row->quantity = $qty;
            $row->save(false);
            return $msg;
        });
    }

    /** تعديل سعر سطر: لمن يملك editSalePrice، ولا ينزل عن التكلفة */
    public function actionPrice()
    {
        return $this->respond(function ($company) {
            if (Yii::$app->user->identity->editSalePrice != 1) {
                throw new \DomainException('لايوجد لديك صلاحية لتعديل سعر البيع');
            }
            $row = $this->findRow(Yii::$app->request->post('id'));
            $price = Yii::$app->request->post('price');
            if (!is_numeric($price) || (float) $price <= 0) {
                throw new \DomainException('السعر يجب أن يكون أكبر من صفر.');
            }
            $price = (float) $price;
            $msg = '';
            $prices = Prices::find()->where(['category' => $row->category])->one();
            if ($prices !== null && $price < (float) $prices->costPrice) {
                $price = (float) $prices->costPrice;
                $msg = 'السعر أقل من التكلفة فتم وضع سعر التكلفة.';
            }
            $row->salePrice = $price;
            $row->save(false);
            return $msg;
        });
    }

    public function actionMinus()
    {
        return $this->respond(function ($company) {
            $row = $this->findRow(Yii::$app->request->post('id'));
            if ((float) $row->quantity > 1) {
                $row->quantity = (float) $row->quantity - 1;
                $row->save(false);
            } else {
                $row->delete();
            }
            return '';
        });
    }

    public function actionDelete()
    {
        return $this->respond(function ($company) {
            $this->findRow(Yii::$app->request->post('id'))->delete();
            return '';
        });
    }

    public function actionDeleteAll()
    {
        return $this->respond(function ($company) {
            TempInvoice::deleteAll(['state' => 1, 'created_by' => (int) Yii::$app->user->identity->id]);
            return '';
        });
    }

    // =====================================================================
    // التعليق
    // =====================================================================

    public function actionHold()
    {
        return $this->respond(function ($company) {
            $userId = (int) Yii::$app->user->identity->id;
            $db = Yii::$app->db;
            if (!TempInvoice::find()->where(['state' => 1, 'created_by' => $userId])->exists()) {
                throw new \DomainException('لا توجد أصناف في الفاتورة الحالية لتعليقها.');
            }
            $got = (int) $db->createCommand('SELECT GET_LOCK(:n, 5)', [':n' => self::HOLD_LOCK])->queryScalar() === 1;
            if (!$got) {
                throw new \DomainException('النظام مشغول. أعد المحاولة.');
            }
            try {
                $number = (int) TempInvoice::find()->max('invoice_number') + 1;
                TempInvoice::updateAll(['state' => 2, 'invoice_number' => $number], ['state' => 1, 'created_by' => $userId]);
            } finally {
                $db->createCommand('SELECT RELEASE_LOCK(:n)', [':n' => self::HOLD_LOCK])->queryScalar();
            }
            return 'تم تعليق الفاتورة.';
        });
    }

    public function actionHolded()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->holdedList();
    }

    public function actionUnhold()
    {
        return $this->respond(function ($company) {
            $userId = (int) Yii::$app->user->identity->id;
            if (TempInvoice::find()->where(['state' => 1, 'created_by' => $userId])->exists()) {
                throw new \DomainException('هناك فاتورة حالية، أنهها أو علّقها أولاً.');
            }
            $n = TempInvoice::updateAll(
                ['state' => 1],
                ['state' => 2, 'created_by' => $userId, 'invoice_number' => (int) Yii::$app->request->post('number')]
            );
            if ($n === 0) {
                throw new \DomainException('الفاتورة المعلّقة غير موجودة.');
            }
            return '';
        });
    }

    // =====================================================================
    // الدفع والحفظ النهائي
    // =====================================================================

    public function actionCheckout()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $identity = Yii::$app->user->identity;
        $userId = (int) $identity->id;
        $company = CompanyInfo::current();
        $req = Yii::$app->request;
        $fail = function ($m) {
            return ['success' => false, 'message' => $m];
        };

        $payWay = (int) $req->post('payWay', 0);
        if (!in_array($payWay, [0, 1], true)) {
            return $fail('طريقة الدفع غير صحيحة.');
        }

        $clientId = (int) $req->post('clinet');
        if ($payWay === 1 && $clientId <= 0) {
            return $fail('اختر العميل.');
        }
        if ($clientId <= 0) {
            $clientId = $this->defaultClientId();
        }
        $client = Client::findOne($clientId);
        if ($client === null) {
            return $fail($payWay === 1 ? 'اختر العميل.' : 'العميل النقدي الافتراضي غير موجود، راجع إعدادات الشاشة.');
        }
        if ($client->post_paid != 1 && $payWay !== 0) {
            return $fail(Yii::t('app', "عفوا هذا الزبون لايمكن البيع له بالآجل"));
        }

        $rows = TempInvoice::find()->where(['created_by' => $userId, 'state' => 1])->all();
        if (!$rows) {
            return $fail('الفاتورة لا تحتوي على أصناف.');
        }

        $total = (float) TempInvoice::find()->where(['created_by' => $userId, 'state' => 1])->sum('salePrice * quantity');

        $discount = ($identity->makeDiscount == 1) ? max(0, (float) $req->post('disscount', 0)) : 0.0;
        if ($discount > $total) {
            return $fail('الخصم لا يمكن أن يزيد عن إجمالي الفاتورة.');
        }
        $net = $total - $discount;

        // النقدي: لا مدفوع مسجل (كما في الشاشة العادية). الآجل: المدفوع دفعة مقدمة ولا يزيد عن الصافي
        $paid = 0.0;
        if ($payWay === 1) {
            $paid = max(0, (float) $req->post('paid', 0));
            if ($paid > $net) {
                return $fail('المدفوع لا يمكن أن يزيد عن صافي الفاتورة.');
            }
        }

        // سقف الدين
        $overDebt = false;
        if ($payWay === 1 && (float) $client->debt != 0) {
            $balance = (float) Dept::find()->where(['id' => $clientId])->sum('credt');
            if ($balance + ($net - $paid) >= (float) $client->debt) {
                if (Yii::$app->user->can('userCansellOverDebt')) {
                    $overDebt = true;
                } else {
                    return $fail(Yii::t('app', "Sorry The Client Cross The Line About Debt"));
                }
            }
        }

        $paymentType = (int) $req->post('payment_type', 1);
        if (!(new Query())->from('payment_types')->where(['id' => $paymentType])->exists()) {
            $paymentType = 1;
        }
        $currency = (int) \app\models\Currancy::find()->select('id')->orderBy('id')->scalar();

        $notes = [];
        try {
            $id = $this->withStockLock(function () use ($rows, $company, $identity, $userId, $clientId, $payWay, $discount, $paid, $paymentType, $currency, &$notes) {
                $branch = (int) $identity->branch;
                $model = new Sales();
                $model->type = 1;
                $model->at = date('Y-m-d');
                $model->clinet = $clientId;
                $model->payWay = $payWay;
                $model->branch = $branch;
                $model->total = 0;
                $model->disscount = $discount;
                $model->paid = $paid;
                $model->payment_type = $paymentType;
                $model->currancy = $currency ?: null;
                $model->wholesale = 0;
                $model->deleviried = ($company->invoiceState == 1) ? 1 : 0;
                $model->billId = (int) Sales::find()->where(['type' => 1])->max('billId') + 1;
                $model->created_at = date('Y-m-d H:i:s');
                $model->user_insert = $userId;
                $model->save(false);

                $saved = 0;
                $skipped = [];
                $reduced = [];
                foreach ($rows as $data) {
                    $qty = $this->allowedQty($data->category, $data->quantity, [1], $company);
                    if ($qty <= 0) {
                        $skipped[] = $data->category;
                        continue;
                    }
                    if ($qty < (float) $data->quantity) {
                        $reduced[] = $data->category;
                    }
                    $d = new SalesDetails();
                    $d->salesId = $model->id;
                    $d->category = $data->category;
                    $d->quantity = $qty;
                    $d->waitQnty = $data->waitQnty;
                    $d->costPrice = $data->costPrice;
                    $d->salePrice = $data->salePrice;
                    $d->original_price = $data->salePrice;
                    $d->box = $data->box;
                    $d->expire = $data->expire;
                    $d->type = $data->type;
                    $d->serial_number = $data->serial_number;
                    $d->branch = $data->branch;
                    $d->save(false);
                    $this->applyDetail(1, $data->category, $branch, $qty, false);
                    $saved++;
                }
                if ($saved === 0) {
                    throw new \DomainException('لا يوجد رصيد متاح لأي صنف في الفاتورة، لم يتم الحفظ.');
                }
                if ($skipped) {
                    $notes[] = 'أصناف حُذفت لعدم توفر رصيد (أرقامها): ' . implode('، ', $skipped);
                }
                if ($reduced) {
                    $notes[] = 'أصناف قُلّصت كمياتها للمتاح (أرقامها): ' . implode('، ', $reduced);
                }

                $sum = (float) SalesDetails::find()->where(['salesId' => $model->id])->sum('salePrice * quantity');
                // الخصم لا يزيد عن الإجمالي الفعلي بعد التقليص
                if ($model->disscount > $sum) {
                    $model->disscount = $sum;
                    $model->save(false);
                }
                Sales::updateAll(['total' => $sum, 'disscount' => $model->disscount], ['id' => $model->id]);
                TempInvoice::deleteAll(['created_by' => $userId, 'state' => 1]);
                return $model->id;
            });
        } catch (\DomainException $e) {
            return $fail($e->getMessage());
        }

        if ($overDebt) {
            $notes[] = 'عفوا هذا العميل تجاوز سقف الديون ! الرجاء المراجعة';
        }
        return [
            'success' => true,
            'id' => $id,
            'receiptUrl' => Url::to(['receipt', 'id' => $id, 'print' => 1]),
            'notes' => $notes,
            'cart' => $this->cartPayload(),
        ];
    }

    // =====================================================================
    // الإيصال (80mm)
    // =====================================================================

    public function actionReceipt($id, $print = 0)
    {
        $this->layout = false;
        $identity = Yii::$app->user->identity;
        $sale = Sales::findOne((int) $id);
        if ($sale === null) {
            throw new NotFoundHttpException('الفاتورة غير موجودة.');
        }
        if ($sale->branch != $identity->branch && !Yii::$app->user->can('can_see_other_branch_sales')) {
            throw new ForbiddenHttpException('لا تملك صلاحية عرض هذه الفاتورة.');
        }
        if ($sale->user_insert != $identity->id && !Yii::$app->user->can('userCanSeeOtherUsersSales')) {
            throw new ForbiddenHttpException('لا تملك صلاحية عرض هذه الفاتورة.');
        }

        $lines = (new Query())
            ->select(['c.name', 'd.quantity', 'd.salePrice'])
            ->from(['d' => 'salesDetails'])
            ->innerJoin(['c' => 'category'], 'c.id = d.category')
            ->where(['d.salesId' => $sale->id])->orderBy('d.id')->all();

        $client = Client::findOne($sale->clinet);
        $balance = ($sale->payWay == 1) ? (float) Dept::find()->where(['id' => $sale->clinet])->sum('credt') : null;

        return $this->render('receipt', [
            'sale' => $sale,
            'lines' => $lines,
            'company' => CompanyInfo::current(),
            'client' => $client,
            'isDefaultClient' => ((int) $sale->clinet === $this->defaultClientId()),
            'balance' => $balance,
            'print' => (int) $print === 1,
            'user' => (new Query())->select('username')->from('user')->where(['id' => $sale->user_insert])->scalar(),
        ]);
    }

    // =====================================================================
    // أدوات داخلية
    // =====================================================================

    /**
     * باركود الميزان (البادئة 2 + كود الصنف + الوزن + خانة التحقق): مؤجل إلى حين اعتماد جدول الباركودات المتعددة وضبط صيغة ميزان رونجتا.
     * عند التفعيل تعيد: ['category' => رقم الصنف، 'qty' => الوزن بالكيلو]، وإلا null.
     */
    private function parseScaleBarcode($code)
    {
        return null;
    }

    /** ينفّذ عملية على السلة ويعيد JSON موحداً: success / message / cart */
    private function respond(callable $fn)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $company = CompanyInfo::current();
        try {
            $message = $fn($company);
        } catch (\DomainException $e) {
            return ['success' => false, 'message' => $e->getMessage(), 'cart' => $this->cartPayload()];
        }
        return ['success' => true, 'message' => (string) $message, 'cart' => $this->cartPayload()];
    }

    private function findRow($id)
    {
        $row = TempInvoice::findOne([
            'id' => (int) $id,
            'created_by' => (int) Yii::$app->user->identity->id,
            'state' => 1,
        ]);
        if ($row === null) {
            throw new \DomainException('السطر غير موجود.');
        }
        return $row;
    }

    /** أنواع المخزون التي يُباع منها: العادي دائماً، والمعلّق (3) لمن يملك saleOnHoldItems */
    private function saleStockTypes()
    {
        return Yii::$app->user->can('saleOnHoldItems') ? [1, 3] : [1];
    }

    /** رصيد الصنف الذي يراه المستخدم: كل الفروع إن كان يملك seeOtherBranchQ، وإلا فرعه فقط */
    private function visibleStock($category)
    {
        $identity = Yii::$app->user->identity;
        $branches = ($identity->seeOtherBranchQ == 0) ? [(int) $identity->branch] : $this->allBranchIds();
        return (float) (new Query())->from('stocks')
            ->where(['category' => (int) $category])
            ->andWhere(['in', 'type', $this->saleStockTypes()])
            ->andWhere(['in', 'branch', $branches])
            ->sum('quantity');
    }

    /** مجموع ما أُدرج من الصنف في فاتورة المستخدم الحالية (مع استثناء سطر اختياري) */
    private function enteredQty($category, $exceptId = null)
    {
        $q = TempInvoice::find()->where([
            'category' => (int) $category,
            'created_by' => (int) Yii::$app->user->identity->id,
            'state' => 1,
        ]);
        if ($exceptId !== null) {
            $q->andWhere(['!=', 'id', (int) $exceptId]);
        }
        return (float) $q->sum('quantity');
    }

    private function fmtQty($q)
    {
        return rtrim(rtrim(number_format((float) $q, 3, '.', ''), '0'), '.');
    }

    /** يضيف كمية من صنف إلى السلة (يزيد السطر الموجود) بعد تطبيق قواعد الرصيد. يعيد رسالة تنبيه إن قُلّصت الكمية */
    private function addToCart($categoryId, $qty, $company)
    {
        $categoryId = (int) $categoryId;
        $qty = round((float) $qty, 3);
        if ($qty <= 0) {
            throw new \DomainException('الكمية يجب أن تكون أكبر من صفر.');
        }
        $category = Category::find()->where(['id' => $categoryId, 'status' => 0])->one();
        $prices = Prices::find()->where(['category' => $categoryId])->one();
        if ($category === null || $prices === null) {
            throw new \DomainException('الصنف غير موجود أو لا يوجد له سعر.');
        }

        $msg = '';
        if ($company->zeroQnty == 0) {
            $balance = $this->visibleStock($categoryId) - $this->enteredQty($categoryId);
            if ($balance <= 0) {
                throw new \DomainException('عفوا لقد تجاوزت الكمية الموجودة لايمكنك الاستمرار');
            }
            if ($qty > $balance) {
                $qty = $balance;
                $msg = 'تم تقليص الكمية إلى المتاح (' . $this->fmtQty($balance) . ').';
            }
        }

        $userId = (int) Yii::$app->user->identity->id;
        $row = TempInvoice::find()->where(['category' => $categoryId, 'created_by' => $userId, 'state' => 1])
            ->orderBy(['id' => SORT_ASC])->one();
        if ($row !== null) {
            $row->quantity = round((float) $row->quantity + $qty, 3);
            $row->save(false);
        } else {
            $row = new TempInvoice();
            $row->category = $categoryId;
            $row->quantity = $qty;
            $row->salePrice = $this->defaultSalePrice($prices, $company);
            $row->costPrice = $prices->costPrice;
            $row->waitQnty = 0;
            $row->branch = (int) Yii::$app->user->identity->branch;
            $row->box = 1;
            $row->type = 1;
            $row->state = 1;
            $row->created_by = $userId;
            $row->created_at = date('Y-m-d H:i:s');
            $row->save(false);
        }
        return $msg;
    }

    private function cartPayload()
    {
        $userId = (int) Yii::$app->user->identity->id;
        $rows = (new Query())
            ->select(['t.id', 't.category', 't.quantity', 't.salePrice', 'c.name', 'c.serialNo'])
            ->from(['t' => 'temp_invoice'])
            ->innerJoin(['c' => 'category'], 'c.id = t.category')
            ->where(['t.created_by' => $userId, 't.state' => 1])
            ->orderBy('t.id')->all();

        $items = [];
        $total = 0.0;
        foreach ($rows as $r) {
            $line = round((float) $r['quantity'] * (float) $r['salePrice'], 3);
            $total += $line;
            $items[] = [
                'id' => (int) $r['id'],
                'category' => (int) $r['category'],
                'name' => $r['name'],
                'serialNo' => $r['serialNo'],
                'qty' => (float) $r['quantity'],
                'price' => (float) $r['salePrice'],
                'total' => $line,
            ];
        }
        return [
            'items' => $items,
            'total' => round($total, 3),
            'count' => count($items),
            'holded' => count($this->holdedList()),
        ];
    }

    private function holdedList()
    {
        $rows = (new Query())
            ->select(['number' => 'invoice_number', 'total' => 'SUM(salePrice * quantity)', 'lines' => 'COUNT(*)'])
            ->from('temp_invoice')
            ->where(['state' => 2, 'created_by' => (int) Yii::$app->user->identity->id])
            ->groupBy('invoice_number')->orderBy(['invoice_number' => SORT_DESC])->limit(50)->all();
        return array_map(function ($r) {
            return ['number' => (int) $r['number'], 'total' => round((float) $r['total'], 3), 'lines' => (int) $r['lines']];
        }, $rows);
    }
}
