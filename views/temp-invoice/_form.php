<?php

use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\TempInvoice $model */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\CompanyInfo $company */

$identity = Yii::$app->user->identity;

// ---------------------------------------------------------------------------
// إعدادات تعتمد على الشركة والمستخدم (تُقرأ مرة واحدة)
// ---------------------------------------------------------------------------
$roundPrices   = ($company->{"criteria\u{0640}value"} <> 0);   // تقريب الأسعار لأقرب 5 أو 10
$searchById    = ($company->searchById == 1);
$showWaitQnty  = ($company->waitQnty == 1 && $identity->client == null);
$seeCostPrice  = ($identity->seeCostPrice == 1);
$priceEditable = ($identity->editSalePrice == 1);
$simpleGrid    = ($identity->editSalePrice == 0);  // نفس شرط الكود القديم لاختيار الأعمدة

// ---------------------------------------------------------------------------
// إجماليات بطاقات الإحصاءات (حلقة واحدة) — لم تتغير المعادلات
// ---------------------------------------------------------------------------
$qtyTotal = 0;
$invoiceTotal = 0;
$profitTotal = 0;
foreach ($dataProvider->getModels() as $row) {
    $line = $row->quantity * $row->salePrice;
    $qtyTotal += $row->quantity;
    $invoiceTotal += $line;
    $profitTotal += $line - ($row->quantity * $row->costPrice);
}

// ---------------------------------------------------------------------------
// تنسيقات الصفحة
// ---------------------------------------------------------------------------
$this->registerCss(<<<'CSS'
.sx-stat { display:flex; align-items:center; gap:.6rem; padding:.35rem .75rem; border-radius:.6rem; line-height:1.15; height:100%; }
.sx-stat > i { font-size:1.35rem; }
.sx-stat .sx-lbl { font-size:.72rem; opacity:.85; }
.sx-stat .sx-val { font-size:1.05rem; font-weight:700; }
.sx-stat .sx-sub { font-size:.72rem; }
.sx-add .form-label { font-size:.8rem; margin-bottom:.15rem; color:var(--bs-secondary-color); }
.sx-add .mb-3 { margin-bottom:.6rem !important; }
.sx-add .select2-container .select2-selection--single { min-height:42px; }
.sx-grid .kv-grid-table > thead > tr > th { white-space:nowrap; }
@media (max-width: 575.98px) {
  .sx-head .btn { flex:1 1 auto; }
}
/* .chip-label {
    font-weight: 700;
    color: #111;
}
.text-bg-success .chip-label,
.text-bg-danger .chip-label,
.text-bg-info .chip-label {
    color: inherit;
} */
CSS);

// ---------------------------------------------------------------------------
// JavaScript موحّد
// ---------------------------------------------------------------------------
$this->registerJs(
    'window.salesCfg = ' . Json::htmlEncode([
        'round'   => $roundPrices,
        'seeCost' => $seeCostPrice,
        'invUrl'  => Url::to(['except/get-inv']),
        'gridUrl' => Url::to(['temp-invoice/create']),
    ]) . ';',
    View::POS_HEAD,
    'sales-form-cfg'
);

