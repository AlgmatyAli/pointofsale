<?php

use app\models\Client;
use app\models\Currancy;
use app\models\PaymentTypes;
use app\models\ShippingType;
use kartik\date\DatePicker;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/** @var yii\web\View $this */
/** @var app\models\Purchases $model */
/** @var yii\data\ActiveDataProvider $dataProvider */

/*
 * ما لم يُمَس عمداً (الحسابات):
 *  - الحقل paid ما زال يستدعي netTotalsPurchases( $(this) ) عند فقدان التركيز (معرّفات #purchases-paid / -total / -netTotal).
 *  - FormulaColumn: الإجمالي = العمود 5 (سعر التكلفة) × العمود 7 (الكمية). ترتيب الأعمدة كما هو بالضبط.
 *  - معرّف الجدول w0 ومعرّف حقل رقم الفاتورة purchases-id (يقرؤه كود إضافة الصنف).
 *
 * تغييرات الواجهة فقط: بطاقات وأقسام، شريط حفظ ثابت أسفل الصفحة، بحث أوضح، جدول متجاوب،
 * اختصارات لوحة المفاتيح (F2 بحث، Ctrl+S حفظ)، والحقلان id و total للقراءة فقط.
 */

// ---------------------------------------------------------------------------
// إجماليات رأس الفاتورة بعد كل إضافة أو تعديل
// تُحسب هنا في PHP (من كل صفوف الفاتورة وليس الصفحة الحالية فقط) وتُوضع كسمات على الجدول،
// ثم يقرؤها JavaScript بعد إعادة رسم الجدول ويكتبها في الحقلين. لا معادلة جديدة في المتصفح.
// ---------------------------------------------------------------------------
$detailRows = $dataProvider->getModels();
if ($dataProvider instanceof ActiveDataProvider && $dataProvider->getTotalCount() > $dataProvider->getCount()) {
    $allRowsQuery = clone $dataProvider->query;
    $detailRows = $allRowsQuery->limit(null)->offset(null)->orderBy(null)->all();
}

// total = مجموع (سعر التكلفة × الكمية): هي نفس معادلة عمود الإجمالي في الجدول ودالة totalsCalculate() في ajax-modal-popup.js
$headerTotal = 0;
foreach ($detailRows as $detailRow) {
    $headerTotal += $detailRow->costPrice * $detailRow->quantity;
}

// totalCost في رأس الفاتورة: معادلته لم تُحدَّد بعد، فلا يُحدَّث تلقائياً (يبقى كما هو).
// لتفعيله ضع المعادلة هنا بدل null، مثلاً:
//   (أ) مجموع إجمالي التكلفة × الكمية: array_sum(array_map(fn ($r) => $r->totalCost * $r->quantity, $detailRows))
//   (ب) مجموع عمود إجمالي التكلفة مباشرة: array_sum(array_map(fn ($r) => $r->totalCost, $detailRows))
$headerTotalCost = null;

$gridDataAttributes = [
    'data-header-total' => number_format($headerTotal, 3, '.', ''),
    'data-item-count' => count($detailRows),      // للعرض فقط في الشريط السفلي
    'class' => 'align-middle',
];
if ($headerTotalCost !== null) {
    $gridDataAttributes['data-header-total-cost'] = number_format($headerTotalCost, 3, '.', '');
}

$datePlugin = ['autoclose' => true, 'format' => 'yyyy-mm-dd', 'todayHighlight' => true, 'todayBtn' => true];

// شارة نوع الفاتورة (للعرض فقط)
$typeBadges = [
    1 => ['فاتورة مشتريات', 'success'],
    2 => ['مسترجع مشتريات', 'warning'],
    3 => ['معلّقة', 'secondary'],
];
[$typeText, $typeColor] = $typeBadges[(int) $model->type] ?? ['', 'light'];

$backUrl = Yii::$app->request->referrer ?: ['index'];

// ---------------------------------------------------------------------------
// JavaScript لقائمة البحث وإضافة الصنف (نفس أسلوب شاشة تعديل المبيعات)
// ---------------------------------------------------------------------------
$this->registerJs(
    'window.purUpdCfg = ' . Json::htmlEncode([
        'addUrl'   => Url::to(['purchases-details/add-purchases']),
        'gridPjax' => '#w0',
    ]) . ';',
    View::POS_HEAD,
    'pur-upd-cfg'
);

