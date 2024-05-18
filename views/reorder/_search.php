<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $model app\models\ReorderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reorder-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

     <?php
             echo '<label class="control-label">تاريخ الفاتورة</label>';
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
       ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
