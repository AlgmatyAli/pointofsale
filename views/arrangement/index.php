<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ArrangementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Arrangements');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="arrangement-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr><br>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'at',
            'branch0.name',
            'createdBy.username',

            [
                'class' => 'yii\grid\ActionColumn',
                'options' => ['style' => 'width:120px;'],
                'template' => '<div class="btn-group btn-group-sm" role="group" aria-label="...">{view}</div>',
                'buttons' => [
                    'view' => function ($url, $searchModel, $key) {
                        return Html::a('<i class="bi bi-eye"></i>', $url, ['class' => 'btn btn-default']);
                    },
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>