$this->registerJs(<<<'JS'
(function (w) {
    function esc(s) {
        if (s === null || s === undefined) { return ''; }
        return String(s).replace(/[&<>"']/g, function (c) {
            return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c];
        });
    }

    // وسم صغير «اسم: قيمة»؛ يُتجاوز إن كانت القيمة فارغة
    function tag(label, value, strong) {
        if (value === null || value === undefined || value === '') { return ''; }
        return '<span class="pur-opt-tag' + (strong ? ' pur-opt-strong' : '') + '">' +
            '<span class="text-body-secondary">' + esc(label) + ':</span> <b>' + esc(value) + '</b></span>';
    }

    // شكل الصنف داخل القائمة المنسدلة: الاسم في سطر، والتفاصيل تحته وتلتف على الشاشات الضيقة
    w.purFormatProduct = function (product) {
        if (product.loading) { return product.text; }

        return '<div class="pur-opt">' +
            '<div class="fw-semibold">' + esc(product.text) + '</div>' +
            '<div class="pur-opt-tags">' +
                tag('الكمية', product.quantity, true) +
                tag('سعر البيع', product.maxPrice) +
                tag('الأدنى', product.minPrice) +
                tag('المكان', product.place) +
                tag('رقم القطعة 1', product.serialNo) +
                tag('رقم القطعة 2', product.commCode) +
                tag('التسلسل', product.id) +
                tag('الشركة', product.company) +
            '</div>' +
        '</div>';
    };

    w.purFormatSelection = function (product) {
        return esc(product.name || product.text);
    };

    // إعادة رسم الجدول (الإجمالي في الصف والمجموع السفلي). لا تغيّر أي معادلة.
    w.purUpdRefresh = function () {
        $.pjax.reload({container: w.purUpdCfg.gridPjax});
    };

    // إضافة الصنف المختار إلى الفاتورة (رقم الفاتورة يُقرأ من الحقل كما كان)
    w.purAddItem = function (id) {
        if (!id) { return; }
        $.get(w.purUpdCfg.addUrl, {categoryid: id, purchasesId: $('#purchases-id').val()})
            .done(function () {
                // تفريغ الحقل ليمكن إضافة الصنف نفسه مجدداً، ثم تحديث الجدول وإعادة التركيز
                $('#kv-repo-template').val(null).trigger('change');
                w.purUpdRefresh();
                $('#kv-repo-template').select2('open');
            })
            .fail(function () {
                if (typeof swal === 'function') {
                    swal({title: 'منظومة المبيعات', text: 'حدث خطأ اثناء الإضافة', type: 'error', confirmButtonText: 'حسنا'});
                }
            });
    };
})(window);
JS, View::POS_HEAD, 'pur-upd-helpers');

