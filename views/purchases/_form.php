<?php

use app\models\Client;
use app\models\Currancy;
use app\models\PaymentTypes;
use app\models\ShippingType;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var app\models\Purchases $model */

/*
 * التنسيق مطابق لنموذج sales/_form: أقسام بعناوين، أزرار اختيار مجمّعة، صندوق المبالغ، زر حفظ ثابت.
 * ما لم يُمَس عمداً:
 *  - معرّف النموذج purchases-create-form وأسماء كل الحقول ومعرّفاتها وقيمها.
 *  - الحقل paid ما زال يستدعي netTotalsPurchases( $(this) ) عند فقدان التركيز.
 *  - الحقل total للقراءة فقط (الخادم يحسبه من الأصناف).
 */

$isAjax = Yii::$app->request->isAjax;

// داخل النافذة المنبثقة (#modal) يجب أن تُرسم قوائم Select2 داخل النافذة نفسها. وإلا تُفتح خلف النافذة
// (z-index) أو يسرق Bootstrap 5 التركيز منها فلا تُفتح. عند عرض النموذج كصفحة عادية لا نستخدم هذا الخيار.
$dropdown = $isAjax
    ? ['dropdownParent' => new JsExpression("$('#modal')")]
    : [];

// كانت القيمة '0' تُفرض دائماً، فتضيع القيمة المدخلة عند إعادة عرض النموذج بعد خطأ تحقق. الآن: الصفر عند الفراغ فقط.
$zeroIfEmpty = function ($v) {
    return ($v === null || $v === '') ? '0' : $v;
};

// ---------------------------------------------------------------------------
// أدوات مساعدة للعرض (نفس أدوات sales/_form)
// ---------------------------------------------------------------------------

// عنوان قسم
$section = function (string $icon, string $title, string $color = 'primary') {
    return '<div class="d-flex align-items-center gap-2 mb-2 text-' . $color . '">'
        . '<i class="bi ' . $icon . '"></i><span class="fw-bold">' . Html::encode($title) . '</span>'
        . '<hr class="flex-grow-1 my-0 opacity-25"></div>';
};

// أزرار اختيار مجمّعة (radio) بدل القوائم المنسدلة القصيرة: نفس الحقل ونفس القيم
$segmented = function (string $idPrefix, string $color) {
    return function ($index, $label, $name, $checked, $value) use ($idPrefix, $color) {
        $id = $idPrefix . '-' . $index;
        return Html::radio($name, $checked, [
            'value' => $value,
            'id' => $id,
            'class' => 'btn-check',
            'autocomplete' => 'off',
        ]) . Html::label(Html::encode($label), $id, ['class' => 'btn btn-outline-' . $color . ' flex-fill']);
    };
};

$datePlugin = ['autoclose' => true, 'format' => 'yyyy-mm-dd', 'todayHighlight' => true];

// تنسيق خاص بالنموذج
$this->registerCss(<<<'CSS'
.purchases-form .btn-group > .btn { white-space: nowrap; }
.purchases-form .pf-amounts { background: var(--bs-tertiary-bg); border: 1px solid var(--bs-border-color); border-radius: .6rem; }
.purchases-form .pf-total input { font-size: 1.35rem; font-weight: 700; text-align: center; }
.purchases-form .pf-submit { position: sticky; bottom: 0; background: var(--bs-body-bg); padding-top: .75rem; }
.purchases-form .form-label { font-size: .85rem; margin-bottom: .2rem; }
CSS);
?>

