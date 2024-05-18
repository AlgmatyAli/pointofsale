<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Sales */
/* @var $form yii\widgets\ActiveForm */
$this->title = Yii::t('app', 'Categories');
?>

<div class="sales-form">
    <h3><?= Html::encode($this->title) ?></h3><hr>
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
    <div class="col-lg-1"></div>
    <div class="col-lg-10">
       <?= $form->field($model, 'place')->textInput(['placeholder' => 'Place']) ?>
      <div class="form-group">
           <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>'.' '.Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
           <?= Html::a('<i class="fa fa-fw fa-window-close"></i>'.' '.Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class'=>'btn btn-danger']) ?>
          </div>
      </div>
      </div>
    <?php ActiveForm::end(); ?>
</div>
