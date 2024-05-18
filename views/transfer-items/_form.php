<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\models\Branches;
use yii\helpers\ArrayHelper;
use dosamigos\datepicker\DatePicker;


/* @var $this yii\web\View */
/* @var $model app\models\TransferItems */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transfer-items-form">

    <?php $form = ActiveForm::begin(); ?>

   <?php     
        echo $form->field($model, 'fromBranch')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Branches::find()->all(),'id', 'name'),
        'language' => 'ar',
        'options' => ['placeholder' => 'اختيار اسم الفرع المسحوب منه...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple'=>false,
        ],
     ]);
     ?>
    <?php     
        echo $form->field($model, 'toBranch')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Branches::find()->all(),'id', 'name'),
        'language' => 'ar',
        'options' => ['placeholder' => 'اختيار اسم الفرع المودع له...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple'=>false,
        ],
     ]);
     ?>

    <?php 
       echo $form->field($model, 'at')->widget(
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
        );
    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
