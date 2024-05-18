<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ReceiptArchSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="receipt-arch-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'rId') ?>

    <?= $form->field($model, 'clinet') ?>

    <?= $form->field($model, 'at') ?>

    <?= $form->field($model, 'value') ?>

    <?php // echo $form->field($model, 'why') ?>

    <?php // echo $form->field($model, 'payWay') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'delete_by') ?>

    <?php // echo $form->field($model, 'delete_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
