<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\Widget;
use kartik\mpdf\Pdf;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CompanyInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Company Infos');
// $this->params['breadcrumbs'][] = $this->title;
?>
<html><div id="showBarcode"></div></html> <!--This element id should  be passed on to options-->

<div class="company-info-index">

<?php 

if (Yii::$app->session->hasFlash('error')): ?>
   
<div class="box-body">
 <div class="alert alert-info alert-dismissible">
 <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <h4><i class="icon fa fa-info"></i>Info!</h4>
   Data is Saved
  </div>
<?php endif; ?>

<br><h1 class=""><?= Html::encode($this->title) ?></h1><br><hr>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
      <?= Html::a('<i class="fa fa-fw fa-file-text-o"></i>'.' '.Yii::t('app', 'Create Company Infos'), ['create'], ['class' => 'btn btn-success', 'id'=>'comp-info']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
       // 'filterModel' => $searchModel,
       'summary' =>'',
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'name',
            'work',
            'address:ntext',
            'phone1',
            
          // ['class' => 'yii\grid\ActionColumn'],
          [
            'class' => 'yii\grid\ActionColumn',
            'options'=>['style'=>'width:120px;'],
            'template'=>'<div class="btn-group btn-group-sm" role="group" aria-label="...">{view}{update}{delete}</div>',
            'buttons'=>[
                'view'=>function($url,$searchModel,$key){
                    return Html::a('<i class="fa fa-eye"></i>',$url,['class'=>'btn btn-default']);
                },
                'update'=>function($url,$searchModel,$key){
                    return Html::a('<i class="fa fa-pencil"></i>',$url,['class'=>'btn btn-default']);
                },
                'delete'=>function($url,$searchModel,$key){
                     return Html::a('<i class="fa fa-trash"></i>', $url,[
                            'title' => Yii::t('yii', 'Delete'),
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'class'=>'btn btn-default'
                            ]);
                }
            ]
        ],
        
        ] 
]); ?>
<?php Pjax::end(); ?>
</div>
