<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Sales */

?>
<div class="sales-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= Html::encode($model->id) ?></h2>
        </div>
    </div>

    <div class="row">
<?php 
    $gridColumn = [
        ['attribute' => 'id', 'visible' => false],
        'billId',
        'at',
        [
            'attribute' => 'clinet0.name',
            'label' => Yii::t('app', 'Clinet'),
        ],
        'payWay',
        [
            'attribute' => 'branch0.name',
            'label' => Yii::t('app', 'Branch'),
        ],
        'total',
        'paid',
        'notes',
        'path',
       
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]); 
?>
    </div>
</div>