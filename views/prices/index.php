<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\PricesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;

$this->title = Yii::t('app', 'Prices');
$this->params['breadcrumbs'][] = $this->title;
$search = "$('.search-button').click(function(){
	$('.search-form').toggle(1000);
	return false;
});";
$this->registerJs($search);
?>
<div class="prices-index">

    <h1><?= Html::encode($this->title) ?></h1><hr>
    
    <p>
        <?= Html::a(Yii::t('app', 'Advance Search'), '#', ['class' => 'btn btn-info search-button']) ?>
    </p>
    <br>
    <div class="search-form" style="display:none">
        <?= $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    <?php
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        [
            'attribute' => 'category',
            'label' => Yii::t('app', 'Category'),
            'value' => function ($model) {
                return $model->category0->name;
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => \yii\helpers\ArrayHelper::map(\app\models\Category::find()->asArray()->all(), 'id', 'name'),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'Category', 'id' => 'grid-prices-search-category']
        ],

        'category0.company',

        'category0.serialNo',
        
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => Yii::t('app', 'costPrice'),
            'editableOptions' => [
                'asPopover' => true,
            ],

            'options' => ['class' => 'form-control', 'placeholder' => '...'],
        ],

        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => Yii::t('app', 'maxPrice'),
            'editableOptions' => [
                'asPopover' => true,
            ],

            'options' => ['class' => 'form-control', 'placeholder' => '...'],
        ],

        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => Yii::t('app', 'minPrice'),
            'editableOptions' => [
                'asPopover' => true,
            ],

            'options' => ['class' => 'form-control', 'placeholder' => '...'],
        ],

        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => Yii::t('app', 'minPrice2'),
            'editableOptions' => [
                'asPopover' => true,
            ],

            'options' => ['class' => 'form-control', 'placeholder' => '...'],
        ],

        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => Yii::t('app', 'minPrice3'),
            'editableOptions' => [
                'asPopover' => true,
            ],

            'options' => ['class' => 'form-control', 'placeholder' => '...'],
        ],

    ];
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-prices']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title),
        ],

    ]); ?>

</div>