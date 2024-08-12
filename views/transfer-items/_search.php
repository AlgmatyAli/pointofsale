<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Branches;
use kartik\date\DatePicker;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\TransferItemsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transfer-items-search">
    
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
<div class='row'>
    <div class='col-md-4'>
   <?php     
        echo $form->field($model, 'fromBranch')->widget(Select2::class, [
        'data' => ArrayHelper::map(Branches::find()->all(),'id', 'name'),
        'language' => 'ar',
        'options' => ['placeholder' => 'اختيار اسم الفرع المسحوب منه...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple'=>false,
        ],
     ]);
     ?>
     </div>
     <div class='col-md-4'>
    <?php     
        echo $form->field($model, 'toBranch')->widget(Select2::class, [
        'data' => ArrayHelper::map(Branches::find()->all(),'id', 'name'),
        'language' => 'ar',
        'options' => ['placeholder' => 'اختيار اسم الفرع المودع له...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple'=>false,
        ],
     ]);
     ?>
    </div>
    <div class='col-md-4'>
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
</div>
</div>
    <hr>
    <br><div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-search"></i>'.' '.Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>'.' '.Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger btn-lg']) ?>
    </div>
    <br>

    <?php ActiveForm::end(); ?>

</div>
