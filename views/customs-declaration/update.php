<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CustomsDeclaration */

$this->title = Yii::t('app', 'Update Customs Declaration: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customs Declarations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="customs-declaration-update">
    <div class="row">
    <div class="col-lg-3"></div>
    <div class="col-lg-6">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
    <div>
    </div>
</div>
