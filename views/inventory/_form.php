<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Inventory */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="inventory-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'kind')->textInput(['maxlength' => true, 'placeholder' => 'Kind']) ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Name']) ?>

    <?= $form->field($model, 'quantity')->textInput(['placeholder' => 'quantity']) ?>

    <?= $form->field($model, 'box')->textInput(['placeholder' => 'Box']) ?>

    <?= $form->field($model, 'unit')->textInput(['maxlength' => true, 'placeholder' => 'Unit']) ?>

    <?= $form->field($model, 'class')->textInput(['maxlength' => true, 'placeholder' => 'Class']) ?>

    <?= $form->field($model, 'branch')->textInput(['placeholder' => 'branch']) ?>

    <?= $form->field($model, 'tranDate')->textInput(['maxlength' => true, 'placeholder' => 'TranDate']) ?>

    <?= $form->field($model, 'serialNo')->textInput(['maxlength' => true, 'placeholder' => 'SerialNo']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
