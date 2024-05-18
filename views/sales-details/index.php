<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesDetailsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Sales Details');
$this->params['breadcrumbs'][] = $this->title;
$search = "$('.search-button').click(function(){
	$('.search-form').toggle(1000);
	return false;
});";
$this->registerJs($search);
?>
<div class="sales-details-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
       
           </p>
   
    </div>
    <?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
       
        [
                'attribute' => 'category',
                'label' => Yii::t('app', 'Category'),
                'value' => function($model){                   
                    return $model->category0->name;                   
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\app\models\Category::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Category', 'id' => 'grid-sales-details-search-category']
            ],
        
        'serial_number:ntext',
        'quantity',
        
        'salePrice',
        'original_price',
        
        'mac_address:ntext',
        'expire',
        ['class' => 'yii\grid\ActionColumn', 
        'template' => '{view}',
        'buttons' => [
            'view' => function($url, $model, $key) {

                $url = Url::to(['sales/print', 'id' => $model['salesId'] ]);

                return Html::a('<span class="glyphicon glyphicon-flag"></span>', $url, [
                    'title' => Yii::t('app', 'View Invoice'),
                ]);

            }
        ],

], 
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-sales-details']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title),
        ],
        
    ]); ?>

</div>
