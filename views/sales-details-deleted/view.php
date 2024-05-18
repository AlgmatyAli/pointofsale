<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\SalesDetailsDeleted */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales Details Deleted'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-details-deleted-view">

    <div class="row">
        <div class="col-sm-8">
            <h2><?= Yii::t('app', 'Sales Details Deleted').' '. Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-4" style="margin-top: 15px">
            <?= Html::a(Yii::t('app', 'Save As New'), ['save-as-new', ], ['class' => 'btn btn-info']) ?>            
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
        [
            'attribute' => 'sales.id',
            'label' => Yii::t('app', 'SalesId'),
        ],
        'category',
        'quantity',
        'costPrice',
        'salePrice',
        'box',
        'expire',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    <div class="row">
        <h4>SalesDeleted<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnSalesDeleted = [
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
        'model' => $model->sales,
        'attributes' => $gridColumnSalesDeleted    ]);
    ?>
</div>
