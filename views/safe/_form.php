<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Branches;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Safe */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="safe-form">

    <?php $form = ActiveForm::begin(); ?>

     <?php     
        echo $form->field($model, 'branch')->widget(Select2::class, [
        'data' => ArrayHelper::map(Branches::find()->all(),'id', 'name'),
        'language' => 'ar',
        'options' => ['placeholder' => 'الرجاء اختيار اسم الفرع ...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple'=>false,
        ],
     ]);
     ?>

    <?= $form->field($model, 'safeNo')->dropDownList([ '1' => ' خزينة رقـم 1', '2' => 'خزينة رقـم 2' ], ['prompt' => 'اختيار اسم الخزينة']) ?>

    <?= $form->field($model, 'value')->textInput() ?>

    <?php 
       echo $form->field($model, 'at')->widget(
        DatePicker::class,
        [
            'language' => 'ar',
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'todayBtn' => true,
            ]
        ]
        );
    ?>
     <?= $form->field($model, 'type')->dropDownList([ '1' => ' صادر', '2' => 'وارد' ], ['prompt' => 'اختيار نوع الحركة']) ?>

    <?= $form->field($model, 'why')->textarea(['rows' => 6]) ?>
    <br>
    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>'.' '.Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-window-close"></i>'.' '.Yii::t('app', 'Clear'), ['/safe/create'], ['class'=>'btn btn-danger btn-lg']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
