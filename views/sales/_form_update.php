<?php

use app\models\Client;
use app\models\CompanyInfo;
use app\models\Currancy;
use app\models\PaymentTypes;
use kartik\date\DatePicker;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\Sales $model */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\CompanyInfo|null $company */

$identity = Yii::$app->user->identity;
$isClientUser = ($identity->client <> null);
$seeCostPrice = ($identity->seeCostPrice == 1);
$isReturn = ($model->type == 2);   // مسترجع مبيعات

// $company يصل عادةً من الكنترولر، ونجلبه هنا إن لم يصل
if (!isset($company)) {
    $company = CompanyInfo::find()->one();
}
$roundPrices = ($company->criteriaـvalue <> 0);   // تقريب الأسعار لأقرب 5 أو 10

// ---------------------------------------------------------------------------
// إجماليات البطاقات (حلقة واحدة بدل حلقتين)
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
// JavaScript موحّد لقائمة البحث عن الأصناف (مثل شاشة temp-invoice)
// ---------------------------------------------------------------------------
$this->registerJs(
    'window.salesCfg = ' . Json::htmlEncode([
        'round'    => $roundPrices,
        'addUrl'   => Url::to(['sales/add-item']),
        'salesId'  => $model->id,
        'infoPjax' => '#pjax-grid-view',                      // بطاقات الإجماليات
        'gridPjax' => '#kv-pjax-container-temp-invoice',      // الجدول
    ]) . ';',
    View::POS_HEAD,
    'sales-update-cfg'
);

$this->registerJs(<<<'JS'
(function (w) {
    function esc(s) {
        if (s === null || s === undefined) { return ''; }
        return String(s).replace(/[&<>"']/g, function (c) {
            return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c];
        });
    }

    // تقريب لأقرب 5 أو 10: آخر رقم 1-4 => 5، آخر رقم 6-9 => 10. الأسعار الأقل من 10 تبقى كما هي.
    function roundPrice(value) {
        var v = Math.round(parseFloat(value));
        if (isNaN(v)) { return value; }
        if (v < 10) { return v; }
        var d = v % 10;
        if (d !== 0 && d < 5) { v += 5 - d; }
        else if (d > 5) { v += 10 - d; }
        return v;
    }

    function price(value) { return w.salesCfg.round ? roundPrice(value) : value; }

    function field(label, valueHtml) {
        return '<span class="badge text-bg-primary rounded-pill">' + esc(label) + '</span> ' + valueHtml;
    }

    function pill(type, value) {
        return '<span class="badge text-bg-' + type + '">' + esc(value) + '</span>';
    }

    // شكل الصنف داخل القائمة المنسدلة
    w.salesFormatProduct = function (product) {
        if (product.loading) { return product.text; }

        // الخادم لا يرسل costPrice لمن لا يملك الصلاحية، ولا costPrice و minPrice للعملاء
        var hasCost = product.costPrice !== undefined && product.costPrice !== null;
        var hasMin  = product.minPrice !== undefined && product.minPrice !== null;
        var nameCols = 4 + (hasCost ? 0 : 2) + (hasMin ? 0 : 2);

        var markup =
            '<div class="row">' +
                '<div class="col-sm-' + nameCols + '"><b>' + esc(product.text) + '</b></div>' +
                (hasCost ? '<div class="col-sm-2">' + field('سعر التكلفة', esc(product.costPrice)) + '</div>' : '') +
                '<div class="col-sm-2">' + field('سعر البيع', pill('danger', price(product.maxPrice))) + '</div>' +
                (hasMin ? '<div class="col-sm-2">' + field('سعر البيع الأدنى', esc(price(product.minPrice))) + '</div>' : '') +
                '<div class="col-sm-2">' + field('مكان الصنف', esc(product.place)) + '</div>' +
            '</div>' +
            '<div class="row mt-2">' +
                '<div class="col-sm-2">' + field('رقم القطعة', esc(product.serialNo)) + '</div>' +
                '<div class="col-sm-2">' + field('رقم القطعة التجاري', esc(product.commCode)) + '</div>' +
                '<div class="col-sm-2">' + field('رقم التسلسل', esc(product.id)) + '</div>' +
                '<div class="col-sm-2">' + field('حالة القطعة', esc(product.type)) + '</div>' +
                '<div class="col-sm-2">' + field('الكمية', pill('success', product.quantity)) + '</div>' +
                '<div class="col-sm-2">' + field('الشركة', pill('warning', product.company)) + '</div>' +
            '</div>';

        return '<div style="overflow:hidden;">' + markup + '</div>';
    };

    w.salesFormatSelection = function (product) {
        return esc(product.name || product.text);
    };

    // تحديث بطاقات الإجماليات ثم الجدول (نفس التسلسل المستخدم في ajax-modal-popup.js)
    w.salesRefreshTotals = function () {
        $.pjax.reload({container: w.salesCfg.infoPjax, async: false});
        $.pjax.reload({container: w.salesCfg.gridPjax, async: true});
    };

    // إضافة الصنف المختار إلى الفاتورة
    w.salesAddItem = function (id) {
        if (!id) { return; }
        $.post(w.salesCfg.addUrl, {categoryid: id, salesId: w.salesCfg.salesId})
            .done(function () {
                // تفريغ الحقل ليمكن إضافة نفس الصنف مجدداً، ثم تحديث الصفحة وإعادة التركيز
                $('#kv-repo-template').val(null).trigger('change');
                w.salesRefreshTotals();
                $('#kv-repo-template').select2('open');
            })
            .fail(function (xhr) {
                // رسالة الخادم (رصيد غير كافٍ، صنف مكرر...) إن وُجدت
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'حدث خطأ اثناء الإضافة';
                $('#kv-repo-template').val(null).trigger('change');
                if (typeof swal === 'function') {
                    swal({title: 'منظومة المبيعات', text: msg, type: 'error', confirmButtonText: 'حسنا'});
                } else {
                    alert(msg);
                }
            });
    };
})(window);
JS, View::POS_HEAD, 'sales-update-helpers');

