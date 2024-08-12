<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
// $this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <br><h1><?= $this->title = Yii::t('app', 'Users'); ?></h1>
    <hr>
    <?php Pjax::begin(); ?>  
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <!-- <?= Html::a(Yii::t('app', 'Creating New User'), ['create'], ['class' => 'btn btn-success']) ?>
        <?php //echo Html::a(Yii::t('app', 'Print User'), ['print'], ['class' => 'btn btn-danger']) ?> -->
    </p>

    <?php
      yii\bootstrap\Modal::begin([
      'header'=> $this->title = Yii::t('app', 'Users'),
      'headerOptions' => ['id' => 'modalHeader'],
      'id' => 'modal',
      'size' => 'modal-lg',
      
           //keeps from closing modal with esc key or by clicking out of the modal.
           // user must click cancel or X to close
      'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE]
      ]);
          echo "<div id='modalContent'></div>";
          yii\bootstrap\Modal::end();
   ?>
     <div id='div1' class="site-about">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary'=>'',
        'rowOptions' => function ($searchModel) {
            if ($searchModel->isActive == 'deActive') {
                return ['class' => 'danger'];
            }
        },

        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
           // 'id',
           'username',
           'phone',
           'email',
           'isActive',
           [
            'label' => Yii::t('app', 'Permission'),
            'format' => 'raw',
            'value' => function ($data) {
                return $data->auth->name;
            }
           ],
           
           [
            'label' => Yii::t('app', 'Br ID'),
            'format' => 'raw',
            'value' => function ($data) {
                return $data->br->name;
            }
        ],
        
           [
            'class' => 'yii\grid\ActionColumn',
            'options'=>['style'=>'width:120px;'],
            'template'=>'<div class="btn-group btn-group-sm" role="group" aria-label="...">{view}{update}</div>',
            'buttons'=>[
                'view'=>function($url,$searchModel,$key){
                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>',$url,['class'=>'btn btn-default']);
                },
                'update'=>function($url,$searchModel,$key){
                    return Html::a('<i class="glyphicon glyphicon-pencil"></i>',$url,['class'=>'btn btn-default']);
                },
            ]
        ],
            
        ],     
    ]); 
    ?>
    </div>
   <?php Pjax::end(); ?>
</div>