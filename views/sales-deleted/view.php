<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\SalesDeleted */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales Deleted'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-deleted-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= Yii::t('app', 'Sales Deleted').' '. Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-3" style="margin-top: 15px">
            
            <?= Html::a(Yii::t('app', 'Update'), ['update', ], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Delete'), ['delete', ], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ])
            ?>
        </div>
    </div>

    <div class="row">
<?php 
    $gridColumn = [
        ['attribute' => 'id', 'visible' => false],
        'billId',
        'at',
        'clinet',
        'payWay',
        'branch',
        'total',
        'paid',
        'notes',
        'path',
        'type',
        'deleviryAt',
        'carpenter',
        'upholstered',
        'paintId',
        'deleviryId',
        'user_insert',
        'user_update',
        'update_at',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    
    <div class="row">
<?php
if($providerSalesDetailsDeleted->totalCount){
    $gridColumnSalesDetailsDeleted = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
                        'category',
            'quantity',
            'costPrice',
            'salePrice',
            'box',
            'expire',
    ];
    echo Gridview::widget([
        'dataProvider' => $providerSalesDetailsDeleted,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-sales-details-deleted']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode(Yii::t('app', 'Sales Details Deleted')),
        ],
        'export' => false,
        'columns' => $gridColumnSalesDetailsDeleted
    ]);
}
?>

    </div>
</div>
