<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\LoanPaidSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'الإستفسار عن دفعات السلف');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-paid-index">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?php Pjax::begin(); ?>
    <?php 
    $sumLoanValue=0; $sumAksat=0;
    $data = $dataProvider->getModels();
   
    foreach ($data as $value) {
        $sumAksat = $sumAksat + $value['kestValue'];
    }
    ?>
    <div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-fw fa-info"></i></span>

                    <div class="info-box-content">
                    <span class="info-box-text"><?=Yii::t('app', 'اجمالي الأقساط المسددة')?></span>
                    <span class="info-box-number"><?= $sumAksat ?><small></small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
    </div>
    </div>
    <br><br>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'employee0.name',
            'loanId',
            'kestValue',
            'at',
            'month',
            'year',
            'notes',
            [
                'class' => 'yii\grid\ActionColumn',
                'options'=>['style'=>'width:120px;'],
                'template'=>'<div class="btn-group btn-group-sm" role="group" aria-label="...">{view}{update}{delete}</div>',
                'buttons'=>[
                    'view'=>function($url,$searchModel,$key){
                        return Html::a('<i class="fa fa-eye"></i>',$url,['class'=>'btn btn-default']);
                    },
                    'update'=>function($url,$searchModel,$key){
                        return Html::a('<i class="fa fa-edit"></i>',$url,['class'=>'btn btn-default']);
                    },
                    'delete'=>function($url,$searchModel,$key){
                        return Html::a('<i class="fa fa-trash"></i>',$url,['class'=>'btn btn-default']);
                    },
                    
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
