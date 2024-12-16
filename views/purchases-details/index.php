<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\PurchasesDetailsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;

$this->title = Yii::t('app', 'Purchases Details By Dates');
$this->params['breadcrumbs'][] = $this->title;
$search = "$('.search-button').click(function(){
	$('.search-form').toggle(1000);
	return false;
});";
$this->registerJs($search);
?>
<div class="purchases-details-index">

    <h1><?= Html::encode($this->title) ?></h1><hr>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <div class="search-form" style="">
        <?= $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    <?php
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        'PurchasesId',
        'purchases.at',
        [
            'attribute' => 'category',
            'headerOptions' => ['style' => 'width:40%'],
            'value' => function ($model) {
                return Html::a(Yii::t('app', ' {modelClass}', [
                    'modelClass' => $model->category0->name,
                ]), ['prices/index_', 'category' => $model->category0->id], ['class' => 'btn-link popupModal']);
            },
            'format' => 'raw',
        ],
        [
            'label' => Yii::t('app', 'quantity'),
            'attribute' => 'quantity',
            'hAlign' => 'right',
            'pageSummary' => true,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => Yii::t('app', 'totalCost'),
            'contentOptions' => ['style' => 'font-size:14px;'],

            'editableOptions' => [
                'asPopover' => true,
            ],
            'format' => ['decimal', 3],
            'pageSummary' => true,
            'footer' => true
        ],

        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => Yii::t('app', 'salePrice'),
            'contentOptions' => ['style' => 'font-size:14px;'],

            'editableOptions' => [
                'asPopover' => true,
            ],
            'format' => ['decimal', 3],
            'pageSummary' => true,
            'footer' => true
        ],

        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => Yii::t('app', 'salePrice_'),
            'contentOptions' => ['style' => 'font-size:14px;'],

            'editableOptions' => [
                'asPopover' => true,

            ],
            'format' => ['decimal', 3],
            'pageSummary' => true,
            'footer' => true
        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'options'=>['style'=>'width:120px;'],
            'template'=>'<div class="btn-group btn-group" role="group" aria-label="...">{view}</div>',
            'buttons'=>[
                'view' => function ($url, $model, $key) {
                    return Html::a('<i class="fa fa-eye"></i>', ['purchases/view', 'id' => $model->PurchasesId], [
                        'class' => 'btn btn-default'
                    ]);
                },
            ]
        ],
    ];
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumn,
        'showPageSummary' => true,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-purchases-details']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title),
        ],

    ]); ?>

</div>