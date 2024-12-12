<?php

use kartik\daterange\DateRangePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PurchasesDetailsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-purchases-details-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>
    <div class="row">

        <div class="col-md-4">

            <?php
            echo '<label class="control-label">تاريخ الفاتورة</label>';
            echo DateRangePicker::widget([
                'model' => $model,
                'attribute' => 'at',
                'language' => 'en',
                'convertFormat' => false,
                'pluginOptions' => [
                    'timePicker' => false,
                    'timePickerIncrement' => 30,
                    'locale' => [
                        'format' => 'YYYY-MM-DD'
                    ]
                ]
            ]); ?>
        </div>
        <div class="col-md-4">

            <?= $form->field($model, 'type')->dropDownList(['1' => 'فاتورة مشتريات', '2' => 'فاتورة مسترجع مشتريات', '3' => 'فاتورة مشتريات معلقة'], ['prompt' => 'نوع الحركـة']) ?>

        </div>
    </div>
    <br>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>