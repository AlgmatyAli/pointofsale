<?php

use app\models\base\Currancy;
use app\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Sales */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="sales-form">

    <?php $form = ActiveForm::begin(); ?>
    <?=  $form->field($model, 'currancy')->widget(Select2::class, [
     'data' => ArrayHelper::map(Currancy::find()
     ->all(),'id', 'name'),
     'language' => 'ar',
     'options' => ['placeholder' => 'الرجاء اختيار اسم العملة ...'],
     'pluginOptions' => [
         'allowClear' => true,
         'multiple'=>false,
     ],
    ]); ?>

   <?=  $form->field($model, 'user_insert')->widget(Select2::class, [
     'data' => ArrayHelper::map(User::find()
     ->where(['=', 'isActive', 'active'])
     ->all(),'id', 'username'),
     'language' => 'ar',
     'options' => ['placeholder' => 'الرجاء اختيار اسم المستخدم ...'],
     'pluginOptions' => [
         'allowClear' => true,
         'multiple'=>false,
     ],
    ]); ?>

      <?= 
        $form->field($model, 'min_date')->widget(
        DatePicker::class,
        [
            'value' => '02-16-2012',
            'language' => 'ar',
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'todayBtn' => true,
            ]
        ]
            ); ?>
        <?= 
        $form->field($model, 'max_date')->widget(
        DatePicker::class,
        [
            'value' => '02-16-2012',
            'language' => 'ar',
            'pluginOptions' => [
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
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>'.' '.Yii::t('app', "Erase"), Url::toRoute(['ftran']), ['class' => 'btn btn-danger btn-lg']) ?>
    </div>
      
    <?php ActiveForm::end(); ?>
</div>
