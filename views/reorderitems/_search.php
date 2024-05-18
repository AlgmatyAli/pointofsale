<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ReorderitemsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-reorderitems-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Name']) ?>

    <?= $form->field($model, 'serialNo')->textInput(['maxlength' => true, 'placeholder' => 'SerialNo']) ?>

    <?= $form->field($model, 'minimum')->textInput(['placeholder' => 'Minimum']) ?>

    <?= $form->field($model, 'quantity')->textInput(['placeholder' => 'Quantity']) ?>

    <?php /* echo $form->field($model, 'class')->textInput(['maxlength' => true, 'placeholder' => 'Class']) */ ?>

    <?php /* echo $form->field($model, 'company')->textInput(['maxlength' => true, 'placeholder' => 'Company']) */ ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
