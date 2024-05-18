<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use yii\bootstrap4\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\Image */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="image-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'file')->widget(FileInput::classname(),['options' => ['accept' => '*/*','id'=>'files'],]);  ?>


    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>'.' '.Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-lg']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