$this->registerJs(<<<'JS'
(function (w) {
    function esc(s) {
        if (s === null || s === undefined) { return ''; }
        return String(s).replace(/[&<>"']/g, function (c) {
            return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c];
        });
    }

    // تقريب لأقرب 5 أو 10 بنفس منطق الكود القديم: آخر رقم 1-4 => 5، آخر رقم 6-9 => 10.
    // الأسعار الأقل من 10 تبقى كما هي (هكذا كان يعمل الكود القديم فعلياً).
    function roundPrice(value) {
        var v = Math.round(parseFloat(value));
        if (isNaN(v)) { return value; }
        if (v < 10) { return v; }
        var d = v % 10;
        if (d !== 0 && d < 5) { v += 5 - d; }
        else if (d > 5) { v += 10 - d; }
        return v;
    }

    function price(value) {
        return w.salesCfg.round ? roundPrice(value) : value;
    }

   // شارة (عنوان: قيمة) بنفس أسلوب شاشة تسوية الجرد؛ الشارات الفارغة تُتجاهل
    function chip(label, value, cls) {
        if (value === null || value === undefined || value === '') { return ''; }
        return '<span class="badge ' + (cls || 'text-bg-light border') + ' fw-normal">' +
            '<span class="text-body-secondary">' + esc(label) + ':</span> <bdi>' + esc(value) + '</bdi></span>';
    }

// شكل الصنف داخل القائمة المنسدلة: الاسم في سطر، وتحته الشارات بترتيب ثابت
w.salesFormatProduct = function (product) {
    if (product.loading) { return esc(product.text); }

    var markup =
                    '<div class="fw-semibold mb-4">' + esc(product.text) + '</div>' +
        '<div class="d-flex flex-wrap gap-4">' +
            chip('التسلسل', product.id) +
            chip('رقم القطعة', product.serialNo) +
            chip('رقم القطعة التجاري', product.commCode) +
            chip('الكمية', product.quantity, 'text-bg-success') +
            (w.salesCfg.seeCost ? chip('سعر التكلفة', product.costPrice) : '') +
            chip('سعر البيع', price(product.maxPrice), 'text-bg-danger') +
            chip('السعر الأدنى', price(product.minPrice)) +
            chip('المكان', product.place) +
            chip('الحالة', product.type) +
            chip('الشركة', product.company, 'text-bg-warning') +
            chip('الفرع', product.BRNAME, 'text-bg-info') +
        '</div>';

    return '<div style="overflow:hidden;">' + markup + '</div>';
};

    w.salesFormatSelection = function (product) {
        return esc(product.name || product.text);
    };

    // بعد تعديل الكمية أو السعر من الجدول: حدّث بطاقات الإجماليات ثم الجدول (الإجمالي والمجموع السفلي)
    w.salesRefreshTotals = function () {
        $.pjax.reload({container: '#pjax-grid-view', async: false});
        $.pjax.reload({container: '#w0', url: w.salesCfg.gridUrl, async: true});
    };

    // عند اختيار صنف: تعبئة الكمية والسعر ومعرّف الصنف ثم نقل التركيز إلى الكمية
    w.salesPick = function (id) {
        if (!id) { return; }
        $.get(w.salesCfg.invUrl, {category: id}, function (data) {
            if (data != null) {
                $('#tempinvoice-quantity').val(1);
                $('#tempinvoice-saleprice').val(price(data.maxPrice));
                $('#tempinvoice-category').val(data.id);
                $('#tempinvoice-quantity').trigger('focus').trigger('select');
            }
        });
    };
})(window);
JS, View::POS_HEAD, 'sales-form-helpers');

