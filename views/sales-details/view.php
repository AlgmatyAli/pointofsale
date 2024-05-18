<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\SalesDetails */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-details-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= Yii::t('app', 'Sales Details').' '. Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-3" style="margin-top: 15px">
            
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
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
        [
            'attribute' => 'category0.name',
            'label' => Yii::t('app', 'Category'),
        ],
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
        <h4>Category<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnCategory = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'class',
        'unit',
        'box',
        'cost',
        'price',
        'quantity',
        'minimum',
        'ending',
        'qShow',
        'status',
        'serialNo',
        'country',
        'company',
        'path',
        'user_insert',
        'user_update',
        'update_at',
    ];
    echo DetailView::widget([
        'model' => $model->category0,
        'attributes' => $gridColumnCategory    ]);
    ?>
    <div class="row">
        <h4>Sales<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnSales = [
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
        'attributes' => $gridColumnSales    ]);
    ?>
</div>
