<?php

use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\TempReorder */
/* @var $dataProvider yii\data\ActiveDataProvider */

// معرّف Pjax الفعلي للجدول في الأصل (كان pjaxSettings مكرراً والأخير هو الذي يُطبَّق)
$pjaxId = 'kv-pjax-container-temp-invoice-purchase';

// ---- تنسيق عناصر القائمة المنسدلة (الاسمان formatProduct و formatProductSelection كما كانا) ----
$this->registerJs(<<<'JS'
var esc = function (v) {
    return (v === undefined || v === null) ? '' : $('<div>').text(v).html();
};
var chip = function (label, value, cls) {
    if (value === undefined || value === null || value === '') { return ''; }
    return '<span class="badge ' + (cls || 'text-bg-light border') + ' fw-normal">' +
        '<span class="text-body-secondary">' + label + ':</span> ' + esc(value) + '</span>';
};

var formatProduct = function (product) {
    if (product.loading) {
        return esc(product.text);
    }
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

var formatProductSelection = function (product) {
    return esc(product.name || product.text);
};
JS, View::POS_HEAD, 'reorder-format');

// تحرير الكمية داخل الخلية (كان نافذة منبثقة تُقصّ داخل حاوية الجدول) مع إعادة رسم الجدول بعد كل حفظ
$editable = [
    'asPopover' => false,
    'bsVersion' => '5.x',
    'submitButton' => ['icon' => '<i class="bi bi-check-lg"></i>'],
    'resetButton' => ['icon' => '<i class="bi bi-arrow-counterclockwise"></i>'],
    'pluginEvents' => [
        'editableSuccess' => new JsExpression("function () { $.pjax.reload({container: '#{$pjaxId}'}); }"),
    ],
];

$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],

    ['attribute' => 'id', 'visible' => false],

    [
        'attribute' => 'category',
        'label' => Yii::t('app', 'Category'),
        'headerOptions' => ['style' => 'width:45%'],
        'width' => '50%',
        'contentOptions' => ['class' => 'fw-semibold'],
        'value' => fn($model) => $model->category0?->name,
    ],

    [
        'label' => Yii::t('app', 'Serial No'),
        'width' => '20%',
        'value' => fn($model) => $model->category0?->serialNo,
    ],

    [
        'class' => 'kartik\grid\EditableColumn',
        'width' => '20%',
        'attribute' => 'quantity',
        'label' => Yii::t('app', 'quantity'),
        'editableOptions' => $editable,
        'format' => ['decimal', 3],
        'pageSummary' => true,
        'footer' => true,
    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{delete}',
        'headerOptions' => ['style' => 'width:1%'],
        'contentOptions' => ['class' => 'text-nowrap'],
        'buttons' => [
            // كان الزر الوحيد المعرَّف save-as-new بأيقونات glyphicon ولا يظهر أصلاً (القالب {delete} فقط) فحُذف
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
?>

<div class="temp-reorder-form">

    <!-- شريط الإجراءات -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <?= Html::button(
            '<i class="bi bi-plus-circle"></i> ' . Yii::t('app', 'Create Category'),
            ['value' => Url::to(['category/create-category']), 'class' => 'btn btn-outline-primary popup']
        ) ?>

        <?= Html::button(
            '<i class="bi bi-check2-circle"></i> ' . Yii::t('app', 'Complete'),
            ['value' => Url::to(['reorder/create']), 'class' => 'btn btn-success popup']
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

    <!-- إضافة صنف -->
    <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-cart-plus"></i> إضافة صنف لقائمة إعادة الطلب</div>
        <div class="card-body">
            <?php // enableClientValidation كان موضوعاً بالخطأ داخل options (يُطبع كخاصية HTML) فنقلته إلى مستواه الصحيح 
            ?>
            <?php $form = ActiveForm::begin([
                'id' => 'temp-reorder-form',
                'enableClientValidation' => false,
            ]); ?>

            <?= $form->errorSummary($model, ['class' => 'alert alert-danger py-2 small']) ?>

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
                    'templateResult' => new JsExpression('formatProduct'),
                    'templateSelection' => new JsExpression('formatProductSelection'),
                ],
                // بعد اختيار الصنف ينتقل المؤشر إلى حقل الكمية
                'pluginEvents' => [
                    'select2:select' => 'function () { setTimeout(function () { $("#' . Html::getInputId($model, 'quantity') . '").trigger("focus").trigger("select"); }, 50); }',
                ],
            ]) ?>

            <div class="row g-2 align-items-end">
                <div class="col-8 col-md-4">
                    <?= $form->field($model, 'quantity')->textInput(['inputmode' => 'decimal', 'autocomplete' => 'off', 'placeholder' => '0']) ?>
                </div>
                <div class="col-4 col-md-auto mb-3">
                    <?= Html::submitButton('<i class="bi bi-plus-lg"></i> ' . Yii::t('app', 'Add'), ['class' => 'btn btn-success w-100 px-4']) ?>
                </div>
            </div>

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
// تركيز تلقائي على حقل البحث (الكود القديم كان يستهدف #focus_first الذي لا يُنشأ فلا يعمل)
$this->registerJs(
    "var s = $('#" . Html::getInputId($model, 'category') . "').data('select2'); if (s && s.\$selection) { s.\$selection.trigger('focus'); }",
    View::POS_READY,
    'reorder-focus'
);
