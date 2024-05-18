<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Items;
use app\models\Branches;

/* @var $this yii\web\View */
/* @var $model app\models\ExpensesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="expenses-search">
    <div class="row">
    <div class="col-lg-2"></div>
    <div class="col-lg-4">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?php
     echo '<label class="control-label">تاريخ الصرف</label>';
     echo DateRangePicker::widget([
        'model'=>$model,
        'attribute'=>'at',
        'language' => 'en',
        'convertFormat'=>false,
        'pluginOptions'=>[
            'timePicker'=>false,
            'timePickerIncrement'=>30,
            'locale'=>[
                'format'=>'YYYY-MM-DD'
            ]
        ]
    ]);

    ?><br>

    <?php 
     echo $form->field($model, 'itemId')->widget(Select2::classname(), [
        'data' =>ArrayHelper::map(Items::find()
           ->all(),'id', 'name'),
        'language' => 'ar',
        'options' => ['placeholder' => 'الرجاء اختيار اسم المصروف ...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple'=>false
        ],
    ]);
    ?>

    <br>
    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-search"></i>'.' '.Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>'.' '.Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger btn-lg']) ?>
    </div>

    </div>
    <div class="col-lg-4">
   
    <?= $form->field($model, 'value') ?>

    

    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
