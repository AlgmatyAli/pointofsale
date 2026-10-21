<?php

use app\models\Currancy;
use kartik\file\FileInput;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\CompanyInfo $model */
/** @var yii\bootstrap5\ActiveForm $form */

// اسم الحقل يحوي الحرف (ـ) U+0640 بين criteria و value: كتبته بالرمز \u{0640} ليبقى الاسم مطابقاً تماماً
$criteriaAttr = "criteria\u{0640}value";

// الشعار الحالي (إن وُجد): مسار نسبي يُحوَّل إلى مسار كامل بحسب الموقع
$logo = (string) $model->path;
if ($logo !== '' && !preg_match('~^(https?:)?//|^/~', $logo)) {
    $logo = Yii::getAlias('@web/') . ltrim($logo, '/');
}

$fileInputOptions = [
    'options' => ['accept' => 'image/*'],
    'bsVersion' => '5.x',
    'pluginOptions' => [
        'showUpload' => false,
        'showRemove' => false,
        'browseLabel' => 'اختيار صورة',
        'browseIcon' => '<i class="bi bi-image"></i> ',
        'previewFileType' => 'image',
    ],
];
if ($logo !== '') {
    $fileInputOptions['pluginOptions']['initialPreview'] = [Html::img($logo, ['class' => 'file-preview-image'])];
    $fileInputOptions['pluginOptions']['overwriteInitial'] = true;
}

// مفتاح تشغيل/إيقاف (يرسل 0 عند الإيقاف و1 عند التشغيل كما كان مع SwitchInput)
$switch = function ($model, string $attribute, string $hint = '') {
    $id = Html::getInputId($model, $attribute);
    $input = Html::activeCheckbox($model, $attribute, [
        'class' => 'form-check-input',
        'role' => 'switch',
        'label' => null,
        'uncheck' => 0,
        'value' => 1,
    ]);
    $label = Html::label(Html::encode($model->getAttributeLabel($attribute)), $id, ['class' => 'form-check-label fw-semibold']);
    $hintHtml = $hint !== '' ? '<div class="form-text mt-0">' . Html::encode($hint) . '</div>' : '';
    return '<div class="list-group-item"><div class="form-check form-switch mb-0">' . $input . $label . $hintHtml . '</div></div>';
};
?>

<div class="company-info-form">
    <?php $form = ActiveForm::begin([
        'id' => 'company-info-form',
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>

    <div class="row g-3">

        <!-- بيانات الشركة -->
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header fw-semibold"><i class="bi bi-building"></i> بيانات الشركة</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"><?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?></div>
                        <div class="col-md-6"><?= $form->field($model, 'work')->textInput(['maxlength' => true]) ?></div>
                    </div>
                    <?= $form->field($model, 'address')->textarea(['rows' => 3]) ?>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header fw-semibold"><i class="bi bi-telephone"></i> بيانات الاتصال</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4"><?= $form->field($model, 'phone1')->textInput(['maxlength' => true, 'inputmode' => 'tel', 'dir' => 'ltr']) ?></div>
                        <div class="col-md-4"><?= $form->field($model, 'phone2')->textInput(['maxlength' => true, 'inputmode' => 'tel', 'dir' => 'ltr']) ?></div>
                        <div class="col-md-4"><?= $form->field($model, 'phone3')->textInput(['maxlength' => true, 'inputmode' => 'tel', 'dir' => 'ltr']) ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><?= $form->field($model, 'fax')->textInput(['maxlength' => true, 'dir' => 'ltr']) ?></div>
                        <div class="col-md-6"><?= $form->field($model, 'email')->textInput(['maxlength' => true, 'type' => 'email', 'dir' => 'ltr']) ?></div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header fw-semibold"><i class="bi bi-sliders"></i> إعدادات عامة</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'currancy')->widget(Select2::class, [
                                'data' => ArrayHelper::map(Currancy::find()->all(), 'id', 'name'),
                                'language' => 'ar',
                                'theme' => Select2::THEME_BOOTSTRAP,
                                'options' => ['placeholder' => 'اختر العملة ...'],
                                'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'rate')->textInput(['maxlength' => true, 'inputmode' => 'decimal'])->label('نسبة  الزيادة') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, $criteriaAttr)->textInput(['maxlength' => true])->label('قيمة البحث') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'skin')->dropDownList([
                                'skin-blue' => 'skin-blue',
                                'skin-blue-light' => 'skin-blue-light',
                                'skin-yellow' => 'skin-yellow',
                                'skin-yellow-light' => 'skin-yellow-light',
                                'skin-green' => 'skin-green',
                                'skin-green-light' => 'skin-green-light',
                                'skin-red' => 'skin-red',
                                'skin-red-light' => 'skin-red-light',
                            ], ['prompt' => 'اختيار لون الخلفية']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header fw-semibold"><i class="bi bi-file-text"></i> الشروط والأحكام (تظهر أسفل الفواتير)</div>
                <div class="card-body">
                    <?= $form->field($model, 'terms')->textarea(['rows' => 6])->label(false) ?>
                </div>
            </div>
        </div>

        <!-- الشعار + المفاتيح -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header fw-semibold"><i class="bi bi-image"></i> شعار الشركة</div>
                <div class="card-body">
                    <?= $form->field($model, 'file')->widget(FileInput::class, $fileInputOptions)->label(false) ?>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header fw-semibold"><i class="bi bi-toggles"></i> خيارات التشغيل</div>
                <div class="list-group list-group-flush">
                    <?= $switch($model, 'searchById') ?>
                    <?= $switch($model, 'repeatCategory') ?>
                    <?= $switch($model, 'payWayCash') ?>
                    <?= $switch($model, 'invoiceState') ?>
                    <?= $switch($model, 'waitQnty') ?>
                    <?= $switch($model, 'zeroQnty') ?>
                    <?= $switch($model, 'avgCost', 'عند إيقافه تُسجَّل تكلفة الصنف بآخر سعر شراء بدل المتوسط المرجّح (عند إنشاء فاتورة المشتريات وتعديلها)') ?>
                    <?= $switch($model, 'editSalePriceOnPurchaseUpdate', 'يحدد الحالة الابتدائية لخيار «تعديل سعر البيع» في شاشة تعديل فاتورة المشتريات') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- شريط الحفظ: يبقى ظاهراً أسفل الشاشة على الهاتف -->
    <div class="position-sticky bottom-0 bg-body border-top py-2 mt-2 d-flex gap-2" style="z-index:5">
        <?= Html::submitButton(
            '<i class="bi bi-check-lg"></i> ' . ($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update')),
            ['class' => 'btn btn-success flex-fill flex-md-grow-0 px-md-5']
        ) ?>
        <?= Html::a(
            '<i class="bi bi-x-lg"></i> ' . Yii::t('app', 'Cancel'),
            ['/company-info/index'],
            ['class' => 'btn btn-outline-secondary flex-fill flex-md-grow-0']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
