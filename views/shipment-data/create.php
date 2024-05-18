<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ShipmentData */

$this->title = Yii::t('app', 'Create Shipment Data');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Shipment Datas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shipment-data-create">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
