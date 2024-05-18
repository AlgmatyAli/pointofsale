<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\BalanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;

$this->title = Yii::t('app', 'Balance');
$this->params['breadcrumbs'][] = $this->title;
$search = "$('.search-button').click(function(){
	$('.search-form').toggle(1000);
	return false;
});";
$this->registerJs($search);
?>
<div class="balance-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>


    <div class="search-form" style="display:none">
        <?= $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    <?php
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        'value',
        'clinet',
        'currancy',
        [
            'class' => 'yii\grid\ActionColumn',
        ],
    ];
    ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'summary' => '',
        'showPageSummary' => true,
        'pjax' => true,
        'striped' => false,
        'hover' => true,
        'toggleDataContainer' => ['class' => 'btn-group mr-2'],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            [
                'attribute' => 'client',
                'label' => 'العمـيل',
                'width' => '310px',
                'value' => function ($model, $key, $index, $widget) {
                    return $model->client0->name;
                },

                'group' => true,  // enable grouping

            ],
            [
                'attribute' => 'value',
                'format' => ['decimal',3],
                'width' => '250px',
                'value' => function ($model) {
                    return $model->value;
                }
            ],
            

            [
                'attribute' => 'currancy',
                'width' => '250px',
                'value' => function ($model, $key, $index, $widget) {
                    return $model->currancy0->name;
                },
            ],
        ],
    ]);

    ?>
</div>