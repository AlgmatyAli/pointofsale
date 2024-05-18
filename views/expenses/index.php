<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ExpensesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Expenses');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expenses-index">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->
    <hr>
    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'showPageSummary' => true,
        'summary' => '',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'expenseTo',
            'at',
            [
                'label' => Yii::t('app', 'Name'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->item->name;
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

            'outBox',
            
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
