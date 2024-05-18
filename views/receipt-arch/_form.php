<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ReceiptArch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="receipt-arch-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'rId')->textInput() ?>

    <?= $form->field($model, 'clinet')->textInput() ?>

    <?= $form->field($model, 'at')->textInput() ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'why')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'payWay')->dropDownList([ 'نقدا' => 'نقدا', 'صك' => 'صك', 'بطاقة' => 'بطاقة', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'delete_by')->textInput() ?>

    <?= $form->field($model, 'delete_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
