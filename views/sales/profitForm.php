<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\helpers\Url;
use dosamigos\datepicker\DatePicker;
/* @var $this yii\web\View */
/* @var $model app\models\Sales */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="profit-form">

    <?php $form = ActiveForm::begin(); ?>
   
      <?= 
        $form->field($model, 'min_date')->widget(
        DatePicker::className(),
        [
            'value' => '02-16-2012',
            'language' => 'ar',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'todayBtn' => true,
            ]
        ]
            ); ?>
        <?= 
        $form->field($model, 'max_date')->widget(
        DatePicker::className(),
        [
            'value' => '02-16-2012',
            'language' => 'ar',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'todayBtn' => true,
            ]
        ]
        ); ?>
     <br>

    <br><div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>'.' '.Yii::t('app', 'Run'), ['class' => 'btn btn-success btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>'.' '.Yii::t('app', "Erase"), Url::toRoute(['profit']), ['class' => 'btn btn-danger btn-lg']) ?>
    </div>
      
    <?php ActiveForm::end(); ?>
</div>
