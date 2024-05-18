<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\TransferSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Transfers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transfer-index">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->
    <hr>
    <?php Pjax::begin(); ?>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'showPageSummary' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
 
            [
                'label' => Yii::t('app', 'From Br'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->fromBr0->name;
                }
            ],            
            [
                'label' => Yii::t('app', 'To Br'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->toBr0->name;
                }
            ], 
            [
                'label' => Yii::t('app', 'Value'),
                'attribute' => 'value',
                // 'format' => 'currency',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary' => true,
            ],           
             'at',
            [ 
                'label' => Yii::t('app', 'Type'),
                'format' => 'raw',
                   'value'=>function($searchModel) { 
    
                    if($searchModel->type ==1){
                        return 'صادر';
                    }elseif($searchModel->type ==2){
                        return 'وارد';
                    }
                }
            ],
            'why',
            //'user_insert',
            //'created_at',
            //'user_update',
            //'update_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'options'=>['style'=>'width:120px;'],
                'template'=>'<div class="btn-group btn-group-sm" role="group" aria-label="...">{view}</div>',
                'buttons'=>[
                    'view'=>function($url,$searchModel,$key){
                        return Html::a('<i class="fa fa-eye"></i>',$url,['class'=>'btn btn-default']);
                    },
                   
                    
                ]
            ],  
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
