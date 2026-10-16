<?php

use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var app\models\TempArrangement $model */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var yii\bootstrap5\ActiveForm $form */

$pjaxId = 'kv-pjax-container-temp-transfer-items';   // معرّف Pjax الفعلي للجدول في الأصل

// ---- تنسيق عناصر القائمة المنسدلة (يبقى الاسمان formatProduct و formatProductSelection كما كانا) ----
$formatJs = <<<'JS'
var esc = function (v) {
    return (v === undefined || v === null) ? '' : $('<div>').text(v).html();
};
var chip = function (label, value, cls) {
    return '<span class="badge ' + (cls || 'text-bg-light border') + ' fw-normal">' +
        '<span class="text-body-secondary">' + label + ':</span> ' + esc(value) + '</span>';
};

var formatProduct = function (product) {
    if (product.loading) {
        return product.text;
    }
    var markup =
        '<div class="fw-semibold mb-1">' + esc(product.text) + '</div>' +
        '<div class="d-flex flex-wrap gap-5">' +
            chip('سعر البيع', product.maxPrice) +
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

var formatProductSelection = function (product) {
    return esc(product.name || product.text);
};
JS;
$this->registerJs($formatJs, $this::POS_HEAD);

// تحويل نتائج البحث إلى الشكل الذي يتوقعه Select2
$resultsJs = <<<'JS'
function (data, params) {
    params.page = params.page || 5;
    return {
        results: data.results
    };
}
JS;

// عند اختيار صنف: جلب كميته الحالية وتعبئة حقل "الكمية الفعلية" (نفس السلوك السابق)
$changeJs = 'function (event) {
    var id = event.currentTarget.value;
    $.get(' . json_encode(Url::to(['temp-arrangement/get-inv'])) . ', {category: id}, function (data) {
        if (data != null && data.quantity !== undefined) {
            $("#temparrangement-realquantity").val(data.quantity);
        }
    });
}';

// تحرير الكمية داخل الخلية مع إعادة رسم الجدول بعد كل حفظ ناجح
$editable = [
    'asPopover' => false,
    'bsVersion' => '5.x',
    'submitButton' => ['icon' => '<i class="bi bi-check-lg"></i>'],
    'resetButton' => ['icon' => '<i class="bi bi-arrow-counterclockwise"></i>'],
    'pluginEvents' => [
        'editableSuccess' => new JsExpression("function () { $.pjax.reload({container: '#{$pjaxId}'}); }"),
    ],
];

// نوع الحركة: 1 إضافة للمخزون، -1 إنقاص من المخزون
$typeBadges = [
    '1' => ['اضافة للمخزون', 'text-bg-success'],
    '-1' => ['انقاص من المخزون', 'text-bg-danger'],
];

