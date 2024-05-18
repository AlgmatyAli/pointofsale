<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\InventorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;

$this->title = Yii::t('app', 'Inventory With Price');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="inventory-index">

    
<br><br>
    <center><h1><?= Html::encode($this->title) ?></h1><hr></center>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'showPageSummary' => true,
        'summary'=>'',
        'columns' => [
       
               [
                   'label' => Yii::t('app', 'ID'),
                   'headerOptions' => ['style' => 'width:10%'],
                   'value' => function ($data)
                   {
                     return $data->id;
                   }
                   
               ],
       
               [
                   'label' => Yii::t('app', 'Name'),
                   'headerOptions' => ['style' => 'width:30%'],
                   'value' => function ($data)
                   {
                     return $data->name;
                   }
                   
               ],

               [
                   'label' => Yii::t('app', 'Price'),
                   'headerOptions' => ['style' => 'width:15%'],
                   'value' => function ($data)
                   {
                     return $data->maxPrice;
                   }
                   
               ],
        ],
    ]);


   ?>

</div>
