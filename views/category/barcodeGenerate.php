<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Category;
use barcode\barcode\BarcodeGenerator as BarcodeGenerator;

/* @var $this yii\web\View */
/* @var $model app\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
    <div class="col-md-12">
       
        <?= $form->field($model, 'id')->widget(\kartik\widgets\Select2::classname(), [
       'data' => ArrayHelper::map(Category::find()->all(),'id', 'name'),
        'language' => 'en',
        'options' => ['placeholder' => Yii::t('app', 'Choose Category'),
        'dir' => 'rtl',
        'onchange' => 'getData( $(this) )'
        ],
        'pluginOptions' => [
            'allowClear' => true 
        ],
        
    ]); ?> 

         <?= $form->field($model, 'serialNo')->textInput() ?>
         <?= $form->field($model, 'name')->hiddenInput()->label(false) ?>

        <br><br>
         <div class="form-group">
              <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>'.' '.Yii::t('app', 'Generate Barcode'), ['class' => 'btn btn-success btn-lg']) ?>
              <?= Html::a('<i class="fa fa-fw fa-window-close"></i>'.' '.Yii::t('app', 'Cancel'),Yii::$app->request->referrer, ['class'=>'btn btn-danger btn-lg']) ?>
        </div>
    </div>
    </div>
    </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
