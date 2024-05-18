<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Balance */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Balance',
]) . ' ' . $model->value;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Balance'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->value, 'url' => ['view', ]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="balance-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