// ---------------------------------------------------------------------------
// إعداد Select2 مشترك بين البحث بالاسم والبحث بالكود
// ---------------------------------------------------------------------------
$select2Options = static function (string $id, string $route): array {
    return [
        'language' => 'en',
        'options' => [
            'id' => $id,
            'placeholder' => Yii::t('app', 'Search...'),
            'dir' => 'rtl',
            'multiple' => false,
        ],
        'pluginOptions' => [
            'minimumInputLength' => 1,
            'ajax' => [
                'url' => Url::to([$route]),
                'dataType' => 'json',
                'data' => new JsExpression('function (params) { return {q: params.term, page: params.page}; }'),
                'processResults' => new JsExpression('function (data) { return {results: data.results}; }'),
                'cache' => true,
                // ينتظر توقف الكتابة 300ms قبل إرسال البحث بدل طلب مع كل حرف
                'delay' => 300,
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('salesFormatProduct'),
            'templateSelection' => new JsExpression('salesFormatSelection'),
        ],
        'pluginEvents' => [
            'change' => 'function (event) { salesPick(event.currentTarget.value); }',
        ],
    ];
};
?>

<div class="temp-invoice-form">

    <!-- العنوان + الأزرار في سطر واحد لتوفير المساحة الرأسية -->
    <div class="sx-head d-flex flex-wrap align-items-center gap-2 mb-2">
        <h4 class="mb-0 flex-grow-1 text-nowrap">
            <i class="bi bi-cart3 text-danger"></i> <?= Html::encode(Yii::t('app', 'Sales')) ?>
        </h4>

        <?= Html::button(
            '<i class="bi bi-plus-circle"></i> ' . Yii::t('app', 'Create Category'),
            ['value' => Url::to(['category/create-category']), 'class' => 'btn btn-sm btn-outline-primary popup']
        ) ?>

        <?= Html::button(
            '<i class="bi bi-lightning-charge"></i> ' . Yii::t('app', 'Adedd Fast'),
            ['value' => Url::to(['fast']), 'class' => 'btn btn-sm btn-info popup']
        ) ?>

        <?= Html::button(
            '<i class="bi bi-folder2-open"></i> ' . Yii::t('app', 'Holded Invoices'),
            ['value' => Url::to(['holded']), 'class' => 'btn btn-sm btn-outline-warning popup']
        ) ?>

        <?php // التعليق يغيّر البيانات فيُرسل POST 
        ?>
        <?= Html::a(
            '<i class="bi bi-pause-circle"></i> ' . Yii::t('app', 'Hold'),
            ['hold'],
            ['class' => 'btn btn-sm btn-warning', 'data' => ['method' => 'post']]
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
            ['value' => Url::to(['sales/create']), 'class' => 'btn btn-success fw-bold px-4 popup']
        ) ?>
    </div>

    <!-- إحصاءات مصغّرة: الـ row داخل Pjax لأن أعمدة Bootstrap 5 يجب أن تكون أبناء مباشرين لـ row -->
    <?php Pjax::begin(['id' => 'pjax-grid-view']); ?>
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
            <div class="sx-stat bg-success-subtle text-success-emphasis justify-content-between">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-cash-coin"></i>
                    <span>
                        <span class="sx-lbl d-block"><?= Html::encode(Yii::t('app', 'Total Invoice')) ?></span>
                        <span class="sx-val fs-5" dir="ltr"><?= number_format((float) $invoiceTotal, 3) ?></span>
                    </span>
                </span>
                <?php if ($seeCostPrice): ?>
                    <span class="text-end">
                        <span class="sx-lbl d-block"><?= Html::encode(Yii::t('app', 'Profit')) ?></span>
                        <span class="sx-sub fw-semibold" dir="ltr"><?= number_format((float) $profitTotal, 3) ?></span>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php Pjax::end(); ?>

    <!-- نموذج إضافة صنف -->
    <div class="card shadow-sm mb-3 sx-add">
        <div class="card-body py-2">
            <?php $form = ActiveForm::begin([
                'id' => 'temp-invoice-form',
                'enableAjaxValidation' => false,
                'enableClientValidation' => false,
                'options' => ['class' => 'search-form'],
            ]); ?>

            <div class="row g-1">
                <div class="<?= $searchById ? 'col-lg-12' : 'col-12' ?>">
                    <?= $form->field($model, 'kind')->widget(Select2::class, $select2Options('kind', '/sales/itemlist')) ?>
                </div>
            </div>
            <div class="row g-1">
                <?php if ($searchById): ?>
                    <div class="col-lg-12">
                        <?= $form->field($model, 'cat')->widget(Select2::class, $select2Options('cat', '/sales/itemlistid')) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="row g-2 align-items-end">
                <div class="col-6 col-md-2">
                    <?= $form->field($model, 'quantity')->textInput(['placeholder' => 'Quantity', 'inputmode' => 'decimal', 'autocomplete' => 'off']) ?>
                </div>

                <div class="col-6 col-md-2">
                    <?= $form->field($model, 'salePrice')->textInput([
                        'placeholder' => 'Sale Price',
                        'readonly' => !$priceEditable,
                        'inputmode' => 'decimal',
                        'autocomplete' => 'off',
                    ]) ?>
                </div>

                <?php if ($showWaitQnty): ?>
                    <div class="col-6 col-md-2">
                        <?= $form->field($model, 'waitQnty')->textInput(['placeholder' => 'Wait Qnty', 'inputmode' => 'decimal', 'autocomplete' => 'off']) ?>
                    </div>
                <?php endif; ?>

                <div class="col-12 col-md-auto mb-3">
                    <?= Html::submitButton(
                        $model->isNewRecord
                            ? '<i class="bi bi-plus-lg"></i> ' . Yii::t('app', 'Add')
                            : Yii::t('app', 'Update'),
                        ['class' => ($model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary') . ' w-100 px-4']
                    ) ?>
                </div>
            </div>

            <?php // حقلان مخفيان (يُبقى الحقل ليظهر خطأ "اختر صنفاً" عند الإرسال بدون اختيار) 
            ?>
            <?= $form->field($model, 'branch')->hiddenInput(['id' => 'branch'])->label(false) ?>
            <?= $form->field($model, 'category')->hiddenInput()->label(false) ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <?php
    // -----------------------------------------------------------------------
    // أعمدة الجدول: نسخة واحدة تتغير حسب صلاحية تعديل السعر
    // تنبيه: FormulaColumn يعتمد على ترتيب الأعمدة (col(6) = الكمية). لا تغيّر الترتيب بدون تعديل الأرقام.
    // -----------------------------------------------------------------------
    $cellStyle = $simpleGrid ? ['style' => 'font-size:14px;'] : [];
    $width = fn(int $simple, int $full): array => ['style' => 'width:' . ($simpleGrid ? $simple : $full) . '%'];
    // تحرير داخل الخلية بدل النافذة المنبثقة
    $popover = [
        'asPopover' => false,
        'bsVersion' => '5.x',
        'submitButton' => ['icon' => '<i class="bi bi-check-lg"></i>'],
        'resetButton'  => ['icon' => '<i class="bi bi-arrow-counterclockwise"></i>'],
        // بعد نجاح الحفظ: تحديث الإجماليات في الصفحة
        'pluginEvents' => ['editableSuccess' => new JsExpression('function () { salesRefreshTotals(); }')],
    ];

    $gridColumns = [
        ['class' => 'yii\grid\SerialColumn'],

        ['attribute' => 'id', 'visible' => false],

        [
            'attribute' => 'category',
            'label' => Yii::t('app', 'ID'),
            'contentOptions' => $cellStyle,
            'headerOptions' => $width(15, 5),
        ],

        [
            'attribute' => 'category',
            'headerOptions' => $width(40, 30),
            'format' => 'raw',
            'value' => function ($model) {
                $category = $model->category0;
                if ($category === null) {
                    return '';
                }
                return Html::a(
                    Html::encode(Yii::t('app', ' {modelClass}', ['modelClass' => $category->name])),
                    ['category/info', 'id' => $category->id],
                    // data-pjax=0: يمنع Pjax من اعتراض الرابط وفتحه داخل الجدول؛ تفتحه النافذة المنبثقة فقط
                    ['class' => 'btn-link popupModal fw-semibold', 'data-pjax' => '0']
                );
            },
        ],

        [
            'attribute' => 'category',
            'label' => Yii::t('app', 'Serial No'),
            'contentOptions' => $cellStyle,
            'headerOptions' => $width(15, 10),
            'value' => 'category0.serialNo',
        ],

        [
            'attribute' => 'category',
            'label' => Yii::t('app', 'Comm Code'),
            'contentOptions' => $cellStyle,
            'headerOptions' => $width(15, 10),
            'value' => 'category0.commCode',
        ],

        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'quantity',
            'label' => Yii::t('app', 'quantity'),
            'contentOptions' => $cellStyle,
            'headerOptions' => $width(15, 8),
            'editableOptions' => $popover,
            'pageSummary' => !$simpleGrid,
        ],
    ];

    // عمود waitQnty مرتبط بـ $showWaitQnty: يظهر عند 1 ويختفي عند 0 (ولا يظهر للعميل)
    if ($showWaitQnty) {
        $gridColumns[] = [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'waitQnty',
            'label' => Yii::t('app', 'Wait Qnty'),
            'contentOptions' => $cellStyle,
            'headerOptions' => $width(10, 8),
            'editableOptions' => $popover,
        ];
    }

    if (!$simpleGrid) {
        $gridColumns[] = [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'salePrice',
            'label' => Yii::t('app', 'Sale Price'),
            'headerOptions' => ['style' => 'width:8%'],
            'editableOptions' => $popover,
        ];
    } else {
        $gridColumns[] = [
            'attribute' => 'salePrice',
            'label' => Yii::t('app', 'Sale Price'),
            'contentOptions' => $cellStyle,
            'headerOptions' => ['style' => 'width:15%'],
        ];
    }

    $gridColumns[] = [
        'class' => 'kartik\grid\FormulaColumn',
        'header' => Yii::t('app', 'Total'),
        'vAlign' => 'middle',
        'value' => function ($model, $key, $index, $widget) use ($showWaitQnty) {
            $p = compact('model', 'key', 'index');
            // 6 = الكمية. السعر هو العمود 7، أو 8 إذا ظهر عمود waitQnty قبله
            return $widget->col(6, $p) * $widget->col($showWaitQnty ? 8 : 7, $p);
        },
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'hAlign' => 'right',
        'width' => $simpleGrid ? '15%' : '10%',
        'format' => ['decimal', 3],
        'mergeHeader' => true,
        'pageSummary' => true,
        'footer' => true,
    ];

    $gridColumns[] = [
        'class' => 'yii\grid\ActionColumn',
        'template' => '<div class="btn-group btn-group-sm" role="group">'
            . ($simpleGrid ? '{delete}' : '{delete}{image}')
            . '</div>',
        'buttons' => [
            'image' => function ($url, $model) {
                return Html::a(
                    '<i class="bi bi-image"></i>',
                    Url::to(['category/image', 'id' => $model['category']]),
                    ['class' => 'btn btn-outline-secondary btn-sm popupModal', 'data-pjax' => '0']
                );
            },
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
    ];
    ?>

    <div class="sx-grid">
        <?= GridView::widget([
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
        ]); ?>
    </div>

</div>

<?php
// روابط .popupModal داخل الجدول تُعاد رسمها بعد كل تحديث Pjax، فالمعالج المربوط مباشرة على العناصر القديمة يضيع.
// لذلك نربط معالجاً مفوَّضاً على document يعمل للعناصر الجديدة. إن كان المعالج القديم قد عالج النقرة
// (أي استدعى preventDefault) نتجاهلها حتى لا تُحمَّل النافذة مرتين.
$this->registerJs(<<<'JS'
if (!window.__popupModalDelegated) {
    window.__popupModalDelegated = true;
    $(document).on('click', '.popupModal', function (e) {
        if (e.isDefaultPrevented()) { return; }
        e.preventDefault();
        var $modal = $('#modal');
        $modal.modal('show').find('.modal-content').load($(this).attr('href'));
    });
}
JS, View::POS_READY, 'sales-popupmodal-delegate');

// تركيز تلقائي على حقل البحث
$focusId = $searchById ? 'cat' : 'kind';
$this->registerJs(
    "var s = $('#{$focusId}').data('select2'); if (s && s.\$selection) { s.\$selection.trigger('focus'); }",
    View::POS_READY,
    'sales-form-focus'
);
