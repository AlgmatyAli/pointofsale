<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BalanceHistory */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Balance History',
]) . ' ' . $model->value;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Balance History'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->value, 'url' => ['view', ]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="balance-history-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
