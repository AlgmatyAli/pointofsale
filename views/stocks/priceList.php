<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\InventorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
//use kartik\export\ExportMenu;
use kartik\grid\GridView;

$this->title = Yii::t('app', 'Stocks');
$this->params['breadcrumbs'][] = $this->title;
$search = "$('.search-button').click(function(){
	$('.search-form').toggle(1000);
	return false;
});";
$this->registerJs($search);
?>
<div class="inventory-index">
  <p>
    <?= Html::a(Yii::t('app', 'Advance Search'), '#', ['class' => 'btn btn-info search-button']) ?>
  </p>
  <div class="search-form" style="display:none">
    <?= $this->render('_searchP', ['model' => $searchModel]); ?>
  </div>
  <?php
  $gridColumn = [
    [
      'label' => Yii::t('app', 'ID'),
      'headerOptions' => ['style' => 'width:15%'],
      'value' => function ($data) {
        return $data->category;
      }

    ],

    [
      'label' => Yii::t('app', 'Serial No'),
      'headerOptions' => ['style' => 'width:15%'],
      'value' => function ($data) {
        return $data->serialNo;
      }

    ],

    [
      'label' => Yii::t('app', 'Name'),
      'headerOptions' => ['style' => 'width:30%'],
      'value' => function ($data) {
        return $data->name;
      },
    ],

    [
      'label' => Yii::t('app', 'Company'),
      'headerOptions' => ['style' => 'width:15%'],
      'value' => function ($data) {
        return $data->company;
      },
    ],

    [
      'label' => Yii::t('app', 'quantity'),
      'headerOptions' => ['style' => 'width:15%'],
      'value' => function ($data) {
        return $data->quantity;
      }
    ],

    [
      'label' => Yii::t('app', 'Cost Price'),
      'headerOptions' => ['style' => 'width:15%'],
      'value' => function ($data) {
        return $data->costPrice;
      }
    ],

    [
      'label' => Yii::t('app', 'Max Price'),
      'headerOptions' => ['style' => 'width:15%'],
      'value' => function ($data) {
        return $data->maxPrice;
      }
    ],

    [
      'label' => Yii::t('app', 'Branch'),
      'headerOptions' => ['style' => 'width:15%'],
      'value' => function ($data) {
        return $data->branches0->name;
      }
    ],

  ];
  ?>
  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumn,
    'pjax' => true,
    'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-inventory']],
    'panel' => [
      'type' => GridView::TYPE_PRIMARY,
    ],
  ]); ?>

</div>