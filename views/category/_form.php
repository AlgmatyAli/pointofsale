<?php

use kartik\file\FileInput;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Category $model */

$identity = Yii::$app->user->identity;
$seeCost = ($identity->seeCostPrice == 1);

// القيم الافتراضية تُطبَّق فقط عند خلو الحقل. الكود القديم كان يفرض 'قطعة' على الوحدة دائماً (فيمحو الوحدة المحفوظة عند التعديل)
if ($model->unit === null || $model->unit === '') {
    $model->unit = 'قطعة';
}
if (empty($model->box)) {
    $model->box = 1;
}
if (empty($model->minimum)) {
    $model->minimum = 3;
}

// مفاتيح التبديل: "ending" مفعّل افتراضياً للصنف الجديد (هذه كانت نية الكود القديم)، وغير ذلك تتبع قيمة الموديل
$isPost = Yii::$app->request->isPost;
$switches = [
    'ending' => ($model->isNewRecord && !$isPost) ? true : (bool) $model->ending,
    'moreRequest' => (bool) $model->moreRequest,
    'qShow' => (bool) $model->qShow,
];
$switch = function (string $attribute, bool $checked) use ($model) {
    $id = Html::getInputId($model, $attribute);
    return '<div class="form-check form-switch mb-2">'
        . Html::checkbox(Html::getInputName($model, $attribute), $checked, [
            'value' => 1, 'uncheck' => 0, 'id' => $id, 'class' => 'form-check-input', 'role' => 'switch',
        ])
        . '<label class="form-check-label fw-semibold" for="' . Html::encode($id) . '">'
        . Html::encode($model->getAttributeLabel($attribute)) . '</label></div>';
};

$this->registerCss(<<<'CSS'
.category-form .form-label { font-size: .85rem; margin-bottom: .2rem; }
.category-form .cf-save { position: sticky; bottom: 0; z-index: 5; background: var(--bs-body-bg); border-top: 1px solid var(--bs-border-color); padding: .75rem 0; margin-top: 1rem; }
CSS);
?>

<div class="category-form">

    <!-- البحث في الأصناف الموجودة (لتفادي التكرار) -->
    <div class="mb-3">
        <?= Html::button(
            '<i class="bi bi-search"></i> ' . Yii::t('app', 'Search'),
            ['value' => Url::to(['data-table']), 'class' => 'btn btn-info popup']
        ) ?>
    </div>

    <?php $form = ActiveForm::begin(['id' => 'category-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger py-2 small']) ?>

    <div class="row g-3">

        <!-- بيانات الصنف -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header fw-bold"><i class="bi bi-tag text-primary"></i> بيانات الصنف</div>
                <div class="card-body">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
                    <?= $form->field($model, 'class')->textInput(['maxlength' => true]) ?>
                    <div class="row g-2">
                        <div class="col-6"><?= $form->field($model, 'serialNo')->textInput(['autocomplete' => 'off']) ?></div>
                        <div class="col-6"><?= $form->field($model, 'commCode')->textInput(['autocomplete' => 'off']) ?></div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6"><?= $form->field($model, 'unit')->textInput(['maxlength' => true]) ?></div>
                        <div class="col-6"><?= $form->field($model, 'box')->textInput(['inputmode' => 'decimal']) ?></div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6"><?= $form->field($model, 'place')->textInput(['maxlength' => true]) ?></div>
                        <div class="col-6"><?= $form->field($model, 'weight')->textInput(['maxlength' => true, 'inputmode' => 'decimal']) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الأسعار والمخزون -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header fw-bold"><i class="bi bi-cash-coin text-success"></i> الأسعار والمخزون</div>
                <div class="card-body">
                    <div class="row g-2">
                        <?php
                        // سعر التكلفة: لمن لا يملك seeCostPrice لا يُعرض عند تعديل صنف قائم (تبقى قيمته المحفوظة في حقل مخفي).
                        // عند إنشاء صنف جديد يبقى الحقل ظاهراً ليتمكن من إدخاله، إذ لا قيمة سابقة تُكشف.
                        $costVisible = $seeCost || $model->isNewRecord;
                        ?>
                        <?php if ($costVisible): ?>
                            <div class="col-6"><?= $form->field($model, 'cost')->textInput(['inputmode' => 'decimal', 'autocomplete' => 'off']) ?></div>
                        <?php else: ?>
                            <?= $form->field($model, 'cost')->hiddenInput()->label(false) ?>
                        <?php endif; ?>
                        <div class="<?= $costVisible ? 'col-6' : 'col-12' ?>"><?= $form->field($model, 'price')->textInput(['inputmode' => 'decimal', 'autocomplete' => 'off']) ?></div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6"><?= $form->field($model, 'quantity')->textInput(['inputmode' => 'decimal']) ?></div>
                        <div class="col-6"><?= $form->field($model, 'minimum')->textInput(['inputmode' => 'decimal']) ?></div>
                    </div>
                    <?php // بحث Ajax في جدول الشركات (lookup/company) بدل تحميل القائمة كاملة ?>
                    <?= $form->field($model, 'companyId')->widget(\app\widgets\LookupSelect2::class, [
                        'lookup' => 'company',
                        'options' => ['placeholder' => Yii::t('app', 'اختر الشركة')],
                        // داخل النافذة المنبثقة (#modal، من شاشتي المبيعات والمشتريات) تُرسم القائمة داخل النافذة،
                        // وإلا يسرق Bootstrap 5 التركيز من خانة البحث فلا يمكن الكتابة فيها
                        'pluginOptions' => ['allowClear' => true] + (Yii::$app->request->isAjax
                            ? ['dropdownParent' => new \yii\web\JsExpression("$('#modal')")]
                            : []),
                    ])->hint(Html::a(Yii::t('app', 'إدارة الشركات'), ['/item-company/index'], ['target' => '_blank'])) ?>
                    <?= $form->field($model, 'country')->textInput() ?>
                </div>
            </div>
        </div>

        <!-- الصورة والخيارات -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header fw-bold"><i class="bi bi-image text-info"></i> الصورة</div>
                <div class="card-body">
                    <?php
                    $fileOptions = ['options' => ['accept' => 'image/*']];
                    if (!empty($model->path)) {
                        // كانت المعاينة تُمرَّر مصفوفة داخل مصفوفة فلا تظهر الصورة الحالية
                        $fileOptions['pluginOptions'] = [
                            'initialPreview' => [Html::img($model->path, ['class' => 'file-preview-image img-fluid'])],
                            'overwriteInitial' => false,
                        ];
                    }
                    echo $form->field($model, 'file')->widget(FileInput::class, $fileOptions)->label(false);
                    ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-bold"><i class="bi bi-sliders text-secondary"></i> خيارات</div>
                <div class="card-body pb-2">
                    <?php foreach ($switches as $attribute => $checked): ?>
                        <?= $switch($attribute, $checked) ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="cf-save d-flex gap-2">
        <?= Html::submitButton('<i class="bi bi-check2-circle"></i> ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-lg flex-grow-1']) ?>
        <?= Html::a('<i class="bi bi-x-circle"></i> ' . Yii::t('app', 'Cancel'), Yii::$app->request->referrer ?: ['index'], ['class' => 'btn btn-lg btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
