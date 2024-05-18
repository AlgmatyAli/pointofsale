<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\ReceiptSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="histrans">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'at')->dropDownList([ '2021' => '2021', '2022' => '2022', '2023' => '2023', '2024' => '2024', '2025' => '2025'], 
        ['prompt' => 'الرجاء اختيار السنـة...']
    ) ?>

     <br><div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>'.' '.Yii::t('app', 'Run'), ['class' => 'btn btn-success btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>'.' '."Erase", Url::toRoute(['index']), ['class' => 'btn btn-danger btn-lg']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