<div class="purchases-form">

    <?php
        // العملة الافتراضية من بيانات المؤسسة (company_info.currancy) عند فتح سجل جديد
        if ($model->isNewRecord && !Yii::$app->request->isPost && ($companyCur = \app\models\CompanyInfo::current()?->currancy)) {
            $model->currancy = $companyCur;
        }
        $form = ActiveForm::begin(['id' => 'purchases-create-form']); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger py-2 small']) ?>

    <!-- نوع الفاتورة -->
    <?= $section('bi-receipt', 'نوع الحركـة') ?>
    <?= $form->field($model, 'type', ['options' => ['class' => 'mb-3']])->radioList(
        ['1' => 'فاتورة مشتريات', '2' => 'مسترجع مشتريات', '3' => 'فاتورة معلقة'],
        [
            'class' => 'btn-group w-100 flex-wrap',
            'role' => 'group',
            // الحقل كان قائمة منسدلة؛ الافتراضي الآن «فاتورة مشتريات» (الأكثر استخداماً) كما في نموذج المبيعات
            'value' => $model->type ?: '1',
            'item' => $segmented('purchases-type', 'primary'),
        ]
    )->label(false) ?>

    <!-- المورد والتواريخ -->
    <?= $section('bi-person-vcard', 'المورد والتواريخ') ?>
    <div class="row g-3 mb-2">
        <div class="col-md-7">
            <?= $form->field($model, 'clinet')->widget(\app\widgets\LookupSelect2::class, [
                'lookup' => 'client', 'types' => [1, 2],
                'language' => 'ar',
                'options' => ['placeholder' => 'الرجاء اختيار اسم العميل ...'],
                'pluginOptions' => ['allowClear' => true, 'multiple' => false] + $dropdown,
            ]) ?>
        </div>
        <div class="col-md-5">
            <?= $form->field($model, 'clientBill')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
        </div>
    </div>
    <div class="row g-3 mb-2">
        <div class="col-md-4">
            <?= $form->field($model, 'at')->widget(DatePicker::class, [
                'language' => 'ar',
                'options' => ['placeholder' => 'yyyy-mm-dd', 'autocomplete' => 'off'],
                'pluginOptions' => $datePlugin,
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'dateOfArrival')->widget(DatePicker::class, [
                'language' => 'ar',
                'options' => ['placeholder' => 'yyyy-mm-dd', 'autocomplete' => 'off'],
                'pluginOptions' => $datePlugin,
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'shippingType')->widget(Select2::class, [
                'data' => ArrayHelper::map(ShippingType::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => ['placeholder' => 'الرجاء اختيار اسم طريقة الشحن ...'],
                'pluginOptions' => ['allowClear' => true, 'multiple' => false] + $dropdown,
            ]) ?>
        </div>
    </div>

    <!-- الدفع -->
    <?= $section('bi-wallet2', 'الدفع', 'success') ?>
    <?= $form->field($model, 'payWay', ['options' => ['class' => 'mb-3']])->radioList(
        ['0' => 'نقدا', '1' => 'آجـــل', '2' => 'دفعة على الحساب'],
        [
            'class' => 'btn-group w-100 flex-wrap',
            'role' => 'group',
            'item' => $segmented('purchases-payway', 'success'),
        ]
    )->label(false) ?>

    <div class="row g-3 mb-2">
        <div class="col-md-4">
            <?= $form->field($model, 'payment_type')->widget(Select2::class, [
                'data' => ArrayHelper::map(PaymentTypes::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => [
                    'placeholder' => 'الرجاء اختيار  طريقة الدفع ...',
                    'value' => $model->isNewRecord ? 1 : $model->payment_type,
                ],
                'pluginOptions' => ['allowClear' => true, 'multiple' => false] + $dropdown,
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'currancy')->widget(Select2::class, [
                'data' => ArrayHelper::map(Currancy::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'pluginOptions' => ['allowClear' => true, 'multiple' => false] + $dropdown,
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'total_currancy')->textInput([
                'maxlength' => true,
                'value' => $zeroIfEmpty($model->total_currancy),
                'inputmode' => 'decimal',
                'autocomplete' => 'off',
            ]) ?>
        </div>
    </div>

    <!-- المبالغ -->
    <?= $section('bi-cash-coin', 'المبالغ', 'danger') ?>
    <div class="pf-amounts p-3 mb-3">
        <div class="row g-3">
            <div class="col-md-4 pf-total">
                <?= $form->field($model, 'total', ['options' => ['class' => 'mb-0']])->textInput([
                    'readonly' => true,
                    'placeholder' => '0.000',
                ]) ?>
            </div>
            <div class="col-6 col-md-4">
                <?= $form->field($model, 'totalCost', ['options' => ['class' => 'mb-0']])->textInput([
                    'maxlength' => true,
                    'value' => $zeroIfEmpty($model->totalCost),
                    'placeholder' => '0.000',
                    'inputmode' => 'decimal',
                    'autocomplete' => 'off',
                ]) ?>
            </div>
            <div class="col-6 col-md-4">
                <?= $form->field($model, 'paid', ['options' => ['class' => 'mb-0']])->textInput([
                    'maxlength' => true,
                    'placeholder' => '0.000',
                    'inputmode' => 'decimal',
                    'autocomplete' => 'off',
                    'onfocusout' => 'netTotalsPurchases( $(this) )',   // لا تغيّره: حساب الصافي
                ]) ?>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'notes')->textInput([
        'placeholder' => 'أضف ملاحظة للفاتورة (اختياري)',
    ]) ?>

    <!-- أزرار الحفظ -->
    <div class="pf-submit d-flex gap-2">
        <?= Html::submitButton(
            '<i class="bi bi-check2-circle"></i> ' . Yii::t('app', 'Save'),
            ['class' => 'btn btn-lg flex-grow-1 btn-success', 'id' => 'purchases-save-btn']
        ) ?>
        <?php if ($isAjax) : ?>
            <button type="button" class="btn btn-lg btn-outline-secondary" data-bs-dismiss="modal">
                <?= Html::encode(Yii::t('app', 'Cancel')) ?>
            </button>
        <?php else : ?>
            <?= Html::a(Html::encode(Yii::t('app', 'Cancel')), ['/temp-invoice-purchase/create'], ['class' => 'btn btn-lg btn-outline-secondary']) ?>
        <?php endif; ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
// منع النقر المزدوج على «حفظ» (الخادم يمنع التكرار أيضاً). يُعاد الزر لحالته إن عاد المستخدم لهذه الصفحة من سجل المتصفح.
$this->registerJs(<<<'JS'
(function () {
    var btn = $('#purchases-save-btn');
    var original = btn.html();
    $('#purchases-create-form').on('beforeSubmit', function () {
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> ' + $('<span>').text(btn.text().trim()).html());
        return true;
    });
    $(window).on('pageshow', function () {
        btn.prop('disabled', false).html(original);
    });
})();
JS);
?>
