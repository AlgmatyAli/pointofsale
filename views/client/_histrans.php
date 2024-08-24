<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;
use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $model app\models\ReceiptSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="histrans">
    
    <?php $form = ActiveForm::begin(); ?>

   <?php
   $catList = [
    0 => 'زبائن',
    1 => 'موردين',
    2 => 'كلاهما'
];
 
    echo $form->field($model, 'type')->dropDownList($catList, ['id'=>'type-id', 'prompt' => 'اختيار نوع العرض']);
    ?>
    
    <?php
    echo $form->field($model, 'id')->widget(DepDrop::class, [
    'type' => DepDrop::TYPE_SELECT2,
    'options'=>['id'=>'type1-id'],
    'pluginOptions'=>[
        'depends'=>['type-id'],
        'placeholder'=>'Select...',
        'url'=>Url::to(['/client/type']),
        'loadingText' => 'Loading child level 2 ...',
    ]
    ]);
    ?>

     <?=
         $form->field($model, 'min_date')->widget(
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
        <?=
         $form->field($model, 'max_date')->widget(
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

    <?= $form->field($model, 'allData')->checkBox(['checked' => false]) ?>


     <br><div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>'.' '.Yii::t('app', 'Run'), ['class' => 'btn btn-success btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>'.' '.Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger btn-lg']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
