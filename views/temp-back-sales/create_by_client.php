<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TempBackSales $model */
/** @var app\models\TempBackSalesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $serial_number */

$this->title = Yii::t('app', 'Create Temp Back Sales By Client');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Temp Back Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="temp-back-sales-create">

    <?= $this->render('back_form', [
        'model' => $model,
        'serial_number' => $serial_number,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>

</div>