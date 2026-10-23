<?php

namespace app\controllers;

use Yii;
use app\models\TempInvoice;
use app\models\TempInvoiceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Category;
use app\models\Prices;
use yii\data\ActiveDataProvider;
use app\models\Stocks;
use yii\db\Query;
use yii\filters\AccessControl;
use app\models\CompanyInfo;
use yii\web\Response;

/**
 * TempInvoiceController: قائمة الفاتورة المؤقتة لكل مستخدم قبل الحفظ النهائي في SalesController.
 * هذه القائمة لا تحرّك المخزون؛ الخصم من المخزون يتم عند الحفظ النهائي فقط.
 *
 * حالة السطر (state): 1 = الفاتورة الحالية، 2 = فاتورة معلّقة (hold) ولها invoice_number.
 */
class TempInvoiceController extends Controller
{
    const HOLD_LOCK = 'pos_temp_invoice_hold';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'delete-all' => ['POST'],
                    'hold' => ['POST'],
                    'unhold' => ['POST'],
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
                            'delete',
                            'delete-all',
                            'hold',
                            'holded',
                            'unhold',
                            'fast',
                        ],
                        'roles' => ['createSales'],
                    ],
                ],
            ],
        ];
    }

    /**
     * شاشة الفاتورة المؤقتة: عرض الجدول، وإدراج صنف (POST من النموذج)، وتعديل الكمية/السعر/كمية الانتظار من الجدول.
     *
     * قواعد الإدراج:
     *  - التكلفة والأسعار تُؤخذ من جدول prices وليس من المتصفح؛
     *  - الكمية والسعر أكبر من صفر؛
     *  - تكرار الصنف حسب repeatCategory؛
     *  - إن لم يكن البيع بلا رصيد مفعلاً (zeroQnty = 0) تُقلَّص الكمية للمتاح مع رسالة (المتاح = رصيد الفروع التي يراها المستخدم
     *    ناقص ما أُدرج سابقاً من نفس الصنف)؛
     *  - سعر البيع الأقل من التكلفة يُرفع إلى التكلفة؛
     *  - الفرع للعرض فقط (الخصم الفعلي من فرع المستخدم عند الحفظ النهائي).
     */
    public function actionCreate()
    {
        $company = CompanyInfo::current();
        $model = new TempInvoice();
        $identity = Yii::$app->user->identity;
        $userId = (int) $identity->id;

        $searchModel = new TempInvoiceSearch();
        $searchModel->created_by = $userId;
        $searchModel->state = 1;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // التعديل المباشر داخل الجدول
        if (Yii::$app->request->post('hasEditable')) {
            return $this->inlineEdit($company);
        }

        // إدراج صنف
        if (is_array(Yii::$app->request->post($model->formName()))) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $this->insertItem($model, $company);
        }

        return $this->render('create', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'company' => $company,
        ]);
    }

    // =====================================================================
    // أدوات داخلية للفاتورة المؤقتة
    // =====================================================================

    /** أنواع المخزون التي يُباع منها: العادي دائماً، والمعلّق (3) لمن يملك saleOnHoldItems */
    private function saleStockTypes()
    {
        return Yii::$app->user->can('saleOnHoldItems') ? [1, 3] : [1];
    }

    private function allBranchIds()
    {
        static $ids = null;
        if ($ids === null) {
            $ids = array_map('intval', (new Query())->select('id')->from('branches')->column());
            if (!$ids) {
                $ids = [(int) Yii::$app->user->identity->branch];
            }
        }
        return $ids;
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
            'created_by' => Yii::$app->user->identity->id,
            'state' => 1,
        ]);
        if ($exceptId !== null) {
            $q->andWhere(['!=', 'id', (int) $exceptId]);
        }
        return (float) $q->sum('quantity');
    }

    private function jsonError($message)
    {
        return ['error' => true, 'success' => false, 'message' => $message];
    }

    /** سعر البيع الافتراضي للصنف كما تعرضه القائمة: نسبة الزيادة (rate) ثم التقريب، بنفس منطق الاستعلام والـ JavaScript */
    private function defaultSalePrice($prices, $company)
    {
        $price = (float) $prices->maxPrice;
        $criteria = ($company->criteriaـvalue != 0) ? (float) $company->criteriaـvalue : 1.0;
        if ($company->rate != 0 && $price >= $criteria) {
            $price = round($price * ((float) $company->rate / 100) + $price);
        }
        if ($company->criteriaـvalue != 0) {
            $v = (int) round($price);
            if ($v >= 10) {
                $d = $v % 10;
                if ($d !== 0 && $d < 5) {
                    $v += 5 - $d;
                } elseif ($d > 5) {
                    $v += 10 - $d;
                }
            }
            $price = $v;
        }
        return (float) $price;
    }

    /**
     * يضيف سطراً إلى الفاتورة الحالية بعد تطبيق كل القواعد (مشترك بين الإدراج العادي والسريع).
     * @param array $in category, quantity, salePrice, branch, waitQnty
     * @return array ['ok' => bool, 'message' => string, 'notes' => string[]]
     */
    private function addRow(array $in, $company)
    {
        $identity = Yii::$app->user->identity;
        $userId = (int) $identity->id;
        $fail = function ($m) {
            return ['ok' => false, 'message' => $m, 'notes' => []];
        };

        $categoryId = (int) $in['category'];
        if ($categoryId <= 0 || Category::findOne($categoryId) === null) {
            return $fail('اختر صنفاً من القائمة أولاً.');
        }

        // الأسعار والتكلفة من قاعدة البيانات، لا مما يرسله المتصفح
        $prices = Prices::find()->where(['category' => $categoryId])->one();
        if ($prices === null) {
            return $fail('لا يوجد سعر لهذا الصنف.');
        }

        $quantity = (float) $in['quantity'];
        $salePrice = (float) $in['salePrice'];
        if ($quantity <= 0) {
            return $fail(Yii::t('app', "Sorry You Enter Zero Value As Quantity"));
        }
        if ($salePrice <= 0) {
            return $fail(Yii::t('app', "Sorry You Enter Zero Value As Sale Price"));
        }

        // تكرار الصنف
        if ($company->repeatCategory == 0) {
            $exists = TempInvoice::find()
                ->where(['category' => $categoryId, 'created_by' => $userId, 'state' => 1])
                ->exists();
            if ($exists) {
                return $fail(Yii::t('app', "Sorry The Item You Insert already exists"));
            }
        }

        $notes = [];

        // الرصيد: يُمنع تجاوزه إلا إن كان البيع بلا رصيد مفعلاً
        if ($company->zeroQnty == 0) {
            $balance = $this->visibleStock($categoryId) - $this->enteredQty($categoryId);
            if ($balance <= 0) {
                return $fail(Yii::t('app', "عفوا لقد تجاوزت الكمية الموجودة لايمكنك الاستمرار"));
            }
            if ($quantity > $balance) {
                $quantity = $balance;
                $notes[] = 'تم تقليص الكمية إلى المتاح (' . $balance . ').';
            }
        }

        // سعر البيع لا ينزل عن التكلفة
        if ($salePrice < (float) $prices->costPrice) {
            $salePrice = (float) $prices->costPrice;
            $notes[] = 'سعر البيع أقل من التكلفة فتم وضع سعر التكلفة.';
        }

        // الفرع للعرض فقط: فرع المستخدم ما لم يملك رؤية الفروع الأخرى ويختار فرعاً موجوداً
        $branch = (int) $identity->branch;
        if ($identity->seeOtherBranchQ != 0) {
            $chosen = (int) $in['branch'];
            if ($chosen > 0 && in_array($chosen, $this->allBranchIds(), true)) {
                $branch = $chosen;
            }
        }

        $waitQnty = 0;
        if ($identity->client == null && $company->waitQnty == 1 && (float) $in['waitQnty'] > 0) {
            $waitQnty = (float) $in['waitQnty'];
        }

        // لا نكتب إلا ما اعتمدناه أعلاه
        $row = new TempInvoice();
        $row->category = $categoryId;
        $row->quantity = $quantity;
        $row->salePrice = $salePrice;
        $row->costPrice = $prices->costPrice;
        $row->waitQnty = $waitQnty;
        $row->branch = $branch;
        $row->box = 1;
        $row->state = 1;
        $row->created_by = $userId;
        $row->created_at = date('Y-m-d H:i:s');

        if (!$row->saveAll()) {
            $errors = $row->getFirstErrors();
            return $fail($errors ? reset($errors) : 'تعذر حفظ السطر.');
        }

        return ['ok' => true, 'message' => '', 'notes' => $notes];
    }

    /** @return array استجابة JSON لعملية الإدراج من النموذج */
    private function insertItem(TempInvoice $model, $company)
    {
        $userId = (int) Yii::$app->user->identity->id;
        $post = Yii::$app->request->post($model->formName());

        $r = $this->addRow([
            'category' => $post['category'] ?? 0,
            'quantity' => (isset($post['quantity']) && is_numeric($post['quantity'])) ? $post['quantity'] : 0,
            'salePrice' => (isset($post['salePrice']) && is_numeric($post['salePrice'])) ? $post['salePrice'] : 0,
            'branch' => $post['branch'] ?? 0,
            'waitQnty' => (isset($post['waitQnty']) && is_numeric($post['waitQnty'])) ? $post['waitQnty'] : 0,
        ], $company);

        if (!$r['ok']) {
            return $this->jsonError($r['message']);
        }

        $counter = (int) TempInvoice::find()
            ->where(['created_by' => $userId, 'state' => 1])
            ->count();
        Yii::$app->session->set('submitCounter', $counter);

        $result = ['success' => true, 'counter' => $counter];
        if ($r['notes']) {
            $result['message'] = implode(' ', $r['notes']);
        }
        return $result;
    }

    /**
     * التعديل المباشر لسطر في الجدول: الكمية أو سعر البيع أو كمية الانتظار.
     * السطر يجب أن يكون للمستخدم نفسه وفي فاتورته الحالية. الأخطاء تعود بحقل message ليعرضها Kartik.
     */
    private function inlineEdit($company)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $identity = Yii::$app->user->identity;

        $key = (int) Yii::$app->request->post('editableKey');
        $all = Yii::$app->request->post('TempInvoice');
        $posted = is_array($all) ? current($all) : null;

        $row = TempInvoice::findOne(['id' => $key, 'created_by' => $identity->id, 'state' => 1]);
        if ($row === null || !is_array($posted)) {
            return ['output' => '', 'message' => 'السطر غير موجود.'];
        }

        // ----- الكمية -----
        if (array_key_exists('quantity', $posted)) {
            if (!is_numeric($posted['quantity']) || (float) $posted['quantity'] <= 0) {
                return ['output' => '', 'message' => Yii::t('app', "Sorry You Enter Zero Value As Quantity")];
            }
            $quantity = (float) $posted['quantity'];

            if ($company->zeroQnty == 0) {
                $balance = $this->visibleStock($row->category) - $this->enteredQty($row->category, $row->id);
                if ($balance <= 0) {
                    return ['output' => '', 'message' => Yii::t('app', "عفوا لقد تجاوزت الكمية الموجودة لايمكنك الاستمرار")];
                }
                if ($quantity > $balance) {
                    $quantity = $balance;   // تُقلَّص للمتاح، ويظهر الرقم الفعلي في الخلية
                }
            }
            $row->quantity = $quantity;
            $row->save(false);
            return ['output' => $row->quantity, 'message' => ''];
        }

        // ----- سعر البيع -----
        if (array_key_exists('salePrice', $posted)) {
            if ($identity->editSalePrice != 1) {
                return ['output' => '', 'message' => 'لايوجد لديك صلاحية لتعديل سعر البيع'];
            }
            if (!is_numeric($posted['salePrice']) || (float) $posted['salePrice'] <= 0) {
                return ['output' => '', 'message' => Yii::t('app', "Sorry You Enter Zero Value As Sale Price")];
            }
            $price = (float) $posted['salePrice'];

            $prices = Prices::find()->where(['category' => $row->category])->one();
            if ($prices !== null) {
                // كما في الكود القديم: الأدنى هو التكلفة لمن يملك selling_by_costprice، وإلا السعر الأدنى
                $floor = Yii::$app->user->can('selling_by_costprice') ? (float) $prices->costPrice : (float) $prices->minPrice;
                if ($price < $floor) {
                    $price = $floor;
                }
            }
            $row->salePrice = $price;
            if ($identity->client <> null) {
                $row->waitQnty = 0;
            }
            $row->save(false);
            return ['output' => $row->salePrice, 'message' => ''];
        }

        // ----- كمية الانتظار -----
        if (array_key_exists('waitQnty', $posted)) {
            if ($identity->client <> null || $company->waitQnty != 1) {
                return ['output' => '', 'message' => 'هذا الحقل غير متاح.'];
            }
            if (!is_numeric($posted['waitQnty']) || (float) $posted['waitQnty'] < 0) {
                return ['output' => '', 'message' => 'القيمة يجب أن تكون رقماً صحيحاً.'];
            }
            $row->waitQnty = (float) $posted['waitQnty'];
            $row->save(false);
            return ['output' => $row->waitQnty, 'message' => ''];
        }

        return ['output' => '', 'message' => 'هذا الحقل غير قابل للتعديل.'];
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /** حذف سطر من الفاتورة الحالية (POST، سطر المستخدم فقط) */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['create']);
    }

    public function actionDeleteAll()
    {
        TempInvoice::deleteAll(['state' => 1, 'created_by' => Yii::$app->user->identity->id]);
        return $this->redirect(['create']);
    }

    /** سطور الفاتورة الحالية للمستخدم فقط (كان أي مستخدم يفتح أو يحذف سطور غيره بالرقم) */
    protected function findModel($id)
    {
        $model = TempInvoice::findOne([
            'id' => (int) $id,
            'created_by' => Yii::$app->user->identity->id,
            'state' => 1,
        ]);
        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * تعليق الفاتورة الحالية: تتحول سطورها إلى state = 2 برقم تعليق جديد.
     * الرقم الجديد أكبر رقم مستعمل في الجدول كله + 1 (تحت قفل) فلا يتكرر مع مستخدم آخر ولا مع فاتورة أُعيد فتحها.
     */
    public function actionHold()
    {
        $userId = (int) Yii::$app->user->identity->id;
        $db = Yii::$app->db;

        if (!TempInvoice::find()->where(['state' => 1, 'created_by' => $userId])->exists()) {
            Yii::$app->session->setFlash('error', 'لا توجد أصناف في الفاتورة الحالية لتعليقها.');
            return $this->redirect(['create']);
        }

        $got = (int) $db->createCommand('SELECT GET_LOCK(:n, 5)', [':n' => self::HOLD_LOCK])->queryScalar() === 1;
        if (!$got) {
            Yii::$app->session->setFlash('error', 'النظام مشغول. أعد المحاولة.');
            return $this->redirect(['create']);
        }
        try {
            $number = (int) TempInvoice::find()->max('invoice_number') + 1;
            TempInvoice::updateAll(
                ['state' => 2, 'invoice_number' => $number],
                ['state' => 1, 'created_by' => $userId]
            );
        } finally {
            $db->createCommand('SELECT RELEASE_LOCK(:n)', [':n' => self::HOLD_LOCK])->queryScalar();
        }

        return $this->redirect(['create']);
    }

    /** قائمة الفواتير المعلّقة للمستخدم (نافذة منبثقة) */
    public function actionHolded()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TempInvoice::find()
                ->select('sum(salePrice*quantity) as salePrice ,invoice_number ')
                ->where(['state' => 2])
                ->andWhere(['created_by' => Yii::$app->user->identity->id])
                ->groupBy('invoice_number')
                ->orderBy(['invoice_number' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 50
            ],
        ]);

        return $this->renderAjax('holded', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /** إعادة فاتورة معلّقة (للمستخدم نفسه) إلى الفاتورة الحالية، بشرط أن تكون الحالية فارغة */
    public function actionUnhold($id)
    {
        $userId = (int) Yii::$app->user->identity->id;

        if (TempInvoice::find()->where(['state' => 1, 'created_by' => $userId])->exists()) {
            Yii::$app->session->setFlash('error', Yii::t('app', "There is another invoice Finish or hold it before !"));
            return $this->redirect(['create']);
        }

        $count = TempInvoice::updateAll(
            ['state' => 1],
            ['state' => 2, 'created_by' => $userId, 'invoice_number' => (int) $id]
        );
        if ($count === 0) {
            Yii::$app->session->setFlash('error', 'الفاتورة المعلّقة غير موجودة.');
        }
        return $this->redirect(['create']);
    }

    /**
     * الإضافة السريعة: جدول بأرصدة الأصناف يكتب المستخدم بجانب كل صنف الكمية المطلوبة.
     * كل صنف يمر بنفس قواعد الإدراج العادي، وبسعر البيع الافتراضي المحسوب على الخادم.
     */
    public function actionFast()
    {
        $model = new TempInvoice();
        $identity = Yii::$app->user->identity;
        $company = CompanyInfo::current();

        if (is_array(Yii::$app->request->post($model->formName()))) {
            $post = Yii::$app->request->post($model->formName());
            $values = (isset($post['textInputValues']) && is_array($post['textInputValues'])) ? $post['textInputValues'] : [];
            $selection = Yii::$app->request->post('selection');
            $selected = is_array($selection) ? array_map('intval', $selection) : [];

            $added = 0;
            $problems = [];
            foreach ($values as $categoryId => $qty) {
                $categoryId = (int) $categoryId;
                if (!in_array($categoryId, $selected, true) || !is_numeric($qty) || (float) $qty <= 0) {
                    continue;
                }
                $prices = Prices::find()->where(['category' => $categoryId])->one();
                if ($prices === null) {
                    $problems[] = 'الصنف ' . $categoryId . ': لا يوجد سعر.';
                    continue;
                }
                $r = $this->addRow([
                    'category' => $categoryId,
                    'quantity' => (float) $qty,
                    'salePrice' => $this->defaultSalePrice($prices, $company),
                    'branch' => (int) $identity->branch,
                    'waitQnty' => 0,
                ], $company);
                if ($r['ok']) {
                    $added++;
                    foreach ($r['notes'] as $n) {
                        $problems[] = 'الصنف ' . $categoryId . ': ' . $n;
                    }
                } else {
                    $problems[] = 'الصنف ' . $categoryId . ': ' . $r['message'];
                }
            }

            if ($added > 0) {
                Yii::$app->session->setFlash('success', 'تمت إضافة ' . $added . ' صنف.');
            }
            if ($problems) {
                Yii::$app->session->setFlash('error', implode(' | ', $problems));
            }
            return $this->redirect(['create']);
        }

        // الرصيد المعروض: الأنواع المسموح البيع منها، وفرع المستخدم فقط إن لم يملك seeOtherBranchQ
        $query = Stocks::find()
            ->select(
                ' category.id, max(category.name) as name, sum(stocks.quantity) as quantity, max(category.unit) as unit, max(category.company) as company,
                        max(category.box) as box, max(category.class) as class, max(category.serialNo) as serialNo, branch, max(category.commCode) as commCode
                        ,max(maxPrice) as maxPrice, max(costPrice) as costPrice, max(minPrice) as minPrice'
            )
            ->leftJoin('category', 'category.id = stocks.category')
            ->leftJoin('prices', 'category.id = prices.category')
            ->where(['in', 'stocks.type', $this->saleStockTypes()])
            ->groupBy('stocks.category, stocks.branch')
            ->having('sum(stocks.quantity) <> 0');
        if ($identity->seeOtherBranchQ == 0) {
            $query->andWhere(['stocks.branch' => (int) $identity->branch]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ],
            'pagination' => ['pageSize' => 70],
        ]);

        return $this->renderAjax('createFast', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }
}
