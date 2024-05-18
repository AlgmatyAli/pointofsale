<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

?>
<div class="category-info">
    <div class="row">
        <div class="col-md-6">
    <?= DetailView::widget([
        'modelInfo' => $modelInfo,
        'attributes' => [
            'id',
            'serialNo',
            'company',
            'place',
            'quantity',
            'maxPrice',
            'minPrice',
        ],
    ]) ?>
    <div>
        <div>
</div>
