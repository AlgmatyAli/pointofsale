<?php

use app\models\Client;
use app\models\CompanyInfo;
use app\models\Currancy;
use app\models\PaymentTypes;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var app\models\Sales $model */

$company = CompanyInfo::find()->one();
$identity = Yii::$app->user->identity;
$isClientUser = ($identity->client <> null);
$isAjax = Yii::$app->request->isAjax;
$isPost = Yii::$app->request->isPost;

// داخل النافذة المنبثقة (#modal) يجب أن تُرسم قوائم Select2 داخل النافذة نفسها. وإلا تُفتح خلف النافذة
// (z-index) أو يسرق Bootstrap 5 التركيز منها فلا تُفتح. عند عرض النموذج كصفحة عادية لا نستخدم هذا الخيار.
$dropdown = $isAjax
    ? ['dropdownParent' => new JsExpression("$('#modal')")]
    : [];

// العملاء: المستخدم المرتبط بعملاء يرى عملاءه فقط
$clientsQuery = Client::find()->where(['in', 'type', [0, 2]])->orderBy('id');
if ($isClientUser) {
    $clientsQuery->andWhere(['in', 'id', explode(',', $identity->client)]);
}
$clients = ArrayHelper::map($clientsQuery->asArray()->all(), 'id', 'name');

// ---------------------------------------------------------------------------
// أدوات مساعدة للعرض
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

// مفتاح تبديل (switch) بنفس اسم الحقل. القيمة المخزّنة 1/0 كما في checkbox
$switch = function (string $attribute, bool $checked) use ($model) {
    $name = Html::getInputName($model, $attribute);
    $id = Html::getInputId($model, $attribute);
    return '<div class="form-check form-switch mb-2">'
        . Html::checkbox($name, $checked, [
            'value' => 1,
            'uncheck' => 0,
            'id' => $id,
            'class' => 'form-check-input',
            'role' => 'switch',
        ])
        . '<label class="form-check-label fw-semibold" for="' . Html::encode($id) . '">'
        . Html::encode($model->getAttributeLabel($attribute)) . '</label>'
        . '</div>';
};

// القيمة الافتراضية لمفتاح "تم التسليم": كما في الكود القديم (الموظف حسب invoiceState، والعميل غير محدد)
$showDelivered = $isClientUser || in_array((int) $company->invoiceState, [0, 1], true);
$deliveredChecked = $isPost ? (bool) $model->deleviried : (!$isClientUser && $company->invoiceState == 1);
$wholesaleChecked = $isPost ? (bool) $model->wholesale : false;

$payWayItems = $isClientUser
    ? ['1' => 'آجـــل']
    : ['0' => 'نقدا', '1' => 'آجـــل', '2' => 'دفعة على الحساب'];

// تنسيق خاص بالنموذج
$this->registerCss(<<<'CSS'
.sales-form .btn-group > .btn { white-space: nowrap; }
.sales-form .sf-amounts { background: var(--bs-tertiary-bg); border: 1px solid var(--bs-border-color); border-radius: .6rem; }
.sales-form .sf-total input { font-size: 1.35rem; font-weight: 700; text-align: center; }
.sales-form .sf-submit { position: sticky; bottom: 0; background: var(--bs-body-bg); padding-top: .75rem; }
.sales-form .form-label { font-size: .85rem; margin-bottom: .2rem; }
CSS);
?>

