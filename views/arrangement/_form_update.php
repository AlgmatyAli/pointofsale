<?php

use kartik\date\DatePicker;
use kartik\grid\GridView;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var app\models\Arrangement $model */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var yii\bootstrap5\ActiveForm $form */

$pjaxId = 'kv-pjax-container-temp-transfer-items';   // معرّف Pjax الفعلي للجدول في الأصل

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
        'width' => '10%',
        'value' => function ($model) {
            return $model->category0?->serialNo;
        },
    ],

    [
        'attribute' => 'category',
        'headerOptions' => ['style' => 'width:28%'],
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
        'width' => '12%',
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
        'width' => '15%',
        'attribute' => 'quantity',
        'label' => Yii::t('app', 'quantity'),
        'editableOptions' => $editable,
        'format' => ['decimal', 3],
        'pageSummary' => true,
        'footer' => true,
    ],

    [
        'attribute' => 'type',
        'width' => '15%',
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
?>

<div class="arrangement-form">

    <!-- بيانات التسوية -->
    <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-clipboard-check"></i> بيانات تسوية الجرد</div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(['id' => 'arrangement-form']); ?>

            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <?= $form->field($model, 'at')->widget(DatePicker::class, [
                        'language' => 'ar',
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,
                        ],
                    ]) ?>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <?= Html::submitButton(
                            '<i class="bi bi-check-lg"></i> ' . Yii::t('app', 'Save'),
                            ['class' => 'btn btn-success w-100']
                        ) ?>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <!-- أصناف التسوية -->
    <div class="card">
        <div class="card-header fw-semibold"><i class="bi bi-list-check"></i> أصناف التسوية</div>
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
$(document).off('click.arPopup').on('click.arPopup', '.popupModal', function (e) {
    if (e.isDefaultPrevented()) { return; }
    e.preventDefault();
    $('#modal').modal('show').find('.modal-content').load($(this).attr('href'));
});
JS, \yii\web\View::POS_READY, 'ar-popup');
?>