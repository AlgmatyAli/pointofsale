<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Branches;
use kartik\daterange\DateRangePicker;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\TransferSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transfer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
 <div class="row">
    <div class="col-lg-2"></div>
    <div class="col-lg-4">    
    <?php  
                echo $form->field($model, 'fromBr')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Branches::find()->all(),'id', 'name'),
                'language' => 'ar',
                'options' => ['placeholder' => 'الرجاء اختيار اسم الفرع ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple'=>false
                ],
             ]);
    ?>

    <?= $form->field($model, 'value') ?>
    
    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-search"></i>'.' '.Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>'.' '.Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger btn-lg']) ?>
    </div>
    </div>
    <div class="col-lg-4">
    
    <?php
     echo '<label class="control-label">تاريخ الحركة</label>';
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

     <?= $form->field($model, 'type')->dropDownList([ '1' => ' صادر', '2' => 'وارد' ], ['prompt' => 'اختيار نوع الحركة']) ?>

    </div>
    </div>
    <br>

    <?php ActiveForm::end(); ?>

</div>