<div class="sales-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger py-2 small']) ?>

    <!-- نوع الفاتورة -->
    <?= $section('bi-receipt', Yii::t('app', 'Invoice Type')) ?>
    <?= $form->field($model, 'type', ['options' => ['class' => 'mb-3']])->radioList(
        ['1' => 'فاتورة نهائية', '3' => 'فاتورة حجز', '4' => 'فاتورة مبدئية'],
        [
            'class' => 'btn-group w-100 flex-wrap',
            'role' => 'group',
            // الحقل كان قائمة منسدلة بلا خيار فارغ فكان أول عنصر هو المختار تلقائياً؛ نحافظ على ذلك
            'value' => $model->type ?: '1',
            'item' => $segmented('sales-type', 'primary'),
        ]
    )->label(false) ?>

    <!-- العميل والتاريخ -->
    <?= $section('bi-person-vcard', 'العميل والتاريخ') ?>
    <div class="row g-3 mb-2">
        <div class="col-md-7">
            <?= $form->field($model, 'clinet')->widget(Select2::class, [
                'data' => $clients,
                'options' => ['placeholder' => 'اختر العميل ...'],
                'pluginOptions' => ['allowClear' => true] + $dropdown,
            ]) ?>
        </div>
        <div class="col-md-5">
            <?= $form->field($model, 'at')->widget(DatePicker::class, [
                'options' => ['placeholder' => 'yyyy-mm-dd', 'autocomplete' => 'off'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                ],
            ])->label('تاريخ الفاتورة') ?>
        </div>
    </div>

    <!-- الدفع -->
    <?= $section('bi-wallet2', 'الدفع', 'success') ?>
    <?= $form->field($model, 'payWay', ['options' => ['class' => 'mb-3']])->radioList(
        $payWayItems,
        [
            'class' => 'btn-group w-100 flex-wrap',
            'role' => 'group',
            // العميل لا يملك سوى "آجل" فيكون محدداً مسبقاً
            'value' => $isClientUser ? '1' : $model->payWay,
            'item' => $segmented('sales-payway', 'success'),
        ]
    )->label(false) ?>

    <div class="row g-3 mb-2">
        <div class="col-md-6">
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
        <div class="col-md-6">
            <?= $form->field($model, 'currancy')->widget(Select2::class, [
                'data' => ArrayHelper::map(Currancy::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'pluginOptions' => ['allowClear' => true, 'multiple' => false] + $dropdown,
            ]) ?>
        </div>
    </div>

    <!-- المبالغ -->
    <?= $section('bi-cash-coin', 'المبالغ', 'danger') ?>
    <div class="sf-amounts p-3 mb-3">
        <div class="row g-3">
            <div class="col-md-4 sf-total">
                <?= $form->field($model, 'total', ['options' => ['class' => 'mb-0']])->textInput([
                    'placeholder' => '0.000',
                    'inputmode' => 'decimal',
                    'autocomplete' => 'off',
                ]) ?>
            </div>
            <div class="col-6 col-md-4">
                <?= $form->field($model, 'disscount', ['options' => ['class' => 'mb-0']])->textInput([
                    'placeholder' => '0.000',
                    'inputmode' => 'decimal',
                    'autocomplete' => 'off',
                    'disabled' => !($identity->makeDiscount == 1),
                ]) ?>
            </div>
            <div class="col-6 col-md-4">
                <?= $form->field($model, 'paid', ['options' => ['class' => 'mb-0']])->textInput([
                    'placeholder' => '0.000',
                    'inputmode' => 'decimal',
                    'autocomplete' => 'off',
                    'disabled' => $isClientUser,
                ]) ?>
            </div>
        </div>
    </div>

    <!-- الاستحقاق والخيارات -->
    <?= $section('bi-sliders', 'الاستحقاق والخيارات', 'secondary') ?>
    <div class="row g-3 mb-2">
        <div class="col-md-5">
            <?= $form->field($model, 'deserving')->widget(DatePicker::class, [
                'options' => ['placeholder' => 'yyyy-mm-dd', 'autocomplete' => 'off'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                ],
            ])->label('تاريخ الاستحقاق') ?>
        </div>
        <div class="col-md-7 d-flex flex-column justify-content-center">
            <?= $showDelivered ? $switch('deleviried', $deliveredChecked) : '' ?>
            <?= $switch('wholesale', $wholesaleChecked) ?>
        </div>
    </div>

    <?= $form->field($model, 'notes')->textInput([
        'maxlength' => true,
        'placeholder' => 'أضف ملاحظة للفاتورة (اختياري)',
    ]) ?>

    <!-- أزرار الحفظ -->
    <div class="sf-submit d-flex gap-2">
        <?= Html::submitButton(
            '<i class="bi bi-check2-circle"></i> ' . ($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update')),
            ['class' => 'btn btn-lg flex-grow-1 ' . ($model->isNewRecord ? 'btn-success' : 'btn-primary')]
        ) ?>
        <?php if ($isAjax): ?>
            <button type="button" class="btn btn-lg btn-outline-secondary" data-bs-dismiss="modal">
                <?= Html::encode(Yii::t('app', 'Cancel')) ?>
            </button>
        <?php endif; ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>