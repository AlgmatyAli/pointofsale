<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Prices */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="prices-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'minPrice')->textInput(['maxlength' => true])->label(Yii::t('app', 'Low Price')) ?>

            <?= $form->field($model, 'maxPrice')->textInput(['maxlength' => true])->label(Yii::t('app', 'Big Price')) ?>
            <!-- Percentage of increase -->
            <?= $form->field($model, 'percentage')->textInput(['maxlength' => true])->label(Yii::t('app', 'Percentage of increase')) ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>