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

   <div id='div1' class="site-about">
   
   <?php $title = CompanyInfo::find()->select(['*'])->asArray()->one();?>

    <img src=<?php echo $title["path"]?> class="img-circle logo" alt="Cinque Terre">
    <h4><?php
    echo '<br><br>';
     echo  $title['name'];
     echo '<br><br>';
    ?> </h4>
   <br><br>
   <center>
    <div>
      <h3>قائمة بالفواتير التي قارب موعد استحقاقها<b><?php // date('Y-m-d') ?></b> </h3>
    </div>
    </center>
    <br><br>
    
    <?php 
      echo GridView::widget([
        'summary'=>'',
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => Yii::t('app', 'Invoice ID'),
              //  'headerOptions' => ['style' => 'width:5%'],
                'attribute' => 'billId',
                'format' => 'raw'
            ],

            [
                'label' => Yii::t('app', 'At'),
              //  'headerOptions' => ['style' => 'width:5%'],
                'attribute' => 'at',
                'format' => 'raw'
            ],

           'c.name',

           [
            'label' => Yii::t('app', 'Total'),
            'format' => 'raw',
            'value' => function ($data) {
                return $data->total - ($data->disscount+$data->paid);
            }
        ],
 
            [
                'label' => Yii::t('app', 'Deserving'),
                'headerOptions' => ['style' => 'width:25%'],
                'attribute' => 'deserving',
                'format' => 'html'
            ],

            [
              'label' => Yii::t('app', ' اجمالي الدين'),
            //  'headerOptions' => ['style' => 'width:5%'],
              'attribute' => 'credt',
              'format' => 'raw'
          ],

            [
              'class' => 'yii\grid\ActionColumn',
              'options'=>['style'=>'width:120px;'],
              'template'=>'<div class="btn-group btn-group-sm" role="group" aria-label="...">{print}{done}</div>',
              'buttons'=>[
                  'print'=>function($url,$searchModel,$key){
                      return Html::a('<i class="fa fa-folder-open"></i>',$url,['class'=>'btn btn-danger']);
                  },
                  'done'=>function($url,$searchModel,$key){
                    return Html::a('<i class="fa fa-paper-plane"></i>',$url,['class'=>'btn btn-default']);
                },
                  
                  ]
            ],
        ],
        ]);
    ?>
   
</div>

</div>