<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\StocksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Quantity Less Than Zero');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stocks-index">

    <center>
        <h1><?= Html::encode($this->title)?></h1>
        <hr>
    </center>

    <?php
    $gridColumn = [
        [
            'label' => Yii::t('app', 'ID'),
            'headerOptions' => ['style' => 'width:10%'],
            'value' => function ($data) {
                return $data->id;
            },

        ],
        [
            'label' => Yii::t('app', 'Name'),
            'headerOptions' => ['style' => 'width:20%'],
            'value' => function ($data) {
                return $data->name;
            },

        ],

        [
            'label' => Yii::t('app', 'Serial No'),
            'headerOptions' => ['style' => 'width:15%'],
            'value' => function ($data) {
                return $data->serialNo;
            }

        ],

        [
            'label' => Yii::t('app', 'Company'),
            'headerOptions' => ['style' => 'width:10%'],
            'value' => function ($data) {
                return $data->company;
            }

        ],

        [
            'label' => Yii::t('app', 'quantity'),
            'headerOptions' => ['style' => 'width:10%'],
            'value' => function ($data) {
                return $data->quantity;
            }

        ],

        [
            'label' => Yii::t('app', 'اسم الفرع'),
            'headerOptions' => ['style' => 'width:10%'],
            'value' => function ($data) {
                return $data->branchName;
            }

        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{transfer}',
            'buttons' => [
                'transfer' => function ($url, $model, $key) {
                    $url = Url::to(['stocks/transfer', 'id' => $model['id']]);
                    return Html::a('<i class="glyphicon glyphicon-open"></i>', $url, ['class' => 'btn btn-default']);
                }
            ],
        ],
    ];
    ?>
    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumn,
        'summary' => '',
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-inventory']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
        ],
    ]);
    ?>

    <?php Pjax::end(); ?>

</div>
