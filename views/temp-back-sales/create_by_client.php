<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TempBackSales */

$this->title = Yii::t('app', 'Create Temp Back Sales By Client');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Temp Back Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="temp-back-sales-create">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('back_form', [
        'model' => $model,
        'serial_number' => $serial_number,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>

</div>
