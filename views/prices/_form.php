<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Prices */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="prices-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'costPrice')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'maxPrice')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'minPrice')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'minPrice2')->textInput() ?>

    <?= $form->field($model, 'minPrice3')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
