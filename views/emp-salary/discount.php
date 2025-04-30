<?php

use app\models\Currancy;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;;
use kartik\date\DatePicker;
use app\models\Employee;

/* @var $this yii\web\View */
/* @var $model app\models\EmpSalary */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="emp-salary-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">

            <?= $form->field($model, 'employee')->widget(Select2::class, [
                'data' => ArrayHelper::map(Employee::find()->where(['=', 'state', 0])
                    ->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => [
                    //'onchange' => 'getSalary( $(this) )',
                    'placeholder' => 'الرجاء اختيار اسم الموظف ...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]); ?>

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
            <?= $form->field($model, 'month')->dropDownList(
                [
                    '0' => 'اختيار الشهر',
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
                [
                    'prompt' => 'الرجاء اختيار الشهر...',
                    //'onchange' => 'getDrawing( $(this) )'
                ]
            ) ?>

            <?= $form->field($model, 'year')->dropDownList(
                ['2021' => '2021', '2022' => '2022', '2023' => '2023', '2024' => '2024', '2025' => '2025'],
                ['prompt' => 'الرجاء اختيار السنـة...']
            ) ?>

            <?= $form->field($model, 'value')->textInput() ?>

            <?= $form->field($model, 'why')->textarea(['rows' => 6]) ?>

            <?php
            echo $form->field($model, 'currancy')->widget(Select2::class, [
                'data' => ArrayHelper::map(Currancy::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>

            <div class="form-group">
                <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-lg']) ?>
                <?= Html::a('<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Clear'), ['/emp-salary/create'], ['class' => 'btn btn-danger btn-lg']) ?>
            </div>

        </div>

        <?php ActiveForm::end(); ?>

    </div>