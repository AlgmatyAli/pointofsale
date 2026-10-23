<?php

use app\models\CompanyInfo;
use app\models\Currancy;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\ReceiptSearch */
/* @var $form yii\bootstrap5\ActiveForm */

$errCfg = ['class' => 'invalid-feedback d-block'];

// العملة الافتراضية = عملة الشركة (تُطبَّق فقط إذا لم تُحدَّد عملة)، والقائمة تحوي عملة الشركة فقط كما في الكود القديم
if (empty($model->currency)) {
    $model->currency = CompanyInfo::find()->one()?->currancy;
}
$currencies = ArrayHelper::map(
    Currancy::find()->where('id = (select currancy from company_info)')->select(['id', 'name'])->asArray()->all(),
    'id',
    'name'
);
$typeNow = ($model->type === null || $model->type === '') ? null : (string) $model->type;
$typeId = Html::getInputId($model, 'type');
$types = [
    '0' => ['زبائـن', 'bi-people', 'btn-outline-info'],
    '1' => ['موردين', 'bi-truck', 'btn-outline-warning'],
];
?>

<div class="credits">
    <div class="row justify-content-center">
        <div class="col-xl-4 col-lg-6 col-md-8">
            <?php $form = ActiveForm::begin(['id' => 'credits-form']); ?>
            <div class="card">
                <div class="card-header fw-semibold"><i class="bi bi-cash-coin me-1"></i> عرض الأرصدة</div>
                <div class="card-body">

                    <?= $form->field($model, 'currency', ['errorOptions' => $errCfg])->widget(Select2::class, [
                        'data' => $currencies,
                        'language' => 'ar',
                        'bsVersion' => '5.x',
                        'options' => ['placeholder' => 'الرجاء اختيار اسم العملة ...'],
                        'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                    ]) ?>

                    <div class="mb-2">
                        <label class="form-label"><?= Html::encode($model->getAttributeLabel('type')) ?></label>
                        <div class="btn-group w-100" role="group" aria-label="نوع العرض">
                            <?php foreach ($types as $val => $t): ?>
                                <?= Html::radio($model->formName() . '[type]', $typeNow === (string) $val, [
                                    'value' => $val, 'id' => $typeId . '-' . $val, 'class' => 'btn-check', 'autocomplete' => 'off', 'label' => null,
                                ]) ?>
                                <label class="btn <?= \yii\helpers\Html::encode($t[2]) ?>" for="<?= $typeId . '-' . $val ?>"><i class="bi <?= \yii\helpers\Html::encode($t[1]) ?>"></i> <?= Html::encode($t[0]) ?></label>
                            <?php endforeach; ?>
                        </div>
                        <?= Html::error($model, 'type', $errCfg) ?>
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <?= Html::submitButton('<i class="bi bi-play-fill"></i> ' . Yii::t('app', 'Run'), ['class' => 'btn btn-success btn-lg flex-grow-1', 'id' => 'report-run', 'data-label' => Yii::t('app', 'Run')]) ?>
                    <?= Html::a('<i class="bi bi-eraser"></i> ' . Yii::t('app', 'Erase'), Url::toRoute(['index']), ['class' => 'btn btn-lg btn-outline-secondary']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php
$this->registerJs(<<<'JS'
$('#credits-form').on('beforeSubmit', function () {
    $('#report-run').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جارٍ التشغيل...');
    return true;
});
$(window).on('pageshow', function (e) {
    if (e.originalEvent && e.originalEvent.persisted) {
        $('#report-run').prop('disabled', false).html('<i class="bi bi-play-fill"></i> ' + $('#report-run').data('label'));
    }
});
JS, yii\web\View::POS_READY, 'credits-form-js');
