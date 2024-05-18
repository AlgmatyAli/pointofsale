<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Balance */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="balance-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true, 'placeholder' => 'Value']) ?>

    <?= $form->field($model, 'clinet')->textInput(['placeholder' => 'Clinet']) ?>

    <?= $form->field($model, 'currancy')->textInput(['placeholder' => 'Currancy']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
