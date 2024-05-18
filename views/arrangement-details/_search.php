<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ArrangementDetailsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="arrangement-details-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'arrangement') ?>

    <?= $form->field($model, 'category') ?>

    <?= $form->field($model, 'quantity') ?>

    <?= $form->field($model, 'box') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'expire') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
