<?php

use app\models\Branches;
use kartik\date\DatePicker;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var app\models\TransferItems $model */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var yii\bootstrap5\ActiveForm $form */

$pjaxId = 'kv-pjax-container-temp-transfer-items';   // معرّف Pjax الفعلي للجدول في الأصل
$branches = ArrayHelper::map(Branches::find()->all(), 'id', 'name');

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
            chip('التسلسل', product.id) +
            chip('رقم القطعة 1', product.serialNo) +
            chip('رقم القطعة 2', product.commCode) +
            chip('الشركة', product.company) +
            chip('الكمية', product.quantity, 'text-bg-success') +
            chip('سعر البيع', product.maxPrice) +
            chip('السعر الأدنى', product.minPrice) +
            chip('المكان', product.place) +
            
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

// إضافة الصنف المختار إلى أمر النقل ثم إعادة رسم الجدول (كانت الإضافة تتم دون تحديث الجدول)
$cfg = Json::htmlEncode([
    'addUrl' => Url::to(['transfer-items/add']),
    'pjax' => '#' . $pjaxId,
]);
$this->registerJs(<<<JS
window.trCfg = $cfg;
window.trAddItem = function (id) {
    if (!id) { return; }
    $.get(window.trCfg.addUrl, {categoryid: id, transfer: $('#transferitems-id').val()})
        .done(function () {
            $('#kv-repo-template').val(null).trigger('change');
            $.pjax.reload({container: window.trCfg.pjax});
            $('#kv-repo-template').select2('open');
        })
        .fail(function () {
            if (typeof swal === 'function') {
                swal({title: 'منظومة المبيعات', text: 'حدث خطأ اثناء الإضافة', type: 'error', confirmButtonText: 'حسنا'});
            }
        });
};
JS, \yii\web\View::POS_HEAD, 'tr-add-helpers');

// تحرير داخل الخلية مع إعادة رسم الجدول بعد كل حفظ ناجح
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
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'category',
        'label' => Yii::t('app', 'ID'),
        'headerOptions' => ['style' => 'width:15%'],
        'editableOptions' => $editable,
    ],

    [
        'label' => Yii::t('app', 'رقم تسلسل الصنف'),
        'headerOptions' => ['style' => 'width:15%'],
        'value' => function ($data) {
            return $data->category0?->serialNo;
        },
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
            return Html::a(
                Html::encode($category->name),
                ['category/info', 'id' => $category->id],
                ['class' => 'btn-link popupModal', 'data-pjax' => '0']
            );
        },
    ],

    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'quantity',
        'label' => Yii::t('app', 'quantity'),
        'editableOptions' => $editable,
        'format' => ['decimal', 3],
        'pageSummary' => true,
        'footer' => true,
    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{remove}',
        'headerOptions' => ['style' => 'width:1%'],
        'contentOptions' => ['class' => 'text-nowrap'],
        'buttons' => [
            // كان رابط GET يحذف السجل. صار POST مع تأكيد
            'remove' => function ($url, $model, $key) {
                return Html::a('<i class="bi bi-trash"></i>', Url::to(['transfer-items/remove', 'id' => $model['id']]), [
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

<div class="transfer-items-form">

    <!-- بيانات أمر النقل -->
    <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-arrow-left-right"></i> بيانات النقل</div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(['id' => 'transfer-items-form']); ?>

            <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>

            <div class="row g-3">
                <div class="col-md-4">
                    <?= $form->field($model, 'fromBranch')->widget(Select2::class, [
                        'data' => $branches,
                        'language' => 'ar',
                        'options' => ['placeholder' => 'اختيار اسم الفرع المسحوب منه...'],
                        'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                    ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'toBranch')->widget(Select2::class, [
                        'data' => $branches,
                        'language' => 'ar',
                        'options' => ['placeholder' => 'اختيار اسم الفرع المودع له...'],
                        'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                    ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'at')->widget(DatePicker::class, [
                        'language' => 'ar',
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,
                            'todayBtn' => true,
                        ],
                    ]) ?>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <?= Html::submitButton(
                    $model->isNewRecord
                        ? '<i class="bi bi-plus-lg"></i> ' . Yii::t('app', 'Create')
                        : '<i class="bi bi-check-lg"></i> ' . Yii::t('app', 'Save'),
                    ['class' => ($model->isNewRecord ? 'btn btn-success' : 'btn btn-primary') . ' px-4']
                ) ?>
                <?= Html::a(
                    '<i class="bi bi-arrow-right-circle"></i> ' . Yii::t('app', 'Back'),
                    Yii::$app->request->referrer ?: ['index'],
                    ['class' => 'btn btn-outline-secondary']
                ) ?>
                <?= Html::a(
                    '<i class="bi bi-trash"></i> ' . Yii::t('app', 'Delete All'),
                    ['delete', 'id' => $model->id],
                    [
                        'class' => 'btn btn-outline-warning ms-auto',
                        'data' => [
                            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                    ]
                ) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <!-- إضافة صنف للنقل -->
    <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-plus-circle"></i> إضافة صنف للنقل</div>
        <div class="card-body">
            <?= Select2::widget([
                'name' => 'kv-repo-template',
                'id' => 'kv-repo-template',
                'language' => 'ar',
                'options' => [
                    'placeholder' => Yii::t('app', 'Search...'),
                    'dir' => 'rtl',
                    'onchange' => 'trAddItem($(this).val());',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 1,
                    'ajax' => [
                        'url' => Url::to(['/sales/itemlist']),
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
    </div>

    <!-- الأصناف المنقولة -->
    <div class="card">
        <div class="card-header fw-semibold"><i class="bi bi-list-check"></i> الأصناف المنقولة</div>
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
$(document).off('click.trPopup').on('click.trPopup', '.popupModal', function (e) {
    if (e.isDefaultPrevented()) { return; }
    e.preventDefault();
    $('#modal').modal('show').find('.modal-content').load($(this).attr('href'));
});
JS, \yii\web\View::POS_READY, 'tr-popup');
?>