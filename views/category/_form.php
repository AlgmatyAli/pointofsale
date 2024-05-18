<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">
    <?php echo Html::button('<i class="fa fa-fw fa-fast"></i>' . ' ' . Yii::t('app', 'Search'), ['value' => Url::to(['data-table']), 'class' => 'btn btn-info popup']); ?>
    <br><br>
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
    <div class="col-md-4">
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'class')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'serialNo')->textInput() ?>

    <?= $form->field($model, 'unit')->textInput(['maxlength' => true, 'value'=>'قطعة']) ?>

    <?= $form->field($model, 'box')->textInput(['value'=> 1]) ?>

    <?= $form->field($model, 'place')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'weight')->textInput(['maxlength' => true]) ?>

    </div>
    
    <div class="col-md-4">
    <?= $form->field($model, 'cost')->textInput() ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'quantity')->textInput() ?>

    <?= $form->field($model, 'minimum')->textInput(['value'=> 3]) ?>
   
    <?= $form->field($model, 'company')->textInput() ?>
   
    <?= $form->field($model, 'country')->textInput() ?>

    <?= $form->field($model, 'commCode')->textInput() ?>

    <?php
    //  $form->field($model, 'commCode')->widget(CKEditor::className(), [
	// 	'options' => ['rows' => 6],
	// 	'preset' => 'basic'
    // ]) 
    ?>
   
    </div>
    <div class="col-md-4">
    <br>
                <?php if (empty($model->path)) {
                      
                    echo $form->field($model, 'file')->widget(FileInput::classname(),['options' => ['accept' => 'image/*'],]);  
                }else{
                    $allimage[] = Html::img($model->path,  ['class'=>'file-preview-image']);
                
                    echo $form->field($model, 'file')->widget(FileInput::classname(),['options' => ['accept' => 'image/*'],
                    'pluginOptions' => [
                    'initialPreview'=>[$allimage],
                    'overwriteInitial'=>false],
                    ]
                );    

                }
                ?>
    </div>
            
    </div>
    <div class="row">
    <div class="col-md-2"></div>
    <div class="col-sm-5">
     <?= $form->field($model, 'ending')->checkBox(['checked' => true]) ?>
     <?= $form->field($model, 'moreRequest')->checkBox() ?>
     <?= $form->field($model, 'qShow')->checkBox() ?>
    
    <br>
    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>'.' '.Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-window-close"></i>'.' '.Yii::t('app', 'Cancel'),Yii::$app->request->referrer, ['class'=>'btn btn-danger btn-lg']) ?>
    </div>
    </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("$(function() {
     $('.popupModal').click(function(e) {
     e.preventDefault();
     $('#modal').modal('show').find('.modal-content')
     .load($(this).attr('href'));
     });
});");

?>
