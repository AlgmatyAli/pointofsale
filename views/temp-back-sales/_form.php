<?php

use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\TempInvoice $model */
/** @var yii\data\ActiveDataProvider $dataProvider */

// ---------------------------------------------------------------------------
// إجماليات البطاقات: عدد الأصناف + مجموع الكميات + الإجمالي (كمية × سعر البيع)
// ---------------------------------------------------------------------------
$qtyTotal = 0;
$invoiceTotal = 0;
foreach ($dataProvider->getModels() as $row) {
    $qtyTotal += $row->quantity;
    $invoiceTotal += $row->quantity * $row->salePrice;
}

$this->registerCss(<<<'CSS'
.sx-stat { display:flex; align-items:center; gap:.6rem; padding:.35rem .75rem; border-radius:.6rem; line-height:1.15; height:100%; }
.sx-stat > i { font-size:1.35rem; }
.sx-stat .sx-lbl { font-size:.72rem; opacity:.85; }
.sx-stat .sx-val { font-size:1.05rem; font-weight:700; }
.sx-search .select2-container .select2-selection--single { min-height:44px; }
@media (max-width: 575.98px) { .sx-head .btn { flex:1 1 auto; } }
CSS);

// ---------------------------------------------------------------------------
// إعدادات وJavaScript موحّد (نفس أسلوب شاشة المبيعات)
// ---------------------------------------------------------------------------
$this->registerJs(
    'window.backCfg = ' . Json::htmlEncode([
        'addUrl'   => Url::to(['temp-back-sales/add']),
        'gridPjax' => '#kv-pjax-container-temp-invoice',
        'statsPjax' => '#pjax-back-stats',
    ]) . ';',
    View::POS_HEAD,
    'back-form-cfg'
);

$this->registerJs(<<<'JS'
(function (w) {
    function esc(s) {
        if (s === null || s === undefined) { return ''; }
        return String(s).replace(/[&<>"']/g, function (c) {
            return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c];
        });
    }

    // شارة (عنوان: قيمة)؛ الشارات الفارغة تُتجاهل — فالسعر الأدنى مثلاً قد لا يصل من الخادم (للعملاء)
    function chip(label, value, cls) {
        if (value === null || value === undefined || value === '') { return ''; }
        return '<span class="badge ' + (cls || 'text-bg-light border') + ' fw-normal">' +
            '<span class="text-body-secondary">' + esc(label) + ':</span> ' + esc(value) + '</span>';
    }

    // شكل الصنف داخل القائمة المنسدلة: الاسم في سطر وتحته الشارات
    w.backFormatProduct = function (product) {
        if (product.loading) { return esc(product.text); }

        var markup =
            '<div class="fw-semibold mb-1">' + esc(product.text) + '</div>' +
            '<div class="d-flex flex-wrap gap-5">' +
                chip('سعر البيع', product.maxPrice, 'text-bg-danger') +
                chip('السعر الأدنى', product.minPrice) +
                chip('المكان', product.place) +
                chip('رقم القطعة 1', product.serialNo) +
                chip('رقم القطعة 2', product.commCode) +
                chip('التسلسل', product.id) +
                chip('الشركة', product.company) +
                chip('الكمية', product.quantity, 'text-bg-success') +
            '</div>';

        return '<div style="overflow:hidden;">' + markup + '</div>';
    };

    w.backFormatSelection = function (product) {
        return esc(product.name || product.text);
    };

    // إعادة تحميل البطاقات ثم الجدول (الإجمالي في الصف والمجموع السفلي)
    w.backRefresh = function () {
        $.pjax.reload({container: w.backCfg.statsPjax, async: false});
        $.pjax.reload({container: w.backCfg.gridPjax});
    };

    // إضافة الصنف المختار إلى فاتورة المسترجع
    w.backAddItem = function (id) {
        if (!id) { return; }
        $.get(w.backCfg.addUrl, {categoryid: id, type: 1})
            .done(function () {
                // تفريغ الحقل ليمكن إضافة نفس الصنف مجدداً، ثم تحديث الجدول وإعادة التركيز
                $('#focus_first').val(null).trigger('change');
                w.backRefresh();
                $('#focus_first').select2('open');
            })
            .fail(function () {
                if (typeof swal === 'function') {
                    swal({title: 'منظومة المبيعات', text: 'حدث خطأ اثناء الإضافة', type: 'error', confirmButtonText: 'حسنا'});
                } else {
                    alert('حدث خطأ اثناء الإضافة');
                }
            });
    };
})(window);
JS, View::POS_HEAD, 'back-form-helpers');

// تحرير داخل الخلية (بدل النافذة المنبثقة) وتحديث الجدول بعد كل حفظ ناجح
$editable = [
    'asPopover' => false,
    'bsVersion' => '5.x',
    'submitButton' => ['icon' => '<i class="bi bi-check-lg"></i>'],
    'resetButton' => ['icon' => '<i class="bi bi-arrow-counterclockwise"></i>'],
    'pluginEvents' => ['editableSuccess' => new JsExpression('function () { backRefresh(); }')],
];

