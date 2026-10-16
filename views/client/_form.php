<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Client */
/* @var $form yii\bootstrap5\ActiveForm */

$types = [
    '0' => ['label' => 'زبون', 'icon' => 'bi-person', 'btn' => 'btn-outline-info'],
    '1' => ['label' => 'مورد', 'icon' => 'bi-truck', 'btn' => 'btn-outline-warning'],
    '2' => ['label' => 'كلاهما', 'icon' => 'bi-arrow-left-right', 'btn' => 'btn-outline-primary'],
];
$typeNow = ($model->type === null || $model->type === '') ? null : (string) $model->type;
$typeId = Html::getInputId($model, 'type');

$errCfg = ['class' => 'invalid-feedback d-block'];
$group = fn(string $icon) => '<div class="input-group"><span class="input-group-text"><i class="bi ' . $icon . '"></i></span>{input}</div>';

$this->registerCss(<<<'CSS'
.client-form .save-bar { position: sticky; bottom: 0; z-index: 5; background: var(--bs-body-bg); border-top: 1px solid var(--bs-border-color); padding: .6rem 0; margin-top: 1rem; }
.client-form .type-seg .btn { flex: 1 1 0; }
CSS);
?>
<div class="client-form">

    <?php $form = ActiveForm::begin(['id' => 'client-form']); ?>

    <div class="row g-3">

        <!-- بيانات التواصل -->
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header fw-semibold"><i class="bi bi-person-lines-fill me-1"></i> بيانات العميل</div>
                <div class="card-body">
                    <?= $form->field($model, 'name', ['inputTemplate' => $group('bi-person'), 'errorOptions' => $errCfg])
                        ->textInput(['maxlength' => true, 'autofocus' => true, 'autocomplete' => 'off']) ?>

                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'mobile', ['inputTemplate' => $group('bi-phone'), 'errorOptions' => $errCfg])
                                ->textInput(['maxlength' => true, 'inputmode' => 'tel', 'dir' => 'ltr']) ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'phone', ['inputTemplate' => $group('bi-telephone'), 'errorOptions' => $errCfg])
                                ->textInput(['maxlength' => true, 'inputmode' => 'tel', 'dir' => 'ltr']) ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'email', ['inputTemplate' => $group('bi-envelope'), 'errorOptions' => $errCfg])
                        ->input('email', ['maxlength' => true, 'dir' => 'ltr', 'autocomplete' => 'off']) ?>

                    <?= $form->field($model, 'address', ['inputTemplate' => $group('bi-geo-alt'), 'errorOptions' => $errCfg])
                        ->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>

        <!-- النوع والأرصدة -->
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header fw-semibold"><i class="bi bi-wallet2 me-1"></i> التصنيف والحساب</div>
                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label"><?= Html::encode($model->getAttributeLabel('type')) ?></label>
                        <div class="btn-group w-100 type-seg" role="group" aria-label="نوع العميل">
                            <?php foreach ($types as $val => $t): ?>
                                <?= Html::radio($model->formName() . '[type]', $typeNow === (string) $val, [
                                    'value' => $val,
                                    'id' => $typeId . '-' . $val,
                                    'class' => 'btn-check',
                                    'autocomplete' => 'off',
                                    'label' => null,
                                ]) ?>
                                <label class="btn <?= $t['btn'] ?>" for="<?= $typeId . '-' . $val ?>">
                                    <i class="bi <?= $t['icon'] ?>"></i> <?= Html::encode($t['label']) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <?= Html::error($model, 'type', $errCfg) ?>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'balance', ['inputTemplate' => $group('bi-cash-stack'), 'errorOptions' => $errCfg])
                                ->textInput(['maxlength' => true, 'inputmode' => 'decimal', 'dir' => 'ltr']) ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'debt', ['inputTemplate' => $group('bi-receipt'), 'errorOptions' => $errCfg])
                                ->textInput(['maxlength' => true, 'inputmode' => 'decimal', 'dir' => 'ltr']) ?>
                        </div>
                    </div>

                    <div class="form-check form-switch fs-6 mt-2">
                        <?= Html::checkbox($model->formName() . '[post_paid]', (bool) $model->post_paid, [
                            'value' => 1,
                            'uncheck' => 0,
                            'id' => 'client-post_paid',
                            'class' => 'form-check-input',
                            'role' => 'switch',
                        ]) ?>
                        <label class="form-check-label" for="client-post_paid"><?= Html::encode($model->getAttributeLabel('post_paid')) ?></label>
                        <div class="form-text">عند التفعيل يُسمح للعميل بالشراء بالآجل.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- شريط الحفظ -->
    <div class="save-bar d-flex gap-2">
        <?= Html::submitButton('<i class="bi bi-save"></i> ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-lg flex-grow-1', 'id' => 'client-save']) ?>
        <?= Html::a('<i class="bi bi-x-lg"></i> ' . Yii::t('app', 'Cancel'), Yii::$app->request->referrer ?: ['index'], ['class' => 'btn btn-lg btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php
// منع الضغط المزدوج بعد نجاح التحقق من جهة العميل فقط
$this->registerJs(<<<'JS'
$('#client-form').on('beforeSubmit', function () {
    $('#client-save').prop('disabled', true);
    return true;
});
JS, View::POS_READY, 'client-form-js');