$this->registerCss(<<<'CSS'
.pur-opt { padding: .15rem 0; }
.pur-opt-tags { display: flex; flex-wrap: wrap; gap: .25rem .9rem; margin-top: .2rem; font-size: .8125rem; }
.pur-opt-strong { color: var(--bs-success-text-emphasis, #0f5132); }
.pur-bar {
    position: sticky; bottom: 0; z-index: 5;
    background: var(--bs-body-bg);
    border-top: 1px solid var(--bs-border-color);
    box-shadow: 0 -4px 12px rgba(0, 0, 0, .06);
}
.pur-search .select2-container { width: 100% !important; }
.pur-search .select2-selection { min-height: 3rem; display: flex; align-items: center; }
#w0 .editable-click { white-space: nowrap; }
@media (max-width: 575.98px) {
    .pur-bar .btn { flex: 1 1 auto; }
}
CSS);

// تحرير داخل الخلية (بدل النافذة المنبثقة) مع إعادة رسم الجدول بعد كل حفظ ناجح
$editable = [
    'asPopover' => false,
    'bsVersion' => '5.x',
    'submitButton' => ['icon' => '<i class="bi bi-check-lg"></i>'],
    'resetButton' => ['icon' => '<i class="bi bi-arrow-counterclockwise"></i>'],
    'pluginEvents' => ['editableSuccess' => new JsExpression('function () { purUpdRefresh(); }')],
];

// زر «نقل إلى الفاتورة المؤقتة» يظهر للفواتير المعلقة (type = 3) فقط
$isPending = (int) $model->type === 3;

// تنبيه: FormulaColumn يعتمد على ترتيب الأعمدة (5 = سعر التكلفة، 7 = الكمية). لا تغيّر الترتيب بدون تعديل الأرقام.
$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],                       // 0

    'category0.id',                                              // 1

    ['attribute' => 'id', 'visible' => false],                  // 2

    [                                                            // 3
        'attribute' => 'category',
        'headerOptions' => ['style' => 'width:20%'],
        'format' => 'html',
        'value' => function ($model) {
            $category = $model->category0;
            if ($category === null) {
                return '';
            }
            // Html::encode: اسم الصنف يُدخله المستخدم ولا يجوز طباعته كما هو داخل HTML
            return Html::a(
                Html::encode($category->name),
                ['category/info', 'id' => $category->id],
                ['class' => 'btn-link popupModal']
            );
        },
    ],

    'category0.company',                                         // 4

    [                                                            // 5
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'costPrice',
        'editableOptions' => $editable,
    ],

    [                                                            // 6
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'totalCost',
        'label' => Yii::t('app', 'Cost Total'),
        'editableOptions' => $editable,
    ],

    [                                                            // 7
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'quantity',
        'label' => Yii::t('app', 'quantity'),
        'editableOptions' => $editable,
    ],

    [                                                            // 8
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'salePrice',
        'editableOptions' => $editable,
    ],

    [                                                            // 9
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'salePrice_',
        'editableOptions' => $editable,
    ],

    [                                                            // 10
        'class' => 'kartik\grid\FormulaColumn',
        'header' => Yii::t('app', 'Total'),
        'vAlign' => 'middle',
        'value' => function ($model, $key, $index, $widget) {
            $p = compact('model', 'key', 'index');
            return $widget->col(5, $p) * $widget->col(7, $p);
        },
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'hAlign' => 'right',
        'format' => ['decimal', 3],
        'mergeHeader' => true,
        'pageSummary' => true,
        'footer' => true,
    ],

    [                                                            // 11
        'class' => 'yii\grid\ActionColumn',
        'template' => '<div class="btn-group btn-group-sm" role="group">{remove}' . ($isPending ? '{transfer}' : '') . '</div>',
        'buttons' => [
            'remove' => function ($url, $model) {
                return Html::a(
                    '<i class="bi bi-trash"></i>',
                    Url::to(['purchases/remove', 'id' => $model['id']]),
                    [
                        'title' => Yii::t('yii', 'Delete'),
                        'aria-label' => Yii::t('yii', 'Delete'),
                        'class' => 'btn btn-outline-danger btn-sm',
                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
            'transfer' => function ($url, $model) {
                return Html::a(
                    '<i class="bi bi-box-arrow-up"></i>',
                    Url::to(['purchases/transfer-to-temp-invoice', 'id' => $model['id']]),
                    [
                        'title' => Yii::t('app', 'Transfer'),
                        'aria-label' => Yii::t('app', 'Transfer'),
                        'class' => 'btn btn-outline-secondary btn-sm',
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]
                );
            },
        ],
    ],
];
?>

<div class="purchases-form">

    <!-- بيانات الفاتورة (قابلة للطي لإظهار مساحة أكبر للجدول) -->
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center gap-2">
            <span class="fw-semibold"><i class="bi bi-receipt"></i> فاتورة رقم <?= Html::encode($model->id) ?></span>
            <?php if ($typeText !== '') : ?>
                <span class="badge text-bg-<?= $typeColor ?>"><?= Html::encode($typeText) ?></span>
            <?php endif; ?>
            <button type="button" class="btn btn-sm btn-outline-secondary ms-auto" id="pur-head-toggle"
                    data-bs-toggle="collapse" data-bs-target="#pur-head-body"
                    aria-expanded="true" aria-controls="pur-head-body" title="إظهار/إخفاء بيانات الفاتورة">
                <i class="bi bi-chevron-expand"></i> <span class="d-none d-sm-inline">بيانات الفاتورة</span>
            </button>
        </div>

        <div id="pur-head-body" class="collapse show">
            <div class="card-body">

                <?php // معرّف صريح للنموذج: بدونه يأخذ Yii المعرّف التلقائي w0، وهو نفسه معرّف حاوية الجدول (Pjax)،
                // فتحلّ إعادة رسم الجدول محل النموذج كله.
                ?>
                <?php $form = ActiveForm::begin(['id' => 'purchases-form']); ?>

                <?= $form->errorSummary($model) ?>

                <div class="row">
                    <div class="col-lg-2 col-md-4">
                        <?= $form->field($model, 'id')->textInput(['maxlength' => true, 'readonly' => true]) ?>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <?= $form->field($model, 'type')->dropDownList([
                            '1' => 'فاتورة مشتريات',
                            '2' => 'فاتورة مسترجع مشتريات',
                            '3' => 'فاتورة مشتريات معلقة',
                        ]) ?>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <?= $form->field($model, 'clinet')->widget(Select2::class, [
                            'data' => ArrayHelper::map(Client::find()->where(['in', 'type', [1, 2]])->all(), 'id', 'name'),
                            'language' => 'ar',
                            'options' => ['placeholder' => 'الرجاء اختيار اسم العميل ...'],
                            'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                        ]) ?>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <?= $form->field($model, 'clientBill')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <?= $form->field($model, 'at')->widget(DatePicker::class, [
                            'type' => DatePicker::TYPE_INPUT,
                            'language' => 'ar',
                            'pluginOptions' => $datePlugin,
                        ]) ?>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <?= $form->field($model, 'dateOfArrival')->widget(DatePicker::class, [
                            'type' => DatePicker::TYPE_INPUT,
                            'language' => 'ar',
                            'pluginOptions' => $datePlugin,
                        ]) ?>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <?= $form->field($model, 'shippingType')->widget(Select2::class, [
                            'data' => ArrayHelper::map(ShippingType::find()->all(), 'id', 'name'),
                            'language' => 'ar',
                            'options' => ['placeholder' => 'الرجاء اختيار اسم طريقة الشحن ...'],
                            'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                        ]) ?>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <?= $form->field($model, 'currancy')->widget(Select2::class, [
                            'data' => ArrayHelper::map(Currancy::find()->all(), 'id', 'name'),
                            'language' => 'ar',
                            'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                        ]) ?>
                    </div>
                </div>

                <h6 class="text-body-secondary border-bottom pb-2 mb-3 mt-2"><i class="bi bi-cash-coin"></i> الدفع والإجماليات</h6>
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <?= $form->field($model, 'payWay')->dropDownList(
                            ['0' => 'نقدا', '1' => 'آجـــل', '2' => 'دفعة على الحساب'],
                            ['prompt' => '']
                        ) ?>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <?= $form->field($model, 'payment_type')->widget(Select2::class, [
                            'data' => ArrayHelper::map(PaymentTypes::find()->all(), 'id', 'name'),
                            'language' => 'ar',
                            'options' => [
                                'placeholder' => 'الرجاء اختيار  طريقة الدفع ...',
                                'value' => $model->isNewRecord ? 1 : $model->payment_type,
                            ],
                            'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                        ]) ?>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <?= $form->field($model, 'total_currancy')->textInput(['maxlength' => true, 'inputmode' => 'decimal']) ?>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <?= $form->field($model, 'totalCost')->textInput(['maxlength' => true, 'inputmode' => 'decimal']) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <?= $form->field($model, 'total')->textInput(['readonly' => true]) ?>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <?= $form->field($model, 'paid')->textInput([
                            'maxlength' => true,
                            'inputmode' => 'decimal',
                            'onfocusout' => 'netTotalsPurchases( $(this) )',   // لا تغيّره: حساب الصافي
                        ]) ?>
                    </div>
                    <div class="col-lg-6 d-flex align-items-center">
                        <?php
                        // الحالة الابتدائية من خيار المؤسسة «تعديل أسعار البيع عند تعديل فاتورة مشتريات»؛ وبعد إرسال النموذج تُحفظ قيمة المستخدم
                        $companyOpt = \app\models\CompanyInfo::find()->one();
                        $defaultChange = ($companyOpt === null || !$companyOpt->hasAttribute('editSalePriceOnPurchaseUpdate') || $companyOpt->editSalePriceOnPurchaseUpdate === null)
                            ? true : ((int) $companyOpt->editSalePriceOnPurchaseUpdate !== 0);
                        $changeChecked = ($model->changeSalePrice === null || $model->changeSalePrice === '') ? $defaultChange : (bool) $model->changeSalePrice;
                        ?>
                        <?= $form->field($model, 'changeSalePrice')->checkbox(['checked' => $changeChecked]) ?>
                    </div>
                </div>

                <?= $form->field($model, 'notes')->textarea(['rows' => 2]) ?>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>

    <!-- البحث عن صنف وإضافته -->
    <div class="card mb-3">
        <div class="card-body pur-search">
            <label class="form-label fw-semibold" for="kv-repo-template">
                <i class="bi bi-search"></i> إضافة صنف إلى الفاتورة
            </label>
            <?= Select2::widget([
                'name' => 'kv-repo-template',
                'language' => 'ar',
                'options' => [
                    'id' => 'kv-repo-template',
                    'placeholder' => 'ابحث بالاسم أو الباركود أو رقم القطعة ...',
                    'dir' => 'rtl',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 1,
                    'ajax' => [
                        'url' => Url::to(['/except/itemlist']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function (params) { return {q: params.term, page: params.page}; }'),
                        'processResults' => new JsExpression('function (data) { return {results: data.results}; }'),
                        'cache' => true,
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('purFormatProduct'),
                    'templateSelection' => new JsExpression('purFormatSelection'),
                ],
                'pluginEvents' => [
                    'change' => 'function (event) { purAddItem(event.currentTarget.value); }',
                ],
            ]) ?>
            <div class="form-text d-none d-lg-block">
                <kbd>F2</kbd> للانتقال إلى البحث &middot; اختر الصنف أو امسح الباركود ثم <kbd>Enter</kbd> لإضافته &middot;
                اضغط على السعر أو الكمية في الجدول لتعديلها مباشرة
            </div>
        </div>
    </div>

    <div class="card mb-0">
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => '{items}{pager}',
                'summary' => true,
                'columns' => $gridColumns,
                'tableOptions' => $gridDataAttributes,
                'responsive' => true,
                'responsiveWrap' => false,
                'hover' => true,
                'condensed' => true,
                'bordered' => false,
                'striped' => true,
                'emptyText' => '<i class="bi bi-inbox fs-3 d-block mb-2"></i>لا توجد أصناف في الفاتورة بعد. ابحث عن صنف في الحقل أعلاه لإضافته.',
                'emptyTextOptions' => ['class' => 'text-center text-body-secondary py-5'],
                'pjax' => true,
                'pjaxSettings' => [
                    'neverTimeout' => true,
                    'options' => ['id' => 'w0'],
                ],
                'showPageSummary' => true,
            ]) ?>
        </div>
    </div>

    <!-- شريط الحفظ الثابت أسفل الشاشة: الأرقام للعرض فقط، ويحدّثها JavaScript من سمات الجدول بعد كل إعادة رسم -->
    <div class="pur-bar d-flex flex-wrap align-items-center gap-3 px-3 py-2 mt-3">
        <div class="d-flex flex-wrap align-items-baseline gap-4">
            <div>
                <span class="text-body-secondary small">عدد الأصناف</span>
                <b id="pur-count"><?= count($detailRows) ?></b>
            </div>
            <div>
                <span class="text-body-secondary small">إجمالي الفاتورة</span>
                <b id="pur-total" class="fs-5"><?= number_format($headerTotal, 3) ?></b>
            </div>
        </div>
        <div class="ms-auto d-flex flex-wrap align-items-center gap-2">
            <span class="text-body-secondary small d-none d-lg-inline"><kbd>Ctrl</kbd>+<kbd>S</kbd> للحفظ</span>
            <?= Html::a(
                '<i class="bi bi-x-circle"></i> ' . Yii::t('app', 'Back'),
                $backUrl,
                ['class' => 'btn btn-outline-secondary']
            ) ?>
            <?= Html::submitButton(
                '<i class="bi bi-save"></i> ' . Yii::t('app', 'Save'),
                ['class' => 'btn btn-success px-4', 'id' => 'pur-save', 'form' => 'purchases-form']
            ) ?>
        </div>
    </div>

</div>

<?php
// فتح روابط .popupModal (اسم الصنف) في النافذة. الربط على document لأن الجدول يُعاد رسمه بـ Pjax،
// ويتجاهل الحدث إن عالجه ajax-modal-popup.js أصلاً (لتفادي فتح النافذة مرتين).
$this->registerJs(<<<'JS'
$(document).off('click.purPopup').on('click.purPopup', '.popupModal', function (e) {
    if (e.isDefaultPrevented()) { return; }
    e.preventDefault();
    $('#modal').modal('show').find('.modal-content').load($(this).attr('href'));
});
JS, View::POS_READY, 'pur-upd-popup');
?>

<?php
// بعد كل إعادة رسم للجدول (إضافة صنف أو تعديل خلية): انسخ الإجماليات المحسوبة في PHP إلى حقلي رأس الفاتورة.
// totalCost يُحدَّث فقط إن وُجدت السمة data-header-total-cost (أي بعد تفعيل معادلته في الأعلى).
// عدّاد الأصناف والإجمالي في الشريط السفلي يعرضان القيمتين نفسيهما فقط.
$this->registerJs(<<<'JS'
$(document).off('pjax:end.purTotals').on('pjax:end.purTotals', '#w0', function () {
    var table = $('#w0').find('table[data-header-total]').first();
    if (!table.length) { return; }

    var total = table.attr('data-header-total');
    $('#purchases-total').val(total);
    $('#pur-total').text(Number(total).toLocaleString('en-US', {minimumFractionDigits: 3, maximumFractionDigits: 3}));
    $('#pur-count').text(table.attr('data-item-count'));

    var totalCost = table.attr('data-header-total-cost');
    if (totalCost !== undefined) {
        $('#purchases-totalcost').val(totalCost);
    }
});
JS, View::POS_READY, 'pur-upd-totals');

// سلوك الشاشة: منع النقر المزدوج على «حفظ»، اختصارات لوحة المفاتيح، تذكّر طيّ بطاقة البيانات، وتركيز البحث عند الفتح.
$this->registerJs(<<<'JS'
(function () {
    var saveBtn = $('#pur-save');
    var saveHtml = saveBtn.html();

    $('#purchases-form').on('beforeSubmit', function () {
        saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> ' + $('<span>').text(saveBtn.text().trim()).html());
        return true;
    });
    $(window).on('pageshow', function () {
        saveBtn.prop('disabled', false).html(saveHtml);
    });

    // F2: انتقال إلى البحث. Ctrl+S: حفظ الفاتورة.
    $(document).off('keydown.purKeys').on('keydown.purKeys', function (e) {
        if (e.key === 'F2') {
            e.preventDefault();
            $('#kv-repo-template').select2('open');
        } else if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S')) {
            e.preventDefault();
            if (!saveBtn.prop('disabled')) { saveBtn.trigger('click'); }
        }
    });

    // تذكّر حالة الطي لدى هذا المستخدم فقط (اختياري؛ يعمل بدونه)
    var body = document.getElementById('pur-head-body');
    var storeKey = 'purHeadCollapsed';
    try {
        if (localStorage.getItem(storeKey) === '1' && !$('#purchases-form .has-error, #purchases-form .is-invalid').length) {
            body.classList.remove('show');
            $('#pur-head-toggle').attr('aria-expanded', 'false');
        }
    } catch (err) { /* التخزين غير متاح */ }
    body.addEventListener('hidden.bs.collapse', function () { try { localStorage.setItem(storeKey, '1'); } catch (err) {} });
    body.addEventListener('shown.bs.collapse', function () { try { localStorage.setItem(storeKey, '0'); } catch (err) {} });

    // على الشاشات الكبيرة ومع عدم وجود أخطاء تحقق: ابدأ بالتركيز على البحث ليعمل ماسح الباركود فوراً
    if (window.matchMedia('(min-width: 992px)').matches && !$('#purchases-form .has-error, #purchases-form .is-invalid').length) {
        setTimeout(function () { $('#kv-repo-template').select2('open'); }, 400);
    }
})();
JS, View::POS_READY, 'pur-upd-behavior');
?>
