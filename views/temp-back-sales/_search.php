<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TempBackSalesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-temp-back-sales-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'category')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\app\models\Category::find()->orderBy('id')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => Yii::t('app', 'Choose Category')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'serial_number')->textInput(['maxlength' => true, 'placeholder' => 'Serial Number']) ?>

    <?= $form->field($model, 'quantity')->textInput(['placeholder' => 'Quantity']) ?>

    <?= $form->field($model, 'costPrice')->textInput(['maxlength' => true, 'placeholder' => 'CostPrice']) ?>

    <?php /* echo $form->field($model, 'salePrice')->textInput(['maxlength' => true, 'placeholder' => 'SalePrice']) */ ?>

    <?php /* echo $form->field($model, 'box')->textInput(['placeholder' => 'Box']) */ ?>

    <?php /* echo $form->field($model, 'state')->textInput(['placeholder' => 'State']) */ ?>

    <?php /* echo $form->field($model, 'expire')->widget(\kartik\datecontrol\DateControl::classname(), [
        'type' => \kartik\datecontrol\DateControl::FORMAT_DATE,
        'saveFormat' => 'php:Y-m-d',
        'ajaxConversion' => true,
        'options' => [
            'pluginOptions' => [
                'placeholder' => Yii::t('app', 'Choose Expire'),
                'autoclose' => true
            ]
        ],
    ]); */ ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
