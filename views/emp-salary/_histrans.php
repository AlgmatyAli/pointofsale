<?php

use app\models\Employee;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\ReceiptSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="histrans">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    echo $form->field($model, 'employee')->widget(Select2::class, [
        'data' => ArrayHelper::map(Employee::find()->where(['=', 'state', 0])
            ->all(), 'id', 'name'),
        'language' => 'ar',
        'id' => 'catId',
        'options' => ['placeholder' => 'الرجاء اختيار اسم الموظف ...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple' => false,
        ],
    ]);
    ?>

    <?=
    $form->field($model, 'min_date')->widget(
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
    )->label(Yii::t('app', 'Min Date'));
    ?>
    <?=
    $form->field($model, 'max_date')->widget(
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
    )->label(Yii::t('app', 'Max Date'));;
    ?>

    <br>
    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Run'), ['class' => 'btn btn-success  ']) ?>
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>' . ' ' . Yii::t('app', "Erase"), Url::toRoute(['histrans']), ['class' => 'btn btn-danger  ']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>