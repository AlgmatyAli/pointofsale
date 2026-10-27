<?php

use kartik\editable\Editable;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/** @var yii\web\View $this */
/** @var app\models\TempInvoicePurchase $model */
/** @var yii\data\ActiveDataProvider $dataProvider */

/*
 * ملاحظة مهمة: كل ما يتعلق بالحسابات بقي كما هو:
 *  - الدوال totalRate() و totalCost() و profit() في ajax-modal-popup.js لم تُمَس، وتعتمد على معرّفات الحقول
 *    (tempinvoicepurchase-totalinvoice / -totalcost / -rate / -derhamrate / -costprice / -costtotal / -saleprice...).
 *    لذلك أبقيت أسماء الحقول ومعرّف النموذج temp-invoice-purchase-form وحاوية الجدول w0 كما هي.
 *  - الحقل totalCost ما زال يستدعي totalRate( $(this) ) عند فقدان التركيز.
 *  - FormulaColumn (الإجمالي = العمود 3 × العمود 4) وترتيب الأعمدة لم يتغيرا.
 *  - إضافة الصنف عبر AJAX يعالجها ajax-modal-popup.js (يعيد تحميل #w0)، فلم أضف معالجاً مكرراً.
 */

// قيم تأتي من الرابط (الافتراضي '0') كما في الكود الأصلي
$getParam = static function (string $name): string {
    $value = Yii::$app->request->get($name);
    return is_scalar($value) ? (string) $value : '0';
};
$rate         = $getParam('rate');
$totalCost    = $getParam('totalCost');
$totalInvoice = $getParam('totalInvoice');
$derhamRate   = $getParam('derhamRate');

// ---------------------------------------------------------------------------
// تنسيقات الصفحة
// ---------------------------------------------------------------------------
$this->registerCss(<<<'CSS'
.pur-head .btn { white-space: nowrap; }
.pur-card .card-header { font-weight: 600; padding: .5rem .85rem; }
.pur-card .form-label { font-size: .8rem; margin-bottom: .15rem; color: var(--bs-secondary-color); }
.pur-card .mb-3 { margin-bottom: .6rem !important; }
.pur-add .select2-container .select2-selection--single { min-height: 44px; }
.pur-grid .kv-grid-table > thead > tr > th { white-space: nowrap; }
@media (max-width: 575.98px) { .pur-head .btn { flex: 1 1 auto; } }
CSS);

// ---------------------------------------------------------------------------
// إعدادات وJavaScript للبحث عن الصنف
// ---------------------------------------------------------------------------
$this->registerJs(
    'window.purCfg = ' . Json::htmlEncode([
        'invUrl'   => Url::to(['temp-invoice-purchase/get-inv']),
        'gridPjax' => '#w0',
    ]) . ';',
    View::POS_HEAD,
    'pur-form-cfg'
);