// ---------------------------------------------------------------------------
// العملاء: المستخدم المرتبط بعملاء يرى عملاءه فقط
// ---------------------------------------------------------------------------
$clientsQuery = Client::find()->where(['in', 'type', [0, 2]])->orderBy('id');
if ($isClientUser) {
    $clientsQuery->andWhere(['in', 'id', explode(',', $identity->client)]);
}
$clients = ArrayHelper::map($clientsQuery->asArray()->all(), 'id', 'name');

$payWay = $isReturn
    ? ['1' => 'نقدا', '2' => 'آجـــل']
    : ['0' => 'نقدا', '1' => 'آجـــل', '2' => 'دفعة على الحساب'];

$datePlugin = ['autoclose' => true, 'format' => 'yyyy-mm-dd', 'todayHighlight' => true];
?>

<div class="sales-form">

    <?php $form = ActiveForm::begin(); ?>

    <!-- الأزرار العلوية -->
    <div class="d-flex flex-wrap justify-content-between gap-2 mb-4">
        <div class="d-flex flex-wrap gap-2">
            <?= Html::submitButton(
                $model->isNewRecord
                    ? Yii::t('app', 'Create')
                    : '<i class="bi bi-pencil-square"></i> ' . Yii::t('app', 'Save'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
            ) ?>

            <?php if ($model->type != 2): ?>
                <?= Html::button(
                    '<i class="bi bi-lightning-charge"></i> ' . Yii::t('app', 'Fast'),
                    ['value' => Url::to(['fast', 'id' => $model->id]), 'class' => 'btn btn-info popup']
                ) ?>
            <?php endif; ?>

            <?= Html::a(
                '<i class="bi bi-x-circle"></i> ' . Yii::t('app', 'Back'),
                Yii::$app->request->referrer ?: ['index'],
                ['class' => 'btn btn-danger']
            ) ?>
        </div>

        <?= Html::a(
            '<i class="bi bi-trash"></i> ' . Yii::t('app', 'Delete All'),
            ['delete', 'id' => $model->id],
            [
                'class' => 'btn btn-outline-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]
        ) ?>
    </div>

    <!-- بطاقات الإجماليات: الـ row داخل Pjax وليس العكس (أعمدة Bootstrap 5 يجب أن تكون أبناء مباشرين لـ row) -->
    <?php Pjax::begin(['id' => 'pjax-grid-view']); ?>
    <div class="row info-boxes mb-4">
        <div class="col-md-4 col-sm-6">
            <div class="info-box text-bg-info shadow-sm">
                <span class="info-box-icon"><i class="bi bi-list-ol"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?= Yii::t('app', 'Items Count') ?></span>
                    <span class="info-box-number"><?= $dataProvider->getCount() ?></span>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="info-box text-bg-success shadow-sm">
                <span class="info-box-icon"><i class="bi bi-boxes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?= Yii::t('app', 'items quantity') ?></span>
                    <span class="info-box-number"><?= $qtyTotal ?></span>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="info-box text-bg-warning shadow-sm">
                <span class="info-box-icon"><i class="bi bi-cash-coin"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?= Yii::t('app', 'Total Invoice') ?></span>
                    <span class="info-box-number">
                        <?= number_format((float) $invoiceTotal, 3) ?>
                        <?php if ($seeCostPrice): ?>
                            <br><small><?= number_format((float) $profitTotal, 3) ?></small>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <?php Pjax::end(); ?>

    <?= $form->errorSummary($model) ?>

    <!-- بيانات الفاتورة -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <?= $form->field($model, 'type')
                ->dropDownList(['2' => 'مسترجع مبيعات', '1' => 'فاتورة نهائية', '3' => 'فاتورة حجز', '4' => 'فاتورة مبدئية'])
                ->label(Yii::t('app', 'Invoice Type')) ?>

            <?= $form->field($model, 'clinet')->widget(Select2::class, [
                'data' => $clients,
                'pluginOptions' => ['allowClear' => true],
            ]) ?>

            <?= $form->field($model, 'notes')->textInput(['maxlength' => true, 'placeholder' => 'Notes']) ?>

            <?php
            // id=todayis مطلوب: ajax-modal-popup.js يعبّئ تاريخ التسليم عند تحديده
            $deliveredOptions = ['id' => 'todayis'];
            if ($isReturn) {
                $deliveredOptions['checked'] = true;
            }
            echo $form->field($model, 'deleviried')->checkbox($deliveredOptions);
            ?>
        </div>

        <div class="col-lg-3 col-md-6">
            <?= $form->field($model, 'at')->widget(DatePicker::class, [
                'type' => DatePicker::TYPE_INPUT,
                'options' => ['placeholder' => 'Enter date ...'],
                'pluginOptions' => $datePlugin,
            ])->label('تاريخ الفاتورة') ?>

            <?= $form->field($model, 'payWay')->dropDownList($payWay, ['prompt' => 'اختيار طريقة الدفع']) ?>

            <?= $form->field($model, 'payment_type')->widget(Select2::class, [
                'data' => ArrayHelper::map(PaymentTypes::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => [
                    'placeholder' => 'الرجاء اختيار  طريقة الدفع ...',
                    'value' => $model->isNewRecord ? 1 : $model->payment_type,
                ],
                'pluginOptions' => ['allowClear' => true, 'multiple' => false],
            ]) ?>

            <?= $form->field($model, 'wholesale')->checkbox() ?>
        </div>

        <div class="col-lg-3 col-md-6">
            <?= $form->field($model, 'total')->textInput(['placeholder' => 'Total']) ?>

            <?= $form->field($model, 'disscount')->textInput(['placeholder' => 'Disscount']) ?>

            <?= $form->field($model, 'paid')->textInput([
                'placeholder' => 'Paid',
                'disabled' => $isClientUser,
            ]) ?>
        </div>

        <div class="col-lg-3 col-md-6">
            <?= $form->field($model, 'deleviryAt')->widget(DatePicker::class, [
                'type' => DatePicker::TYPE_INPUT,
                'language' => 'ar',
                'pluginOptions' => $datePlugin + ['todayBtn' => true],
            ])->label('تاريخ التسليم') ?>

            <?= $form->field($model, 'currancy')->widget(Select2::class, [
                'data' => ArrayHelper::map(Currancy::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'pluginOptions' => ['allowClear' => true, 'multiple' => false],
            ]) ?>

            <?= $form->field($model, 'deserving')->widget(DatePicker::class, [
                'type' => DatePicker::TYPE_INPUT,
                'options' => ['placeholder' => 'Enter Deserving ...'],
                'pluginOptions' => $datePlugin,
            ])->label('تاريخ الاستحقاق') ?>
        </div>
    </div>

    <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>

    <?php ActiveForm::end(); ?>

    <!-- البحث عن صنف وإضافته (كان داخل نموذج ثانٍ بلا حاجة) -->
    <div class="mb-4">
        <?= Select2::widget([
            'name' => 'kv-repo-template',
            'language' => 'en',
            'options' => [
                'id' => 'kv-repo-template',
                'placeholder' => Yii::t('app', 'Search...'),
                'dir' => 'rtl',
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'ajax' => [
                    'url' => Url::to(['/sales/itemlist']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function (params) { return {q: params.term, page: params.page}; }'),
                    'processResults' => new JsExpression('function (data) { return {results: data.results}; }'),
                    'cache' => true,
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('salesFormatProduct'),
                'templateSelection' => new JsExpression('salesFormatSelection'),
            ],
            'pluginEvents' => [
                'change' => 'function (event) { salesAddItem(event.currentTarget.value); }',
            ],
        ]) ?>
    </div>

    <?php
    // تحرير داخل الخلية (بدل النافذة المنبثقة) وتحديث الإجماليات بعد كل حفظ ناجح
    $popover = [
        'asPopover' => false,
        'bsVersion' => '5.x',
        'submitButton' => ['icon' => '<i class="bi bi-check-lg"></i>'],
        'resetButton' => ['icon' => '<i class="bi bi-arrow-counterclockwise"></i>'],
        'pluginEvents' => ['editableSuccess' => new JsExpression('function () { salesRefreshTotals(); }')],
    ];

    // تنبيه: FormulaColumn يعتمد على ترتيب الأعمدة (6 = الكمية، 7 = السعر). لا تغيّر الترتيب بدون تعديل الأرقام.
    $gridColumns = [
        ['class' => 'yii\grid\SerialColumn'],

        ['attribute' => 'id', 'visible' => false],

        // رقم الصنف للعرض فقط: تغيير الصنف داخل فاتورة محفوظة يغيّر المخزون ولا يُسمح به (احذف السطر وأضف من جديد)
        [
            'attribute' => 'category',
            'label' => Yii::t('app', 'ID'),
            'contentOptions' => ['style' => 'font-size:14px;'],
            'headerOptions' => ['style' => 'width:10%'],
        ],

        [
            'attribute' => 'category',
            'headerOptions' => ['style' => 'width:20%'],
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
                    ['class' => 'btn-link popupModal']
                );
            },
        ],

        [
            'label' => Yii::t('app', 'Serial No'),
            'value' => function ($data) {
                return $data->category0?->serialNo;
            },
        ],

        'category0.company',

        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'quantity',
            'label' => Yii::t('app', 'quantity'),
            'editableOptions' => $popover,
        ],

        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'salePrice',
            'label' => Yii::t('app', 'Sale Price'),
            'editableOptions' => $popover,
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
            'width' => '7%',
            'format' => ['decimal', 3],
            'mergeHeader' => true,
            'pageSummary' => true,
            'footer' => true,
        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '<div class="btn-group btn-group-sm" role="group">{remove}{transfer}</div>',
            'buttons' => [
                'remove' => function ($url, $model) {
                    return Html::a(
                        '<i class="bi bi-trash"></i>',
                        Url::to(['sales/remove', 'id' => $model['id']]),
                        [
                            'title' => Yii::t('yii', 'Delete'),
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
                        Url::to(['sales/transfer-to-temp-invoice', 'id' => $model['id']]),
                        [
                            'title' => Yii::t('app', 'Transfer'),
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

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => true,
        'columns' => $gridColumns,
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => ['id' => 'kv-pjax-container-temp-invoice'],
        ],
        'showPageSummary' => true,
        'panel' => ['type' => GridView::TYPE_PRIMARY],
    ]) ?>

</div>

<?php
// فتح روابط .popupModal (اسم الصنف) في النافذة. الربط على document لأن الجدول يُعاد رسمه بـ Pjax،
// ويتجاهل الحدث إن عالجه ajax-modal-popup.js أصلاً (لتفادي فتح النافذة مرتين).
$this->registerJs(<<<'JS'
$(document).off('click.salesPopup').on('click.salesPopup', '.popupModal', function (e) {
    if (e.isDefaultPrevented()) { return; }
    e.preventDefault();
    $('#modal').modal('show').find('.modal-content').load($(this).attr('href'));
});
JS, View::POS_READY, 'sales-update-popup');
