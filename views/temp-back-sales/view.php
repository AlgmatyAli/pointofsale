<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\TempBackSales */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Temp Back Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="temp-back-sales-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= Yii::t('app', 'Temp Back Sales').' '. Html::encode($this->title) ?></h2>
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
            'attribute' => 'category0.name',
            'label' => Yii::t('app', 'Category'),
        ],
        'serial_number',
        'quantity',
        'costPrice',
        'salePrice',
        'box',
        'state',
        'expire',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]);
?>
    </div>
    <div class="row">
        <h4>User<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnUser = [
        ['attribute' => 'id', 'visible' => false],
        'username',
                'isActive',
        'createedDate',
        'phone',
        'email',
        'path',
        'branch',
        'permission',
    ];
    echo DetailView::widget([
        'model' => $model->createdBy,
        'attributes' => $gridColumnUser    ]);
    ?>
    <div class="row">
        <h4>User<?= ' '. Html::encode($this->title) ?></h4>
    </div>
    <?php 
    $gridColumnUser = [
        ['attribute' => 'id', 'visible' => false],
        'username',
                'isActive',
        'createedDate',
        'phone',
        'email',
        'path',
        'branch',
        'permission',
    ];
    echo DetailView::widget([
        'model' => $model->updatedBy,
        'attributes' => $gridColumnUser    ]);
    ?>
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
        'place',
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
</div>
