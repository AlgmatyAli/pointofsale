<?php

use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var app\models\TempTransferItems $model */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var yii\bootstrap5\ActiveForm $form */

$pjaxId = 'w1';   // معرّف Pjax للجدول (كما في الأصل)

// ---- تنسيق عناصر القائمة المنسدلة (يبقى الاسمان formatProduct و formatProductSelection كما كانا) ----
$formatJs = <<<'JS'
var esc = function (v) {
    return (v === undefined || v === null) ? '' : $('<div>').text(v).html();
};
var chip = function (label, value, cls) {
    return '<span class="badge ' + (cls || 'text-bg-light border') + ' fw-normal">' +
        '<span class="text-body-secondary">' + label + ':</span> <bdi>' + esc(value) + '</bdi></span>';
};

var formatProduct = function (product) {
    if (product.loading) {
        return product.text;
    }
    var markup =
        '<div class="fw-semibold mb-1">' + esc(product.text) + '</div>' +
        '<div class="d-flex flex-wrap gap-3">' +
            chip('سعر البيع', product.maxPrice) +
            chip('السعر الأدنى', product.minPrice) +
            chip('المكان', product.place) +
            chip('رقم القطعة 1', product.serialNo) +
            chip('رقم القطعة 2', product.commCode) +
            chip('التسلسل', product.id) +
            chip('الشركة', product.company) +
            chip('الكمية', product.quantity, 'text-bg-success') +
            chip('الفرع', product.BRNAME, 'text-bg-info') +
            '<span class="d-none">' + esc(product.branchId) + '</span>' +
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

// تحرير الكمية داخل الخلية (بدل النافذة المنبثقة) مع إعادة رسم الجدول بعد كل حفظ ناجح
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
    ['attribute' => 'id', 'visible' => false],

    [
        'label' => Yii::t('app', 'ID'),
        'headerOptions' => ['style' => 'width:10%'],
        'value' => function ($data) {
            return \yii\helpers\Html::encode($data->category);
        },
    ],

    [
        'attribute' => Yii::t('app', 'Category'),
        'headerOptions' => ['style' => 'width:30%'],
        'format' => 'raw',
        'value' => function ($data) {
            $category = $data->category0;
            if ($category === null) {
                return '';
            }
            return Html::a(
                Html::encode($category->name),
                ['category/info', 'id' => $category->id],
                ['class' => 'btn-link popupModal', 'data-pjax' => '0']
            );
        },
    ],

    [
        'label' => Yii::t('app', 'Serial No'),
        'headerOptions' => ['style' => 'width:20%'],
        'value' => function ($data) {
            return $data->category0?->serialNo;
        },
    ],

    [
        'label' => Yii::t('app', 'Company'),
        'headerOptions' => ['style' => 'width:10%'],
        'value' => function ($data) {
            return $data->category0?->company;
        },
    ],

    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'quantity',
        'headerOptions' => ['style' => 'width:10%'],
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

<div class="temp-transfer-items-form">

    <!-- شريط الإجراءات -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <?= Html::button(
            '<i class="bi bi-check2-circle"></i> ' . Yii::t('app', 'Save'),
            [
                'value' => Url::to(['transfer-items/create']),
                'class' => 'btn btn-danger popup',
            ]
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
        <div class="card-header fw-semibold"><i class="bi bi-plus-circle"></i> إضافة صنف للنقل</div>
        <div class="card-body">
            <?php $form = ActiveForm::begin([
                'id' => 'temp-transfer-items-form',
                'enableAjaxValidation' => false,
                'enableClientValidation' => false,
                'options' => ['class' => 'search-form'],
            ]) ?>

            <?= $form->errorSummary($model) ?>

            <div class="row g-3 align-items-end">
                <div class="col-lg-8">
                    <?= $form->field($model, 'category')->widget(Select2::class, [
                        'language' => 'ar',
                        'options' => [
                            'id' => 'kind',
                            'placeholder' => Yii::t('app', 'Search...'),
                            'dir' => 'rtl',
                            'multiple' => false,
                            'autofocus' => true,
                        ],
                        'pluginOptions' => [
                            'minimumInputLength' => 1,
                            'ajax' => [
                                'url' => Url::to(['/except/itemlist']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term, page: params.page}; }'),
                                'processResults' => new JsExpression($resultsJs),
                                'cache' => true,
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('formatProduct'),
                            'templateSelection' => new JsExpression('formatProductSelection'),
                        ],
                    ]) ?>
                </div>

                <div class="col-6 col-lg-2">
                    <?= $form->field($model, 'quantity')->textInput([
                        'maxlength' => true,
                        'placeholder' => Yii::t('app', 'Quantity'),
                        'inputmode' => 'decimal',
                    ]) ?>
                    <?= $form->field($model, 'branch')->hiddenInput(['placeholder' => 'branch', 'id' => 'branch'])->label(false) ?>
                </div>

                <div class="col-6 col-lg-2">
                    <div class="mb-3">
                        <?= Html::submitButton(
                            $model->isNewRecord
                                ? '<i class="bi bi-plus-lg"></i> ' . Yii::t('app', 'Add')
                                : '<i class="bi bi-check-lg"></i> ' . Yii::t('app', 'Update'),
                            ['class' => ($model->isNewRecord ? 'btn btn-success' : 'btn btn-primary') . ' w-100']
                        ) ?>
                    </div>
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
// فتح رابط اسم الصنف في النافذة. الربط على document لأن الجدول يُعاد رسمه بـ Pjax،
// ويتجاهل الحدث إن عالجه ajax-modal-popup.js أصلاً (لتفادي فتح النافذة مرتين).
$this->registerJs(<<<'JS'
$(document).off('click.ttiPopup').on('click.ttiPopup', '.popupModal', function (e) {
    if (e.isDefaultPrevented()) { return; }
    e.preventDefault();
    $('#modal').modal('show').find('.modal-content').load($(this).attr('href'));
});
JS, \yii\web\View::POS_READY, 'tti-popup');
?>