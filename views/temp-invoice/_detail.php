<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\TempInvoice */

?>
<div class="temp-invoice-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= Html::encode($model->id) ?></h2>
        </div>
    </div>

    <div class="row">
<?php 
    $gridColumn = [
        ['attribute' => 'id', 'visible' => false],
        'invoice_number',
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
</div>