<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TempTransferItems */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Temp Transfer Items',
]) . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Temp Transfer Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="temp-transfer-items-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>