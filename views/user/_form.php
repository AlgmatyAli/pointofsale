<?php

use app\models\AuthItem;
use app\models\Branches;
use app\models\Client;
use kartik\file\FileInput;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\User */

// الصلاحيات الإضافية: [الحقل، الوصف]
$flags = [
    'seeCostPrice' => 'رؤية سعر التكلفة وهامش الربح',
    'editSalePrice' => 'تعديل سعر البيع في الفاتورة',
    'makeDiscount' => 'منح خصم على الفاتورة',
    'printPurtchaseInvoice' => 'طباعة فواتير المشتريات',
    'seeOtherBranchQ' => 'رؤية كميات الفروع الأخرى',
];

$switch = function (string $attribute, string $hint) use ($model) {
    $id = Html::getInputId($model, $attribute);
    return '<div class="form-check form-switch mb-2">'
        . Html::activeCheckbox($model, $attribute, ['class' => 'form-check-input', 'role' => 'switch', 'label' => null, 'id' => $id])
        . '<label class="form-check-label fw-semibold" for="' . Html::encode($id) . '">' . Html::encode($model->getAttributeLabel($attribute)) . '</label>'
        . '<div class="form-text mt-0">' . Html::encode($hint) . '</div></div>';
};

// حالة المستخدم: القيمتان المخزّنتان active / disabled كما كانتا
$statusValue = $model->isActive ?: 'active';

$this->registerCss(<<<'CSS'
.user-form .form-label { font-size: .85rem; margin-bottom: .2rem; }
.user-form .uf-save { position: sticky; bottom: 0; z-index: 5; background: var(--bs-body-bg); border-top: 1px solid var(--bs-border-color); padding: .75rem 0; margin-top: 1rem; }
.user-form .btn-group > .btn { white-space: nowrap; }
CSS);
?>
<div class="user-form">
    <?php $form = ActiveForm::begin(['id' => 'user-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger py-2 small']) ?>

    <div class="row g-3">

        <!-- بيانات الحساب -->
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header fw-bold"><i class="bi bi-person-badge text-primary"></i> بيانات الحساب</div>
                <div class="card-body">

                    <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>

            <?= $form->field($model, 'password', [
                'template' => '{label}<div class="input-group"><span class="input-group-text"><i class="bi bi-key"></i></span>{input}'
                    . '<button type="button" class="btn btn-outline-secondary" id="user-pw-toggle" tabindex="-1" aria-label="إظهار / إخفاء"><i class="bi bi-eye"></i></button></div>{error}',
            ])->passwordInput(['maxlength' => true, 'autocomplete' => 'new-password']) ?>

                    <div class="row g-2">
                        <div class="col-sm-6"><?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'inputmode' => 'tel']) ?></div>
                        <div class="col-sm-6"><?= $form->field($model, 'email')->textInput(['maxlength' => true, 'inputmode' => 'email']) ?></div>
                    </div>

                    <?= $form->field($model, 'isActive')->radioList(
                        ['active' => 'نشط', 'disabled' => 'معطّل'],
                        [
                            'class' => 'btn-group w-100',
                            'role' => 'group',
                            'value' => $statusValue,
                            'item' => function ($index, $label, $name, $checked, $value) {
                                $id = 'user-status-' . $index;
                                $color = $value === 'active' ? 'success' : 'secondary';
                                return Html::radio($name, $checked, ['value' => $value, 'id' => $id, 'class' => 'btn-check', 'autocomplete' => 'off'])
                                    . Html::label(Html::encode($label), $id, ['class' => 'btn btn-outline-' . $color . ' flex-fill']);
                            },
                        ]
                    ) ?>

                    <?= $form->field($model, 'branch')->widget(Select2::class, [
                        'data' => ArrayHelper::map(Branches::find()->all(), 'id', 'name'),
                        'language' => 'ar',
                        'options' => ['placeholder' => 'الرجاء اختيار اسم الفرع ...'],
                        'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                    ]) ?>

                    <?= $form->field($model, 'permission')->widget(Select2::class, [
                        'data' => ArrayHelper::map(AuthItem::find()->where(['!=', 'type', '2'])->asArray()->all(), 'type', 'name'),
                        'options' => ['placeholder' => Yii::t('app', 'Choose User Type')],
                        'pluginOptions' => ['allowClear' => true],
                    ]) ?>

                    <?= $form->field($model, 'client')->widget(\app\widgets\LookupSelect2::class, [
                        'lookup' => 'client',
                        'options' => ['placeholder' => 'اختر العملاء (اختياري) ...'],
                        'pluginOptions' => ['allowClear' => true, 'multiple' => true],
                    ])->hint('اختياري: يقتصر المستخدم على العملاء المحددين.') ?>

                </div>
            </div>
        </div>

        <!-- الصورة -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100">
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
        </div>

        <!-- الصلاحيات الإضافية -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-primary">
                <div class="card-header fw-bold bg-primary-subtle"><i class="bi bi-shield-check text-primary"></i> صلاحيات اضافية</div>
                <div class="card-body">
                    <?php foreach ($flags as $attribute => $hint): ?>
                        <?= $switch($attribute, $hint) ?>
                    <?php endforeach; ?>

                    <hr class="opacity-25">
                    <div class="text-body-secondary small mb-2">الحدود القصوى</div>
                    <?= $form->field($model, 'maxDiscount')->textInput(['maxlength' => true, 'inputmode' => 'decimal']) ?>
                    <?= $form->field($model, 'maxExpenses')->textInput(['maxlength' => true, 'inputmode' => 'decimal']) ?>
                    <?= $form->field($model, 'maxReceipt')->textInput(['maxlength' => true, 'inputmode' => 'decimal']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- شريط الحفظ -->
    <div class="uf-save d-flex gap-2">
        <?= Html::submitButton(
            $model->isNewRecord
                ? '<i class="bi bi-save"></i> ' . Yii::t('app', 'Create')
                : '<i class="bi bi-pencil-square"></i> ' . Yii::t('app', 'Update'),
            ['class' => 'btn btn-lg flex-grow-1 ' . ($model->isNewRecord ? 'btn-success' : 'btn-primary')]
        ) ?>
        <?= Html::a('<i class="bi bi-eraser"></i> مسح', Url::toRoute(['create']), ['class' => 'btn btn-lg btn-outline-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php

$this->registerJs(<<<'JS'
$('#user-pw-toggle').on('click', function () {
    var $i = $(this).closest('.input-group').find('input');
    var show = $i.attr('type') === 'password';
    $i.attr('type', show ? 'text' : 'password');
    $(this).find('i').toggleClass('bi-eye', !show).toggleClass('bi-eye-slash', show);
});
JS, \yii\web\View::POS_READY, 'user-pw-toggle');
