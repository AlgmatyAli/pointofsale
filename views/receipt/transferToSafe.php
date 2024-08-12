<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Sales */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sales-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
    <div class="col-lg-1"></div>
    <div class="col-lg-10">
      <?= $form->field($model, 'safeNo')->dropDownList([ '1' => ' خزينة رقـم 1', '2' => 'خزينة رقـم 2' ], ['prompt' => 'اختيار اسم الخزينة']) ?>
      <div class="form-group">
           <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>'.' '.Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
           <?= Html::a('<i class="fa fa-fw fa-window-close"></i>'.' '.Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class'=>'btn btn-danger']) ?>
          </div>
      </div>
      </div>
    <?php ActiveForm::end(); ?>
</div>
