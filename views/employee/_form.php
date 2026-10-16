<?php

use kartik\date\DatePicker;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/** @var yii\web\View $this */
/** @var app\models\Employee $model */

// حالة الموظف: كانت قائمة منسدلة بخيار فارغ. للسجل الجديد نحدد "يعمل" مسبقاً لتوفير نقرة
$stateValue = ($model->state === null || $model->state === '') ? '0' : (string) $model->state;

$this->registerCss(<<<'CSS'
.employee-form .form-label { font-size: .85rem; margin-bottom: .2rem; }
.employee-form .btn-group > .btn { white-space: nowrap; }
CSS);
?>

<div class="employee-form">

    <?php $form = ActiveForm::begin(['id' => 'employee-form']); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger py-2 small']) ?>

    <!-- رسالة خطأ الإرسال عبر AJAX (بدل alert) -->
    <div id="employee-form-msg" class="alert alert-danger py-2 small d-none" role="alert">
        <i class="bi bi-exclamation-triangle"></i> <span></span>
    </div>

    <div class="d-flex align-items-center gap-2 mb-2 text-primary">
        <i class="bi bi-person-badge"></i><span class="fw-bold">بيانات الموظف</span>
        <hr class="flex-grow-1 my-0 opacity-25">
    </div>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>

    <div class="d-flex align-items-center gap-2 mb-2 mt-3 text-success">
        <i class="bi bi-cash-coin"></i><span class="fw-bold">الراتب والعمل</span>
        <hr class="flex-grow-1 my-0 opacity-25">
    </div>

    <div class="row g-3">
        <div class="col-6">
            <?= $form->field($model, 'salary')->textInput(['maxlength' => true, 'inputmode' => 'decimal', 'autocomplete' => 'off']) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'dayOfWork')->textInput(['maxlength' => true, 'inputmode' => 'numeric', 'autocomplete' => 'off']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'startWork')->widget(DatePicker::class, [
                'language' => 'ar',
                'options' => ['placeholder' => 'yyyy-mm-dd', 'autocomplete' => 'off'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                    'todayBtn' => true,
                ],
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'state')->radioList(
                ['0' => 'يعمل', '1' => 'موقوف'],
                [
                    'class' => 'btn-group w-100',
                    'role' => 'group',
                    'value' => $stateValue,
                    'item' => function ($index, $label, $name, $checked, $value) {
                        $id = 'employee-state-' . $index;
                        $color = $value === '0' ? 'success' : 'secondary';
                        return Html::radio($name, $checked, ['value' => $value, 'id' => $id, 'class' => 'btn-check', 'autocomplete' => 'off'])
                            . Html::label(Html::encode($label), $id, ['class' => 'btn btn-outline-' . $color . ' flex-fill']);
                    },
                ]
            ) ?>
        </div>
    </div>

    <?= $form->field($model, 'notes')->textarea(['rows' => 3, 'maxlength' => true, 'placeholder' => 'ملاحظات (اختياري)']) ?>

    <div class="mt-3">
        <?= Html::submitButton(
            '<i class="bi bi-check2-circle"></i> ' . Yii::t('app', 'Save'),
            ['class' => 'btn btn-success btn-lg w-100', 'id' => 'employee-submit']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php
    // الإرسال عبر AJAX كما في الكود القديم، لكن مقصور على هذا النموذج (كان $('form') يلتقط كل النماذج في الصفحة)
    // beforeSubmit لا يُستدعى أصلاً إلا بعد نجاح التحقق من جهة العميل، فلا حاجة لفحص .has-error (صنف Bootstrap 3)
    $this->registerJs(<<<'JS'
(function () {
    var $form = $('#employee-form');
    var $btn = $('#employee-submit');
    var btnHtml = $btn.html();
    var $msg = $('#employee-form-msg');

    function fail(text) {
        $msg.removeClass('d-none').find('span').text(text);
    }

    $form.on('beforeSubmit', function () {
        $msg.addClass('d-none');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جارٍ الحفظ ...');

        $.ajax({
            url: $form.attr('action'),
            type: 'post',
            data: $form.serialize()
        }).done(function (result) {
            if (result === 'success') {
                $.pjax.reload({container: '#grid-id'});
                $form.trigger('reset');
                // إن كان النموذج داخل نافذة منبثقة مفتوحة فأغلقها
                var modalEl = $form.closest('.modal.show')[0];
                if (modalEl && window.bootstrap) {
                    var inst = bootstrap.Modal.getInstance(modalEl);
                    if (inst) { inst.hide(); }
                }
            } else {
                fail('تعذّر الحفظ. راجع الحقول وأعد المحاولة.');
            }
        }).fail(function () {
            fail('تعذّر الاتصال بالخادم. أعد المحاولة.');
        }).always(function () {
            $btn.prop('disabled', false).html(btnHtml);
        });

        return false;
    });
})();
JS, View::POS_READY, 'employee-form-js');
    ?>
</div>