$this->registerJs(<<<'JS'
(function (w) {
    function esc(s) {
        if (s === null || s === undefined) { return ''; }
        return String(s).replace(/[&<>"']/g, function (c) {
            return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c];
        });
    }

    // شارة (عنوان: قيمة)؛ الشارات الفارغة تُتجاهل
    function chip(label, value, cls) {
        if (value === null || value === undefined || value === '') { return ''; }
        return '<span class="badge ' + (cls || 'text-bg-light border') + ' fw-normal">' +
            '<span class="text-body-secondary">' + esc(label) + ':</span> <bdi>' + esc(value) + '</bdi></span>';
    }

    // شكل الصنف داخل القائمة المنسدلة: الاسم في سطر والشارات تحته
    w.purFormatProduct = function (product) {
        if (product.loading) { return esc(product.text); }

        return '<div style="overflow:hidden;">' +
            '<div class="fw-semibold mb-1">' + esc(product.text) + '</div>' +
            '<div class="d-flex flex-wrap gap-5">' +
                chip('التسلسل', product.id) +
                chip('رقم القطعة 1', product.serialNo) +
                chip('رقم القطعة 2', product.commCode) +
                chip('الكمية', product.quantity, 'text-bg-success') +
                chip('سعر البيع', product.maxPrice, 'text-bg-danger') +
                chip('السعر الأدنى', product.minPrice) +
                chip('المكان', product.place) +
                chip('الشركة', product.company) +
            '</div></div>';
    };

    w.purFormatSelection = function (product) {
        return esc(product.name || product.text);
    };

    // عند اختيار صنف: تعبئة الحقول (نفس الحقول والمعرّفات كما في الكود الأصلي)
    w.purPick = function (id) {
        $.get(w.purCfg.invUrl, {category: id}, function (data) {
            if (data != null) {
                $('#tempinvoicepurchase-quantity').val(data.quantity);
                $('#tempinvoicepurchase-costprice').val(data.costPrice);
                $('#tempinvoicepurchase-saleprice').val(data.maxPrice);
                $('#tempinvoicepurchase-saleprice_').val(data.minPrice);
                $('#tempinvoicepurchase-saleprice_2').val(data.minPrice2);
                $('#tempinvoicepurchase-saleprice_3').val(data.minPrice3);
                $('#category-serialno').val(data.serialNo);
                // نقل التركيز إلى الكمية لتبدأ الكتابة مباشرة
                $('#tempinvoicepurchase-quantity').trigger('focus').trigger('select');
            }
        });
    };

    // إعادة رسم الجدول بعد تعديل خلية (يحدّث عمود الإجمالي والمجاميع). لا يغيّر أي معادلة.
    w.purRefresh = function () {
        $.pjax.reload({container: w.purCfg.gridPjax});
    };
})(window);
JS, View::POS_HEAD, 'pur-form-helpers');

// تحرير داخل الخلية (بدل النافذة المنبثقة) مع إعادة رسم الجدول بعد كل حفظ ناجح
$editable = [
    'asPopover' => false,
    'bsVersion' => '5.x',
    'submitButton' => ['icon' => '<i class="bi bi-check-lg"></i>'],
    'resetButton' => ['icon' => '<i class="bi bi-arrow-counterclockwise"></i>'],
    'pluginEvents' => ['editableSuccess' => new JsExpression('function () { purRefresh(); }')],
];

// تنبيه: FormulaColumn يعتمد على ترتيب الأعمدة (3 = سعر التكلفة، 4 = الكمية). لا تغيّر الترتيب بدون تعديل الأرقام.
$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],                       // 0

    ['attribute' => 'id', 'visible' => false],                  // 1

    [                                                            // 2
        'attribute' => 'category',
        'headerOptions' => ['style' => 'width:40%'],
        'format' => 'raw',
        'value' => function ($model) {
            $category = $model->category0;
            if ($category === null) {
                return '';
            }
            // Html::encode: اسم الصنف يُدخله المستخدم ولا يجوز طباعته كما هو داخل HTML
            return Html::a(
                Html::encode($category->name),
                ['category/info', 'id' => $category->id],
                // data-pjax=0: يمنع Pjax من اعتراض الرابط وفتحه داخل الجدول؛ تفتحه النافذة المنبثقة فقط
                ['class' => 'btn-link popupModal fw-semibold', 'data-pjax' => '0']
            );
        },
    ],

    [                                                            // 3
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'costPrice',
        'contentOptions' => ['style' => 'font-size:14px;'],
        'editableOptions' => $editable,
        'format' => ['decimal', 3],
        'pageSummary' => true,
        'footer' => true,
    ],

    [                                                            // 4
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'quantity',
        'contentOptions' => ['style' => 'font-size:14px;'],
        'label' => Yii::t('app', 'quantity'),
        'editableOptions' => $editable + [
            'header' => Yii::t('app', 'quantity'),
            'inputType' => Editable::INPUT_TEXT,
            'options' => ['pluginOptions' => []],
        ],
        'format' => ['decimal', 3],
        'pageSummary' => true,
        'footer' => true,
    ],

    [                                                            // 5
        'class' => 'kartik\grid\FormulaColumn',
        'contentOptions' => ['style' => 'font-size:14px;'],
        'header' => Yii::t('app', 'Total'),
        'vAlign' => 'middle',
        'value' => function ($model, $key, $index, $widget) {
            $p = compact('model', 'key', 'index');
            return $widget->col(3, $p) * $widget->col(4, $p);
        },
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'hAlign' => 'right',
        'width' => '10%',
        'format' => ['decimal', 3],
        'mergeHeader' => true,
        'pageSummary' => true,
        'footer' => true,
    ],

    [                                                            // 6
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'costTotal',
        'contentOptions' => ['style' => 'font-size:14px;'],
        'editableOptions' => $editable,
        'format' => ['decimal', 3],
        'pageSummary' => true,
        'footer' => true,
    ],

    [                                                            // 7
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'salePrice',
        'label' => 'سعر البيع',
        'contentOptions' => ['style' => 'font-size:14px;'],
        'editableOptions' => $editable,
        'format' => ['decimal', 3],
        'pageSummary' => true,
        'footer' => true,
    ],

    [                                                            // 8
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'salePrice_',
        'label' => 'السعر الأدنى',
        'contentOptions' => ['style' => 'font-size:14px;'],
        'editableOptions' => $editable,
        'format' => ['decimal', 3],
        'pageSummary' => true,
        'footer' => true,
    ],

    [                                                            // 9
        'class' => 'yii\grid\ActionColumn',
        'template' => '<div class="btn-group btn-group-sm" role="group">{delete}</div>',
        'buttons' => [
            'delete' => function ($url) {
                return Html::a('<i class="bi bi-trash"></i>', $url, [
                    'title' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                    'class' => 'btn btn-outline-danger btn-sm',
                ]);
            },
        ],
    ],
];
?>

