<?php

use app\models\Branches;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\TransferSearch */
?>

<div class="transfer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['data-pjax' => 1],
    ]); ?>

    <div class="row g-3">
        <div class="col-md-6 col-xl-3">
            <?= $form->field($model, 'fromBr')->widget(Select2::class, [
                'data' => ArrayHelper::map(Branches::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => ['placeholder' => 'الرجاء اختيار اسم الفرع ...'],
                'pluginOptions' => ['allowClear' => true, 'multiple' => false],
            ]) ?>
        </div>

        <div class="col-md-6 col-xl-3">
            <?= $form->field($model, 'at')->widget(DateRangePicker::class, [
                'language' => 'en',
                'convertFormat' => false,
                'options' => ['class' => 'form-control', 'autocomplete' => 'off'],
                'pluginOptions' => [
                    'timePicker' => false,
                    'timePickerIncrement' => 30,
                    'locale' => ['format' => 'YYYY-MM-DD'],
                ],
            ])->label('تاريخ الحركة') ?>
        </div>

        <div class="col-6 col-xl-2">
            <?= $form->field($model, 'type')->dropDownList(['1' => 'صادر', '2' => 'وارد'], ['prompt' => 'اختيار نوع الحركة']) ?>
        </div>

        <div class="col-6 col-xl-2">
            <?= $form->field($model, 'value')->textInput(['inputmode' => 'decimal', 'autocomplete' => 'off']) ?>
        </div>

        <div class="col-12 col-xl-2 d-flex gap-2 align-items-end mb-3">
            <?= Html::submitButton('<i class="bi bi-search"></i> ' . Yii::t('app', 'Search'), ['class' => 'btn btn-primary flex-grow-1']) ?>
            <?= Html::a('<i class="bi bi-eraser"></i>', Url::toRoute(['index']), [
                'class' => 'btn btn-outline-secondary',
                'title' => Yii::t('app', 'Erase'),
                'aria-label' => Yii::t('app', 'Erase'),
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
