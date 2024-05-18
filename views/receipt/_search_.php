<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Client;
use kartik\daterange\DateRangePicker;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\ReceiptSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="receipt-search">
    <div class="row">
    <div class="col-lg-6">
    <?php $form = ActiveForm::begin([
        'action' => ['index_'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
 
    <?= $form->field($model, 'rId') ?>

    <?=  $form->field($model, 'clinet')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Client::find()
       // ->where(['branch' => Yii::$app->user->identity->branch])
       ->where(['in', 'type', [1,2]])
        ->all(),'id', 'name'),
        'language' => 'ar',
        'options' => ['placeholder' => 'الرجاء اختيار اسم العميل ...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple'=>false
        ],
    ]);
    ?>

     <?php
     echo '<label class="control-label">تاريخ الايصال</label>';
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

     <br><div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-search"></i>'.' '.Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>'.' '.Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger btn-lg']) ?>
    </div>

    </div>
    <div class="col-lg-6">
    <?= $form->field($model, 'value') ?>

    <?= $form->field($model, 'payWay')->dropDownList([ 'نقدا' => 'نقدا', 'صك' => 'صك', 'بطاقة' => 'بطاقة', ], ['prompt' => 'الرجاء اختيار طريقة الدفع']) ?>

    <?= $form->field($model, 'type')->widget(Select2::classname(), [
                'data' => [
                    '2' => ' صرف', '1' => 'قبض'
                ],
                'language' => 'ar',
                'options' => [
                    //  'placeholder' => 'الرجاء اختيار اسم العميل ...'
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,


                ],
            ]); ?>

    <?php //$form->field($model, 'type')->dropDownList([ '1' => 'قبض', '2' => 'صرف'], ['prompt' => '']) ?>
    </div>
    </div>
   

    <?php ActiveForm::end(); ?>

</div>