// تنبيه: FormulaColumn يعتمد على ترتيب الأعمدة (6 = الكمية، 7 = السعر). لا تغيّر الترتيب بدون تعديل الأرقام.
$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],

    ['attribute' => 'id', 'visible' => false],

    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'category',
        'label' => Yii::t('app', 'ID'),
        'contentOptions' => ['style' => 'font-size:14px;'],
        'headerOptions' => ['style' => 'width:15%'],
        'editableOptions' => $editable,
    ],

    [
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
                ['class' => 'btn-link popupModal fw-semibold']
            );
        },
    ],

    [
        'attribute' => 'serial_number',
        'label' => Yii::t('app', 'Serial No'),
        'contentOptions' => ['style' => 'font-size:14px;'],
        'headerOptions' => ['style' => 'width:15%'],
        'value' => 'category0.serialNo',
    ],

    [
        'attribute' => 'serial_number',
        'label' => Yii::t('app', 'Comm Code'),
        'contentOptions' => ['style' => 'font-size:14px;'],
        'headerOptions' => ['style' => 'width:15%'],
        'value' => 'category0.commCode',
    ],

    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'quantity',
        'label' => Yii::t('app', 'quantity'),
        'contentOptions' => ['style' => 'font-size:14px;'],
        'headerOptions' => ['style' => 'width:15%'],
        'editableOptions' => $editable,
    ],

    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'salePrice',
        'label' => Yii::t('app', 'Sale Price'),
        'contentOptions' => ['style' => 'font-size:14px;'],
        'headerOptions' => ['style' => 'width:15%'],
        'editableOptions' => $editable,
    ],

    [
        'class' => 'kartik\grid\FormulaColumn',
        'header' => Yii::t('app', 'Total'),
        'vAlign' => 'middle',
        'value' => function ($model, $key, $index, $widget) {
            $p = compact('model', 'key', 'index');
            return $widget->col(6, $p) * $widget->col(7, $p);
        },
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'hAlign' => 'right',
        'width' => '15%',
        'format' => ['decimal', 3],
        'mergeHeader' => true,
        'pageSummary' => true,
        'footer' => true,
    ],

    [
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

<div class="temp-invoice-form">

    <!-- العنوان + الأزرار في سطر واحد -->
    <div class="sx-head d-flex flex-wrap align-items-center gap-2 mb-2">
        <h4 class="mb-0 flex-grow-1 text-nowrap">
            <i class="bi bi-arrow-return-left text-danger"></i> مرتجع مبيعات
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
            '<i class="bi bi-check2-circle"></i> ' . Yii::t('app', 'Complete Sale'),
            ['value' => Url::to(['sales/back-create']), 'class' => 'btn btn-success fw-bold px-4 popup']
        ) ?>
    </div>

    <!-- إحصاءات مصغّرة (داخل Pjax خاص بها كي تتحدث بعد كل إضافة أو تعديل) -->
    <?php Pjax::begin(['id' => 'pjax-back-stats']); ?>
    <div class="row g-2 mb-2">
        <div class="col-6 col-md-3">
            <div class="sx-stat bg-info-subtle text-info-emphasis">
                <i class="bi bi-list-ol"></i>
                <div>
                    <div class="sx-lbl"><?= Html::encode(Yii::t('app', 'Items Count')) ?></div>
                    <div class="sx-val"><?= (int) $dataProvider->getCount() ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="sx-stat bg-primary-subtle text-primary-emphasis">
                <i class="bi bi-boxes"></i>
                <div>
                    <div class="sx-lbl"><?= Html::encode(Yii::t('app', 'items quantity')) ?></div>
                    <div class="sx-val"><?= Html::encode((string) $qtyTotal) ?></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="sx-stat bg-danger-subtle text-danger-emphasis">
                <i class="bi bi-cash-coin"></i>
                <div>
                    <div class="sx-lbl"><?= Html::encode(Yii::t('app', 'Total Invoice')) ?></div>
                    <div class="sx-val fs-5" dir="ltr"><?= number_format((float) $invoiceTotal, 3) ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php Pjax::end(); ?>

    <!-- البحث عن صنف وإضافته -->
    <div class="card shadow-sm mb-3 sx-search">
        <div class="card-body py-2">
            <label class="form-label small text-body-secondary mb-1" for="focus_first">
                <i class="bi bi-search"></i> ابحث عن صنف لإضافته للمرتجع
            </label>
            <?= Select2::widget([
                'name' => 'kv-repo-template',
                'id' => 'focus_first',
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
                    'templateResult' => new JsExpression('backFormatProduct'),
                    'templateSelection' => new JsExpression('backFormatSelection'),
                ],
                'pluginEvents' => [
                    'change' => 'function (event) { backAddItem(event.currentTarget.value); }',
                ],
            ]) ?>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{items}{pager}',
        'summary' => true,
        'columns' => $gridColumns,
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => ['id' => 'kv-pjax-container-temp-invoice'],
        ],
        'showPageSummary' => true,
        'condensed' => true,
        'hover' => true,
        'striped' => true,
        'bordered' => false,
    ]) ?>

</div>

<?php
// تركيز تلقائي على حقل البحث عند تحميل الصفحة
$this->registerJs(
    "var s = $('#focus_first').data('select2'); if (s && s.\$selection) { s.\$selection.trigger('focus'); }",
    View::POS_READY,
    'back-form-focus'
);
