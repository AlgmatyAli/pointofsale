<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\models\Dept;
use app\models\CompanyInfo;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;
use app\models\HistransClient;
use app\models\Inventory;

/* @var $this yii\web\View */
/* @var $model app\models\Salaryroll */
?>
       <hr>
<div class="row">

<div class="col-md-12">

   <p>
     <button class='btn btn-info' onClick="window.print()"><?= Yii::t('app', 'Print')?></button>`
     <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer , ['class'=> 'btn btn-danger btnx']) ?>
   </p>
   <br><br>
   
   <?php $title = CompanyInfo::find()->select(['*'])->asArray()->one();?>

    <img src=<?php echo $title["path"]?> class="img-circle logo" alt="Cinque Terre">
    <h4>
    <br>
    <?php
     echo  $title['name'];
     echo '<br><br><br>';
    ?> </h4>
   <center>
    <div>
      <h3>قائمة بالأصناف المعلقة بفواتير المشتريات : <b><?= date('Y-m-d') ?></b> </h3>
    </div>
    </center>
    <br><br><br>
    
    <?php 

      echo GridView::widget([
        'summary'=>'',
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => Yii::t('app', 'Name'),
                'headerOptions' => ['style' => 'width:35%'],
                'attribute' => 'name',
                'format' => 'raw'
            ],
            [
                'label' => Yii::t('app', 'Serial No'),
                'headerOptions' => ['style' => 'width:20%'],
                'attribute' => 'serialNo',
                'format' => 'html'
            ],
            
            [
                'label' => Yii::t('app', 'quantity'),
              //  'headerOptions' => ['style' => 'width:5%'],
                'attribute' => 'quantity',
                'format' => 'raw'
            ],

            [
                'label' => Yii::t('app', 'Total Cost'),
                'headerOptions' => ['style' => 'width:10%'],
                'attribute' => 'totalCost',
                'format' => 'raw'
            ],

            [
                'label' => Yii::t('app', 'Sale Price'),
                'headerOptions' => ['style' => 'width:10%'],
                'attribute' => 'salePrice',
                'format' => 'raw'
            ],
            
            [
                'label' => Yii::t('app', 'Sale Price_'),
                'headerOptions' => ['style' => 'width:10%'],
                'attribute' => 'salePrice_',
                'format' => 'raw'
            ],
            
        ],
        ]);
    ?>
   
</div>

</div>