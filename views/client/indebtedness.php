<?php

use app\models\CompanyInfo;
use app\models\Currancy;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/** @var app\models\Client $model */
/* @var $form yii\bootstrap5\ActiveForm */

$errCfg = ['class' => 'invalid-feedback d-block'];

// العملة الافتراضية = عملة الشركة (تُطبَّق فقط إذا لم تُحدَّد عملة)، والقائمة تحوي عملة الشركة فقط كما في الكود القديم
if (empty($model->currency)) {
    $model->currency = CompanyInfo::current()?->currancy;
}
$currencies = ArrayHelper::map(
    Currancy::find()->where('id = (select currancy from company_info)')->select(['id', 'name'])->asArray()->all(),
    'id',
    'name'
);

$kinds = [
    '0' => ['دائنون', 'bi-arrow-down-circle', 'btn-outline-success'],
    '1' => ['مدينون', 'bi-arrow-up-circle', 'btn-outline-danger'],
];
$now = ($model->indebtedness === null || $model->indebtedness === '') ? null : (string) $model->indebtedness;
$kindId = Html::getInputId($model, 'indebtedness');
?>

<div class="histrans">
    <?php $form = ActiveForm::begin(['id' => 'indebtedness-form']); ?>
    <div class="card">
        <div class="card-header fw-semibold"><i class="bi bi-cash-coin me-1"></i> تقرير المديونية</div>
        <div class="card-body">

            <?= $form->field($model, 'currency', ['errorOptions' => $errCfg])->widget(Select2::class, [
                'data' => $currencies,
                'language' => 'ar',
                'bsVersion' => '5.x',
                'options' => ['placeholder' => 'الرجاء اختيار اسم العملة ...'],
                'pluginOptions' => ['allowClear' => true, 'multiple' => false],
            ]) ?>

            <div class="mb-2">
                <label class="form-label"><?= Html::encode($model->getAttributeLabel('indebtedness')) ?></label>
                <div class="btn-group w-100" role="group" aria-label="نوع العرض">
                    <?php foreach ($kinds as $val => $k): ?>
                        <?= Html::radio($model->formName() . '[indebtedness]', $now === (string) $val, [
                            'value' => $val, 'id' => $kindId . '-' . $val, 'class' => 'btn-check', 'autocomplete' => 'off', 'label' => null,
                        ]) ?>
                        <label class="btn <?= \yii\helpers\Html::encode($k[2]) ?>" for="<?= $kindId . '-' . $val ?>"><i class="bi <?= \yii\helpers\Html::encode($k[1]) ?>"></i> <?= Html::encode($k[0]) ?></label>
                    <?php endforeach; ?>
                </div>
                <?= Html::error($model, 'indebtedness', $errCfg) ?>
            </div>
        </div>
        <div class="card-footer d-flex gap-2">
            <?= Html::submitButton('<i class="bi bi-play-fill"></i> ' . Yii::t('app', 'Run'), ['class' => 'btn btn-success btn-lg flex-grow-1', 'id' => 'report-run', 'data-label' => Yii::t('app', 'Run')]) ?>
            <?= Html::a('<i class="bi bi-eraser"></i> ' . Yii::t('app', 'Erase'), Url::toRoute(['index']), ['class' => 'btn btn-lg btn-outline-secondary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs(<<<'JS'
$('#indebtedness-form').on('beforeSubmit', function () {
    $('#report-run').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جارٍ التشغيل...');
    return true;
});
$(window).on('pageshow', function (e) {
    if (e.originalEvent && e.originalEvent.persisted) {
        $('#report-run').prop('disabled', false).html('<i class="bi bi-play-fill"></i> ' + $('#report-run').data('label'));
    }
});
JS, View::POS_READY, 'indebtedness-form-js');