<div class="temp-invoice-purchase-form">

    <!-- العنوان والأزرار في سطر واحد -->
    <div class="pur-head d-flex flex-wrap align-items-center gap-2 mb-3">
        <h4 class="mb-0 flex-grow-1 text-nowrap">
            <i class="bi bi-bag-plus text-success"></i> <?= Html::encode($this->title) ?>
        </h4>

        <?= Html::button(
            '<i class="bi bi-plus-circle"></i> ' . Yii::t('app', 'Create Category'),
            ['value' => Url::to(['category/create-category']), 'class' => 'btn btn-sm btn-outline-primary popup']
        ) ?>

        <?= Html::a(
            '<i class="bi bi-trash"></i> ' . Yii::t('app', 'Delete All'),
            ['delete-all'],
            [
                'class' => 'btn btn-sm btn-outline-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]
        ) ?>

        <?= Html::button(
            '<i class="bi bi-check2-circle"></i> ' . Yii::t('app', 'Save'),
            [
                'value' => Url::to(['purchases/create', 'totalInvoice' => $totalInvoice]),
                'class' => 'btn btn-danger fw-bold px-4 popup',
            ]
        ) ?>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'temp-invoice-purchase-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
    ]); ?>

    <?= $form->errorSummary($model) ?>

    <!-- بيانات الفاتورة (قيمها من الرابط كما كانت) -->
    <div class="card pur-card shadow-sm mb-3">
        <div class="card-header"><i class="bi bi-receipt"></i> بيانات الفاتورة</div>
        <div class="card-body py-2">
            <div class="row g-2">
                <div class="col-6 col-md-3">
                    <?= $form->field($model, 'totalInvoice')->textInput([
                        'inputmode' => 'decimal',
                        'autocomplete' => 'off',
                        'value' => $totalInvoice,
                    ]) ?>
                </div>
                <div class="col-6 col-md-3">
                    <?= $form->field($model, 'totalCost')->textInput([
                        'maxlength' => true,
                        'inputmode' => 'decimal',
                        'autocomplete' => 'off',
                        'onfocusout' => 'totalRate( $(this) )',   // لا تغيّره: حساب المعامل (rate)
                        'value' => $totalCost,
                    ]) ?>
                </div>
                <div class="col-6 col-md-3">
                    <?= $form->field($model, 'rate')->textInput([
                        'inputmode' => 'decimal',
                        'autocomplete' => 'off',
                        'value' => $rate,
                    ]) ?>
                </div>
                <div class="col-6 col-md-3">
                    <?= $form->field($model, 'derhamRate')->textInput([
                        'inputmode' => 'decimal',
                        'autocomplete' => 'off',
                        'value' => $derhamRate,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- إضافة صنف -->
    <div class="card pur-card pur-add shadow-sm mb-3">
        <div class="card-header"><i class="bi bi-box-seam"></i> إضافة صنف</div>
        <div class="card-body py-2">

            <?= $form->field($model, 'category')->widget(Select2::class, [
                'language' => 'en',
                'options' => [
                    'placeholder' => Yii::t('app', 'Search...'),
                    'dir' => 'rtl',
                    'multiple' => false,
                ],
                'pluginOptions' => [
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
                    'change' => 'function (event) { purPick(event.currentTarget.value); }',
                ],
            ]) ?>

            <div class="row g-2 align-items-end">
                <div class="col-6 col-md-4 col-lg-2">
                    <?= $form->field($model, 'quantity')->textInput(['inputmode' => 'decimal', 'autocomplete' => 'off']) ?>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <?= $form->field($model, 'costPrice')->textInput(['maxlength' => true, 'inputmode' => 'decimal', 'autocomplete' => 'off']) ?>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <?= $form->field($model, 'salePrice')->label('سعر البيع')->textInput(['maxlength' => true, 'inputmode' => 'decimal', 'autocomplete' => 'off']) ?>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <?= $form->field($model, 'salePrice_')->label('السعر الأدنى')->textInput(['maxlength' => true, 'inputmode' => 'decimal', 'autocomplete' => 'off']) ?>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <?= $form->field($model, 'salePrice_2')->label('التكلفة بالدولار')->textInput(['maxlength' => true, 'inputmode' => 'decimal', 'autocomplete' => 'off']) ?>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <?= $form->field($model, 'salePrice_3')->label('التكلفة بالدرهم')->textInput(['maxlength' => true, 'inputmode' => 'decimal', 'autocomplete' => 'off']) ?>
                </div>
            </div>

            <div class="d-grid d-md-flex justify-content-md-end mt-1">
                <?= Html::submitButton(
                    $model->isNewRecord
                        ? '<i class="bi bi-plus-lg"></i> ' . Yii::t('app', 'Add')
                        : Yii::t('app', 'Update'),
                    ['class' => ($model->isNewRecord ? 'btn btn-success' : 'btn btn-primary') . ' px-5']
                ) ?>
            </div>
        </div>
    </div>

    <?php // حقلان مخفيان بنفس القيم الأصلية (box = 1، state = 0) 
    ?>
    <?= $form->field($model, 'box')->hiddenInput(['placeholder' => 'Box', 'value' => '1'])->label(false) ?>
    <?= $form->field($model, 'state')->hiddenInput(['readonly' => true, 'value' => '0'])->label(false) ?>

    <?php ActiveForm::end(); ?>

    <div class="pur-grid">
        <?= GridView::widget([
            'id' => 'my-gridview',
            'dataProvider' => $dataProvider,
            'layout' => '{items}{pager}',
            'summary' => true,
            'columns' => $gridColumns,
            'pjax' => true,
            'pjaxSettings' => [
                'neverTimeout' => true,
                'options' => ['id' => 'w0'],
            ],
            'showPageSummary' => true,
            'condensed' => true,
            'hover' => true,
            'striped' => true,
            'bordered' => false,
        ]) ?>
    </div>

</div>

<?php
// روابط .popupModal داخل الجدول تُعاد رسمها بعد كل تحديث Pjax (إضافة صنف أو تعديل خلية)، فالمعالج المربوط
// مباشرة على العناصر القديمة يضيع. لذلك نربط معالجاً مفوَّضاً على document يعمل للعناصر الجديدة.
// إن كان المعالج القديم قد عالج النقرة (استدعى preventDefault) نتجاهلها حتى لا تُحمَّل النافذة مرتين.
$this->registerJs(<<<'JS'
if (!window.__popupModalDelegated) {
    window.__popupModalDelegated = true;
    $(document).on('click', '.popupModal', function (e) {
        if (e.isDefaultPrevented()) { return; }
        e.preventDefault();
        $('#modal').modal('show').find('.modal-content').load($(this).attr('href'));
    });
}
JS, View::POS_READY, 'pur-popupmodal-delegate');
