<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Purchases */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="purchases-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="form-group">
        <div class="btn-group">
            <?= Html::a(
                '<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Back'),
                Yii::$app->request->referrer,
                ['class' => 'btn btn-danger']
            ) ?>
        </div>
    </div>
    
<br>
<?php
$gridColumn = [
    ['class' => 'yii\grid\SerialColumn'],
    'category0.id',
    ['attribute' => 'id', 'visible' => false],
    [
        'attribute' => 'category',
        'headerOptions' => ['style' => 'width:20%'],
        'value' => function ($model) {
            return Html::a(Yii::t('app', ' {modelClass}', [
                'modelClass' => $model->category0->name,
            ]), ['category/info', 'id' => $model->category0->id], ['class' => 'btn-link popupModal']);
        },
        'format' => 'html',
    ],
    'category0.company',

    [
        'attribute' => Yii::t('app', 'costPrice'),
    ],

    [
        'attribute' => Yii::t('app', 'totalCost'),
        'label' => Yii::t('app', 'Cost Total'),
    ],

    [
        'attribute' => 'quantity',
        'label' => Yii::t('app', 'quantity'),
    ],

    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => Yii::t('app', 'salePrice'),
        'editableOptions' => [
            'asPopover' => true,
        ],

    ],

    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => Yii::t('app', 'salePrice_'),
        'editableOptions' => [
            'asPopover' => true,
        ],

    ],

    [
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
        'footer' => true

    ],
];
?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => '{items}{pager}',
    'summary' => true,
    'columns' => $gridColumn,
    'pjax' => true,
    'pjaxSettings' => [
        'neverTimeout' => true,
        'options' => [
            'id' => 'w0',
        ]
    ],
    'showPageSummary' => true,
]); ?>

<?php
$this->registerJs("$(function() {
     $('.popupModal').click(function(e) {
     e.preventDefault();
     $('#modal').modal('show').find('.modal-content')
     .load($(this).attr('href'));
     });
});");

?>