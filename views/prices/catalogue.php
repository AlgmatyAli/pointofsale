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
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Advance Search'), '#', ['class' => 'btn btn-info search-button']) ?>
    </p>
    <br>
    <div class="search-form" style="display:none">
        <?=  $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    
    <?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        [
            'attribute' => 'logo',
            'format' => 'html',
            'label' => ' ',
            'value' => function ($data) {
                       return Html::img( $data->category0['path'],
                       ['width' => '100px',
                        'height' => '100px']);
                      },
        ],
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
                'filterInputOptions' => ['placeholder' => 'Category', 'id' => 'grid-prices-search-category']
            ],
            [
                'attribute' => 'category0.commCode',
                'value' => function($model){                   
                    return $model->category0->commCode;                   
                },
                'format' => 'raw',
            ],
            
            [
           
                  'attribute' => Yii::t('app', 'category0.serialNo'),
             
                  
                  'options' => ['class'=>'form-control', 'placeholder'=>'...'],
                  // 'group' => true,
              ],
           
            [
           
                'attribute' => Yii::t('app', 'maxPrice'),
           
                
                'options' => ['class'=>'form-control', 'placeholder'=>'...'],
                // 'group' => true,
            ],

            [
           
                'attribute' => Yii::t('app', 'minPrice'),
           
                
                'options' => ['class'=>'form-control', 'placeholder'=>'...'],
                // 'group' => true,
            ],

            
        
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-prices']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title),
        ],
        
    ]); ?>
    <div class="col-xl-3 col-md-4 col-12">

        <div class="box box-widget widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-black center center;">
              <h3 class="widget-user-username">Elizabeth Pierce</h3>
              <h5 class="widget-user-desc">Web Designer</h5>
            </div>
            <div class="widget-user-image">
              <img class="img-circle" src="img/category/61d7631de0cdf.jpeg" alt="User Avatar">
            </div>
            <div class="box-footer">
              <div class="row">
                <div class="col-sm-4 border-right">
                  <div class="description-block">
                    <h5 class="description-header">3,200</h5>
                    <span class="description-text">SALES</span>
                  </div>
                  <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-4 border-right">
                  <div class="description-block">
                    <h5 class="description-header">13,000</h5>
                    <span class="description-text">FOLLOWERS</span>
                  </div>
                  <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-4">
                  <div class="description-block">
                    <h5 class="description-header">35</h5>
                    <span class="description-text">PRODUCTS</span>
                  </div>
                  <!-- /.description-block -->
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
          </div>
          </div>
          <div class="col-xl-3 col-md-4 col-12">

<div class="box box-widget widget-user">
    <!-- Add the bg color to the header using any of the bg-* classes -->
    <div class="widget-user-header bg-black center center;">
      <h3 class="widget-user-username">Elizabeth Pierce</h3>
      <h5 class="widget-user-desc">Web Designer</h5>
    </div>
    <div class="widget-user-image">
      <img class="img-circle" src="img/category/61d7631de0cdf.jpeg" alt="User Avatar">
    </div>
    <div class="box-footer">
      <div class="row">
        <div class="col-sm-4 border-right">
          <div class="description-block">
            <h5 class="description-header">3,200</h5>
            <span class="description-text">SALES</span>
          </div>
          <!-- /.description-block -->
        </div>
        <!-- /.col -->
        <div class="col-sm-4 border-right">
          <div class="description-block">
            <h5 class="description-header">13,000</h5>
            <span class="description-text">FOLLOWERS</span>
          </div>
          <!-- /.description-block -->
        </div>
        <!-- /.col -->
        <div class="col-sm-4">
          <div class="description-block">
            <h5 class="description-header">35</h5>
            <span class="description-text">PRODUCTS</span>
          </div>
          <!-- /.description-block -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
  </div>
  </div>
  <div class="col-xl-3 col-md-4 col-12">

<div class="box box-widget widget-user">
    <!-- Add the bg color to the header using any of the bg-* classes -->
    <div class="widget-user-header bg-black center center;">
      <h3 class="widget-user-username">Elizabeth Pierce</h3>
      <h5 class="widget-user-desc">Web Designer</h5>
    </div>
    <div class="widget-user-image">
      <img class="img-circle" src="img/category/61d7631de0cdf.jpeg" alt="User Avatar">
    </div>
    <div class="box-footer">
      <div class="row">
        <div class="col-sm-4 border-right">
          <div class="description-block">
            <h5 class="description-header">3,200</h5>
            <span class="description-text">SALES</span>
          </div>
          <!-- /.description-block -->
        </div>
        <!-- /.col -->
        <div class="col-sm-4 border-right">
          <div class="description-block">
            <h5 class="description-header">13,000</h5>
            <span class="description-text">FOLLOWERS</span>
          </div>
          <!-- /.description-block -->
        </div>
        <!-- /.col -->
        <div class="col-sm-4">
          <div class="description-block">
            <h5 class="description-header">35</h5>
            <span class="description-text">PRODUCTS</span>
          </div>
          <!-- /.description-block -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
  </div>
  </div>
  <div class="col-xl-3 col-md-4 col-12">

<div class="box box-widget widget-user">
    <!-- Add the bg color to the header using any of the bg-* classes -->
    <div class="widget-user-header bg-black center center;">
      <h3 class="widget-user-username">Elizabeth Pierce</h3>
      <h5 class="widget-user-desc">Web Designer</h5>
    </div>
    <div class="widget-user-image">
      <img class="img-circle" src="img/category/61d7631de0cdf.jpeg" alt="User Avatar">
    </div>
    <div class="box-footer">
      <div class="row">
        <div class="col-sm-4 border-right">
          <div class="description-block">
            <h5 class="description-header">3,200</h5>
            <span class="description-text">SALES</span>
          </div>
          <!-- /.description-block -->
        </div>
        <!-- /.col -->
        <div class="col-sm-4 border-right">
          <div class="description-block">
            <h5 class="description-header">13,000</h5>
            <span class="description-text">FOLLOWERS</span>
          </div>
          <!-- /.description-block -->
        </div>
        <!-- /.col -->
        <div class="col-sm-4">
          <div class="description-block">
            <h5 class="description-header">35</h5>
            <span class="description-text">PRODUCTS</span>
          </div>
          <!-- /.description-block -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
  </div>
  </div>


        </div>
