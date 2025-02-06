<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\BalanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;

$this->title = Yii::t('app', 'Balances');
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
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <p>
        <?php //Html::a(Yii::t('app', 'Advance Search'), '#', ['class' => 'btn btn-info search-button']) ?>
    </p>
    <br>
    <div class="search-form" style="display:none">
        <?php // $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    <?php
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        [
            'label' => Yii::t('app', 'نوع الحركة'),
            'format' => 'raw',
            'attribute' => 'type',
           'filter' => Html::activeDropDownList(
                $searchModel,
                'type',
                ['0' => 'زبون', '1' => 'مورد', '2' => 'عميل'],
                ['class' => 'form-control', 'prompt' => 'اختيار نوع الحركة ...']),
               
            'value' => function ($data) {
            if ($data->type == 0) {
                return 'زبون';
            }
            if ($data->type == 1) {
                return 'مورد';
            }
            if ($data->type == 2) {
                return 'عميل';
            }
            }
        ],
        'name',
        [
            'label' => Yii::t('app', 'الرصيد'),
            'format' => 'raw',
            'value' => function ($data) {
                return $data->credt;
            }
        ],
        [
            'label' => Yii::t('app', 'Phone'),
            'format' => 'raw',
            'value' => function ($data) {
                return $data->phone;
            }
        ],
        [
            'label' => Yii::t('app', 'Currency'),
            'format' => 'raw',
            'value' => function ($data) {
                return $data->currancy0->name;
            }
        ],
    ];
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => '',
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-balance']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title),
        ],
        'export' => false,
        // your toolbar can include the additional full export menu
        'toolbar' => [
            '{export}',
            ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $gridColumn,
                'target' => ExportMenu::TARGET_BLANK,
                'fontAwesome' => true,
                'dropdownOptions' => [
                    'label' => 'Full',
                    'class' => 'btn btn-default',
                    'itemsBefore' => [
                        '<li class="dropdown-header">Export All Data</li>',
                    ],
                ],
                'exportConfig' => [
                    ExportMenu::FORMAT_PDF => false
                ]
            ]),
        ],
    ]); ?>

</div>