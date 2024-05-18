<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SalesDetailsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-sales-details-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <div class="row">
        <div class="col-md-4">
        <?= $form->field($model, 'category')->widget(\kartik\widgets\Select2::classname(), [
        'data' => \yii\helpers\ArrayHelper::map(\app\models\Category::find()->orderBy('id')->asArray()->all(), 'id', 'name'),
        'options' => ['placeholder' => Yii::t('app', 'Choose Category')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    

    <?= $form->field($model, 'serial_number')->textInput(['rows' => 6]) ?>

        </div>
        <div class="col-md-4">
        <?php  echo $form->field($model, 'quantity')->textInput(['placeholder' => 'Quantity'])  ?>

<?php  echo $form->field($model, 'salePrice')->textInput(['maxlength' => true, 'placeholder' => 'SalePrice'])  ?>

        </div>
        <div class="col-md-4">
        <?php echo $form->field($model, 'original_price')->textInput(['maxlength' => true, 'placeholder' => 'Original Price'])  ?>

    <?php  echo $form->field($model, 'mac_address')->textInput(['rows' => 6])  ?>

    
        </div>
    
    
    </div>

    
   
    

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
      
    </div>

    <?php ActiveForm::end(); ?>

</div>
