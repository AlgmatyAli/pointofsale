<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\LoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'تقرير تفصيلي عن السلف الممنوحة');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loans-index">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?php Pjax::begin(); ?>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'summary' => '',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'employee0.name',
            'loanValue',
            'kestValue',
            [ 
                'label' => Yii::t('app', 'تاريخ المنح'),
                'format' => 'raw',
                   'value'=>function($searchModel) { 
                        return $searchModel->at;
                }
            ],
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
