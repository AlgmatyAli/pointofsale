<?php

use app\models\Employee;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\LoanPaidSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="loan-paid-search">

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
                'data' => ArrayHelper::map(Employee::find()
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
            <?= $form->field($model, 'year')->dropDownList(
                ['2021' => '2021', '2022' => '2022', '2023' => '2023', '2024' => '2024', '2025' => '2025'],
                ['prompt' => 'الرجاء اختيار السنـة...']
            ) ?>

        </div>
    </div>
    <br>
    <br>
    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-search"></i>' . ' ' . Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>' . ' ' . Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>