$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],

    ['attribute' => 'id', 'visible' => false],

    [
        'attribute' => 'category0.serialNo',
        'width' => '13%',
        'value' => function ($model) {
            return $model->category0?->serialNo;
        },
    ],

    [
        'attribute' => 'category',
        'headerOptions' => ['style' => 'width:30%'],
        'format' => 'raw',
        'value' => function ($model) {
            $category = $model->category0;
            if ($category === null) {
                return '';
            }
            return Html::a(
                Html::encode($category->name),
                ['category/change-place', 'id' => $category->id],
                ['class' => 'btn-link popupModal', 'data-pjax' => '0']
            );
        },
    ],

    [
        'attribute' => 'category0.place',
        'width' => '15%',
        'format' => 'raw',
        'value' => function ($model) {
            $place = $model->category0?->place;
            if ($place == null) {
                return '<i class="bi bi-dash text-body-secondary"></i>';
            }
            return Html::encode($place);
        },
    ],

    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'quantity',
        'label' => Yii::t('app', 'quantity'),
        'editableOptions' => $editable,
        'width' => '15%',
        'format' => ['decimal', 3],
        'pageSummary' => true,
        'footer' => true,
    ],

    [
        'attribute' => 'type',
        'width' => '10%',
        'format' => 'raw',
        'value' => function ($model) use ($typeBadges) {
            $key = (string) $model->type;
            if (!isset($typeBadges[$key])) {
                return Html::encode($key);
            }
            [$text, $class] = $typeBadges[$key];
            return '<span class="badge ' . $class . '">' . Html::encode($text) . '</span>';
        },
    ],

    [
        'attribute' => 'stockTaking',
        'width' => '10%',
        'format' => 'raw',
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'value' => function ($model) {
            return ((int) $model->stockTaking === 1)
                ? '<i class="bi bi-check-circle-fill text-success"></i>'
                : '<i class="bi bi-x-circle text-body-secondary"></i>';
        },
    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{delete}',
        'headerOptions' => ['style' => 'width:1%'],
        'contentOptions' => ['class' => 'text-nowrap'],
        'buttons' => [
            'delete' => function ($url) {
                return Html::a('<i class="bi bi-trash"></i>', $url, [
                    'class' => 'btn btn-sm btn-outline-danger',
                    'title' => Yii::t('app', 'Delete'),
                    'aria-label' => Yii::t('app', 'Delete'),
                    'data-pjax' => '0',
                    'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                ]);
            },
        ],
    ],
];

// مفتاح "جرد": يبقى غير مفعّل افتراضياً كما في الأصل
$stockTakingInput = Html::activeCheckbox($model, 'stockTaking', [
    'class' => 'form-check-input',
    'role' => 'switch',
    'label' => null,
    'checked' => false,
]);
?>

<div class="temp-arrangement-form">

    <!-- شريط الإجراءات -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <?= Html::button(
            '<i class="bi bi-check2-circle"></i> ' . Yii::t('app', 'Complete'),
            ['value' => Url::to(['arrangement/create']), 'class' => 'btn btn-danger popup']
        ) ?>

        <?= Html::a(
            '<i class="bi bi-trash"></i> ' . Yii::t('app', 'Delete All'),
            ['delete-all'],
            [
                'class' => 'btn btn-outline-warning ms-auto',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]
        ) ?>
    </div>

    <!-- إضافة صنف للتسوية -->
    <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-clipboard-check"></i> إضافة صنف لتسوية الجرد</div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(['id' => 'temp-arrangement-form']); ?>

            <?= $form->errorSummary($model) ?>

            <?= $form->field($model, 'id', ['template' => '{input}'])->hiddenInput() ?>

            <?= $form->field($model, 'category')->widget(Select2::class, [
                'language' => 'ar',
                'options' => [
                    'placeholder' => Yii::t('app', 'Search...'),
                    'dir' => 'rtl',
                    'multiple' => false,
                    'autofocus' => true,
                ],
                'pluginOptions' => [
                    'minimumInputLength' => 1,
                    'ajax' => [
                        'url' => Url::to(['/temp-arrangement/itemlist']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term, page: params.page}; }'),
                        'processResults' => new JsExpression($resultsJs),
                        'cache' => true,
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('formatProduct'),
                    'templateSelection' => new JsExpression('formatProductSelection'),
                ],
                'pluginEvents' => [
                    'change' => new JsExpression($changeJs),
                ],
            ]) ?>

            <div class="row g-3">
                <div class="col-6 col-lg-3">
                    <?= $form->field($model, 'realQuantity')->textInput(['placeholder' => 'Real Quantity', 'inputmode' => 'decimal']) ?>
                </div>
                <div class="col-6 col-lg-3">
                    <?= $form->field($model, 'quantity')->textInput(['placeholder' => 'Quantity', 'inputmode' => 'decimal']) ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'type')->dropDownList(
                        ['1' => 'اضافة للمخزون', '-1' => 'انقاص من المخزون'],
                        ['prompt' => Yii::t('app', 'Transaction Type')]
                    )->label(Yii::t('app', 'Transaction Type')) ?>
                </div>
                <div class="col-lg-2 d-flex align-items-center">
                    <div class="form-check form-switch mb-3">
                        <?= $stockTakingInput ?>
                        <label class="form-check-label" for="<?= Html::getInputId($model, 'stockTaking') ?>">
                            <?= Html::encode($model->getAttributeLabel('stockTaking')) ?>
                        </label>
                    </div>
                </div>
            </div>

            <?= Html::submitButton(
                '<i class="bi bi-plus-lg"></i> ' . Yii::t('app', 'Add'),
                ['class' => 'btn btn-success px-4']
            ) ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <!-- الأصناف المضافة -->
    <div class="card">
        <div class="card-header fw-semibold"><i class="bi bi-list-check"></i> الأصناف المضافة</div>
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => '{items}{pager}',
                'summary' => true,
                'columns' => $gridColumns,
                'bordered' => false,
                'striped' => true,
                'condensed' => true,
                'hover' => true,
                'responsive' => true,
                'pjax' => true,
                'pjaxSettings' => [
                    'neverTimeout' => true,
                    'options' => ['id' => $pjaxId],
                ],
                'showPageSummary' => true,
            ]) ?>
        </div>
    </div>
</div>

<?php
// فتح رابط اسم الصنف (تغيير المكان) في النافذة. الربط على document لأن الجدول يُعاد رسمه بـ Pjax،
// ويتجاهل الحدث إن عالجه ajax-modal-popup.js أصلاً (لتفادي فتح النافذة مرتين).
$this->registerJs(<<<'JS'
$(document).off('click.taPopup').on('click.taPopup', '.popupModal', function (e) {
    if (e.isDefaultPrevented()) { return; }
    e.preventDefault();
    $('#modal').modal('show').find('.modal-content').load($(this).attr('href'));
});
JS, \yii\web\View::POS_READY, 'ta-popup');
?>