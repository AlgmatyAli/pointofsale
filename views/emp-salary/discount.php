<?php

use app\models\Currancy;
use app\models\Employee;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\EmpSalary */

// السنوات من 2020 حتى السنة الحالية
$years = range(2020, (int) date('Y'));
$years = array_combine($years, $years);

$months = [
    '1' => '1 - يناير', '2' => '2 - فبراير', '3' => '3 - مارس', '4' => '4 - أبريل',
    '5' => '5 - مايو', '6' => '6 - يونيو', '7' => '7 - يوليو', '8' => '8 - أغسطس',
    '9' => '9 - سبتمبر', '10' => '10 - أكتوبر', '11' => '11 - نوفمبر', '12' => '12 - ديسمبر',
];

// للسجل الجديد: السنة والشهر الحاليان كقيمة افتراضية (كان على المستخدم اختيارهما في كل مرة)
if ($model->isNewRecord) {
    $model->year = $model->year ?: (int) date('Y');
    $model->month = $model->month ?: (int) date('n');
}

$employees = ArrayHelper::map(Employee::find()->where(['=', 'state', 0])->all(), 'id', 'name');
$currencies = ArrayHelper::map(Currancy::find()->all(), 'id', 'name');

$section = function (string $icon, string $title, string $color) {
    return '<div class="d-flex align-items-center gap-2 mb-2 mt-3 text-' . $color . '">'
        . '<i class="bi ' . $icon . '"></i><span class="fw-bold">' . Html::encode($title) . '</span>'
        . '<hr class="flex-grow-1 my-0 opacity-25"></div>';
};

$datePicker = [
    'language' => 'ar',
    'options' => ['placeholder' => 'yyyy-mm-dd', 'autocomplete' => 'off'],
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'yyyy-mm-dd',
        'todayHighlight' => true,
        'todayBtn' => true,
    ],
];

$this->registerCss(<<<'CSS'
.emp-salary-form .form-label { font-size: .85rem; margin-bottom: .2rem; }
.emp-salary-form .es-amount input { font-size: 1.3rem; font-weight: 700; text-align: center; }
.emp-salary-form .es-summary { background: var(--bs-tertiary-bg); border: 1px solid var(--bs-border-color); border-radius: .6rem; }
.emp-salary-form .es-summary input { font-weight: 600; text-align: center; }
.emp-salary-form .es-summary .mb-3 { margin-bottom: 0 !important; }
CSS);
?>

<div class="emp-salary-form">

    <?php $form = ActiveForm::begin(['id' => 'emp-salary-form']); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger py-2 small']) ?>

    <div class="alert alert-warning d-flex align-items-center gap-2 py-2">
        <i class="bi bi-scissors fs-4"></i>
        <div><div class="fw-bold">خصم على الموظف</div><div class="small">يُسجَّل المبلغ كخصم من راتب الشهر المحدد.</div></div>
    </div>

    <?= $section('bi-person-badge', 'الموظف', 'warning') ?>
    <?= $form->field($model, 'employee')->widget(Select2::class, [
        'data' => $employees,
        'language' => 'ar',
        'options' => ['placeholder' => 'الرجاء اختيار اسم الموظف ...'],
        'pluginOptions' => ['allowClear' => true, 'multiple' => false],
    ]) ?>

    <?= $section('bi-calendar3', 'الفترة', 'warning') ?>
    <div class="row g-3">
        <div class="col-md-5">
            <?= $form->field($model, 'at')->widget(DatePicker::class, $datePicker) ?>
        </div>
        <div class="col-6 col-md-4">
            <?= $form->field($model, 'month')->dropDownList($months, ['prompt' => 'الرجاء اختيار الشهر...']) ?>
        </div>
        <div class="col-6 col-md-3">
            <?= $form->field($model, 'year')->dropDownList($years, ['prompt' => 'الرجاء اختيار السنـة...']) ?>
        </div>
    </div>

    <?= $section('bi-cash-coin', 'المبلغ', 'warning') ?>
    <div class="row g-3">
        <div class="col-md-6 es-amount">
            <?= $form->field($model, 'value')->textInput(['inputmode' => 'decimal', 'autocomplete' => 'off', 'placeholder' => '0.000']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'currancy')->widget(Select2::class, [
                'data' => $currencies,
                'language' => 'ar',
                'pluginOptions' => ['allowClear' => true, 'multiple' => false],
            ]) ?>
        </div>
    </div>

    <?= $form->field($model, 'why')->textarea(['rows' => 4, 'placeholder' => 'السبب / ملاحظات (اختياري)']) ?>

    <div class="d-flex gap-2 mt-3">
        <?= Html::submitButton('<i class="bi bi-check2-circle"></i> ' . Yii::t('app', 'Save'), ['class' => 'btn btn-warning btn-lg flex-grow-1']) ?>
        <?= Html::a('<i class="bi bi-arrow-counterclockwise"></i> ' . Yii::t('app', 'Clear'), ['/emp-salary/create'], ['class' => 'btn btn-lg btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
