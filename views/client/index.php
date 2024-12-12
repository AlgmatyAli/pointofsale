<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Clients');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>


    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary'=> '',
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'phone',
            'mobile',
            'address',
            'debt',

            [
                'class' => 'yii\grid\ActionColumn',
                'options' => ['style' => 'width:120px;'],
                'template' => '<div class="btn-group btn-group-sm" role="group" aria-label="...">{view}{update}</div>',
                'buttons' => [
                    'view' => function ($url, $searchModel, $key) {
                        return Html::a('<i class="fa fa-eye"></i>', $url, ['class' => 'btn btn-default']);
                    },
                    'update' => function ($url, $searchModel, $key) {
                        return Html::a('<i class="fa fa-edit"></i>', $url, ['class' => 'btn btn-default']);
                    },


                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>