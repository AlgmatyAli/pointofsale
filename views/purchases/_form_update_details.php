<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var app\models\Purchases $model */
/** @var yii\data\ActiveDataProvider $dataProvider */

$pjaxId = 'purchases-details-pjax';

// تحرير داخل الخلية (بدل النافذة المنبثقة) مع إعادة رسم الجدول بعد كل حفظ ناجح
$editable = [
    'asPopover' => false,
    'bsVersion' => '5.x',
    'submitButton' => ['icon' => '<i class="bi bi-check-lg"></i>'],
    'resetButton' => ['icon' => '<i class="bi bi-arrow-counterclockwise"></i>'],
    'pluginEvents' => [
        'editableSuccess' => new JsExpression("function () { $.pjax.reload({container: '#{$pjaxId}'}); }"),
    ],
];

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
            return Html::a(
                Html::encode($category->name),
                ['category/info', 'id' => $category->id],
                ['class' => 'btn-link popupModal', 'data-pjax' => '0']
            );
        },
    ],

    'category0.company',                                         // 4

    ['attribute' => 'costPrice'],                                // 5

    [                                                            // 6
        'attribute' => 'totalCost',
        'label' => Yii::t('app', 'Cost Total'),
    ],

    [                                                            // 7
        'attribute' => 'quantity',
        'label' => Yii::t('app', 'quantity'),
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
];
?>

<div class="purchases-form">

    <div class="mb-3">
        <?= Html::a(
            '<i class="bi bi-arrow-right-circle"></i> ' . Yii::t('app', 'Back'),
            Yii::$app->request->referrer ?: ['index'],
            ['class' => 'btn btn-danger']
        ) ?>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => '{items}{pager}',
                'summary' => true,
                'columns' => $gridColumns,
                'bordered' => true,
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
$(document).off('click.purPopup').on('click.purPopup', '.popupModal', function (e) {
    if (e.isDefaultPrevented()) { return; }
    e.preventDefault();
    $('#modal').modal('show').find('.modal-content').load($(this).attr('href'));
});
JS, \yii\web\View::POS_READY, 'pur-details-popup');
?>
