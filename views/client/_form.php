<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\SwitchInput;
/* @var $this yii\web\View */
/* @var $model app\models\Client */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-lg-1"></div>
        <div class="col-lg-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'balance')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'debt')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'type')->dropDownList(['0' => 'زبون', '1' => 'مورد', '2' => 'كلاهما'], ['prompt' => 'اختيار نوع العميل...']) ?>
            <br>
            <div class="form-group">
                <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-danger']) ?>
            </div>
        </div>
        <div class='col-md-2' style="height: 3%;">
            <?php
            echo $form->field($model, 'post_paid')->widget(SwitchInput::class, [
                'pluginOptions' => [
                    'size' => 'small',
                    'onColor' => 'success',
                    'offColor' => 'danger',
                ]
            ]); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>