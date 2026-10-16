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

    <?= $section('bi-calendar3', 'الفترة', 'primary') ?>
    <div class="row g-3">
        <div class="col-6">
            <?= $form->field($model, 'year')->dropDownList($years, ['prompt' => 'الرجاء اختيار السنـة...']) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'month')->dropDownList($months, ['prompt' => 'الرجاء اختيار الشهر...']) ?>
        </div>
    </div>

    <?= $section('bi-person-badge', 'الموظف', 'primary') ?>
    <?= $form->field($model, 'employee')->widget(Select2::class, [
        'data' => $employees,
        'language' => 'ar',
        'options' => ['placeholder' => 'الرجاء اختيار اسم الموظف ...'],
        'pluginOptions' => ['allowClear' => true, 'multiple' => false],
        // جلب بيانات الراتب عند اختيار الموظف؛ نفس الحقول ونفس معادلة المتبقي (الراتب - المقبوض)
        'pluginEvents' => ['select2:select' => 'function () { empSalaryLoad(); }'],
    ]) ?>

    <!-- ملخص الراتب (للقراءة فقط، تعبئه الدالة empSalaryLoad) -->
    <div class="es-summary p-3 mb-3">
        <div class="row g-2">
            <div class="col-6 col-md">
                <?= $form->field($model, 'salary')->textInput(['disabled' => true]) ?>
            </div>
            <div class="col-6 col-md">
                <?= $form->field($model, 'lastPay')->textInput(['disabled' => true]) ?>
            </div>
            <div class="col-6 col-md">
                <?= $form->field($model, 'totalPay')->textInput(['disabled' => true])->label('اجمالي المقبوض بالشهر') ?>
            </div>
            <div class="col-6 col-md">
                <?= $form->field($model, 'remaining')->textInput(['disabled' => true])->label('المتبقي') ?>
            </div>
            <div class="col-12 col-md">
                <?= $form->field($model, 'lastPayDate')->textInput(['disabled' => true]) ?>
            </div>
        </div>
    </div>

    <?= $section('bi-cash-coin', 'السحب', 'success') ?>
    <div class="row g-3">
        <div class="col-md-5">
            <?= $form->field($model, 'at')->widget(DatePicker::class, $datePicker) ?>
        </div>
        <div class="col-md-4 es-amount">
            <?php // checValue دالة عامة معرّفة في مكان آخر من المشروع؛ نتحقق من وجودها كي لا يحدث خطأ JS إن غابت ?>
            <?= $form->field($model, 'value')->textInput([
                'inputmode' => 'decimal',
                'autocomplete' => 'off',
                'placeholder' => '0.000',
                'onfocusout' => 'if (window.checValue) { checValue($(this)); }',
            ]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'currancy')->widget(Select2::class, [
                'data' => $currencies,
                'language' => 'ar',
                'pluginOptions' => ['allowClear' => true, 'multiple' => false],
            ]) ?>
        </div>
    </div>

    <?= $form->field($model, 'why')->textarea(['rows' => 3, 'placeholder' => 'ملاحظات (اختياري)']) ?>

    <div class="d-flex gap-2 mt-3">
        <?= Html::submitButton('<i class="bi bi-check2-circle"></i> ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-lg flex-grow-1']) ?>
        <?= Html::a('<i class="bi bi-arrow-counterclockwise"></i> ' . Yii::t('app', 'Clear'), ['/emp-salary/create'], ['class' => 'btn btn-lg btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs(
    'window.empSalaryUrl = ' . json_encode(Url::to(['emp-salary/get-salary'])) . ';',
    \yii\web\View::POS_HEAD,
    'emp-salary-url'
);
$this->registerJs(<<<'JS'
// جلب الراتب والمقبوض للموظف المختار في السنة/الشهر المحددين.
// يُستدعى عند اختيار الموظف، وأيضاً عند تغيير السنة أو الشهر بعد اختيار الموظف (كانت القيم تبقى قديمة)
window.empSalaryLoad = function () {
    var employee = $('#empsalary-employee').val();
    if (!employee) { return; }
    $.get(window.empSalaryUrl, {
        employee: employee,
        year: $('#empsalary-year').val(),
        month: $('#empsalary-month').val()
    }, function (data) {
        if (data != null) {
            $('#empsalary-salary').val(data.salary);
            $('#empsalary-lastpay').val(data.lastpay);
            $('#empsalary-totalpay').val(data.value);
            $('#empsalary-lastpaydate').val(data.at);
            $('#empsalary-remaining').val(data.salary - data.value);
        }
    });
};
$('#empsalary-year, #empsalary-month').on('change', window.empSalaryLoad);
JS, \yii\web\View::POS_READY, 'emp-salary-load');
