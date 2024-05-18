<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BalanceHistory */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="balance-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true, 'placeholder' => 'Value']) ?>

    <?= $form->field($model, 'clinet')->textInput(['placeholder' => 'Clinet']) ?>

    <?= $form->field($model, 'currancy')->textInput(['placeholder' => 'Currancy']) ?>

    <?= $form->field($model, 'AT')->widget(\kartik\datecontrol\DateControl::classname(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_DATE,
        'saveFormat' => 'php:Y-m-d',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => Yii::t('app', 'Choose At'),
                'autoclose' => true
            ]
        ],
    ]); ?>

    <?= $form->field($model, 'why')->textInput(['maxlength' => true, 'placeholder' => 'Why']) ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true, 'placeholder' => 'Type']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
