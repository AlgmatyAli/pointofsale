<?php

use app\models\Employee;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Url;
use app\models\User;
use app\models\Inventory;
/* @var $this yii\web\View */
/* @var $model app\models\ReceiptSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="histrans">

    <?php $form = ActiveForm::begin(); ?>
 
     <?php
       echo $form->field($model, 'employee')->widget(Select2::classname(), [
        'data' =>ArrayHelper::map(Employee::find()->where(['=','state',0])
           ->all(),'id', 'name'),
        'language' => 'ar',
        'id'=>'catId',
        'options' => ['placeholder' => 'الرجاء اختيار اسم الموظف ...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple'=>false,
        ],
        ]); 
        ?>

     <?=
        $form->field($model, 'min_date')->widget(
        DatePicker::className(),
        [
            'language' => 'ar',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'todayBtn' => true,
            ]
        ])->label(Yii::t('app', 'Min Date')); 
        ?>
        <?= 
        $form->field($model, 'max_date')->widget(
        DatePicker::className(),
        [
            'language' => 'ar',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'todayBtn' => true,
            ]
        ]
        )->label(Yii::t('app', 'Max Date')); ; 
        ?>

     <br><div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>'.' '.Yii::t('app', 'Run'), ['class' => 'btn btn-success btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>'.' '.Yii::t('app', "Erase"), Url::toRoute(['histrans']), ['class' => 'btn btn-danger btn-lg']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
