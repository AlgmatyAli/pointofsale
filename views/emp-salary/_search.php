<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\Employee;
/* @var $this yii\web\View */
/* @var $model app\models\ReceiptSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="receipt-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'employee')->widget(Select2::class, [
                'data' => ArrayHelper::map(Employee::find()->where(['=', 'state', 0])
                    ->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => ['placeholder' => 'الرجاء اختيار اسم الموظف ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]); ?>
        </div>
        <div class="col-lg-3">

            <?= $form->field($model, 'month')->dropDownList(
                [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                    '9' => '9',
                    '10' => '10',
                    '11' => '11',
                    '12' => '12',
                ],
                ['prompt' => 'الرجاء اختيار الشهر...']
            ) ?>
        </div>
        <div class="col-lg-3">
            <?php
            $years = range(2020, date('Y'));
            $years = array_combine($years, $years);
            echo $form->field($model, 'year')->dropDownList(
                $years,
                ['prompt' => 'الرجاء اختيار السنـة...']
            ) ?>

        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'type')->dropDownList(
                ['1' => 'سحب', '2' => 'خصم', '3' => 'اضافي'],
                ['prompt' => 'الرجاء اختيار نوع الحركة...']
            ) ?>

        </div>
    </div>
    <br>
    <br>
    <div class="form-group">
        <?= Html::a(Yii::t('app', 'Create Emp Salary'), ['create'], ['class' => 'btn btn-success btn-sm']) ?>
        <?= Html::submitButton('<i class="fa fa-fw fa-search"></i>' . ' ' . Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-sm']) ?>
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>' . ' ' . Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger btn-sm']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>