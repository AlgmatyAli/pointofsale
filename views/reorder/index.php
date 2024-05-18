<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ReorderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Reorders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reorder-index">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <p>
        <?php // Html::a(Yii::t('app', 'Create Reorder'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary'=>'',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'at',
            // 'branch',
            // 'created_by',
            // 'created_at',
            //'updated_by',
            //'updated_at',

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
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
