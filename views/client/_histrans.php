<?php

use app\models\CompanyInfo;
use app\models\Currancy;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

/** @var  app\models\Client $model*/

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
$dateCfg = fn() => [
    'language' => 'ar',
    'bsVersion' => '5.x',
    'options' => ['autocomplete' => 'off', 'placeholder' => 'yyyy-mm-dd'],
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'yyyy-mm-dd',
        'todayHighlight' => true,
        'todayBtn' => true,
    ],
];
$catList = [0 => 'زبائن', 1 => 'موردين', 2 => 'كلاهما'];
?>

<div class="histrans report-form">
    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-7 col-md-9">
            <?php $form = ActiveForm::begin(['id' => 'client-histrans-form']); ?>
            <div class="card">
                <div class="card-header fw-semibold"><i class="bi bi-journal-text me-1"></i> كشف حساب</div>
                <div class="card-body">

                    <?= $form->field($model, 'currency', ['errorOptions' => $errCfg])->widget(Select2::class, [
                        'data' => $currencies,
                        'language' => 'ar',
                        'bsVersion' => '5.x',
                        'options' => ['placeholder' => 'الرجاء اختيار اسم العملة ...'],
                        'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                    ]) ?>

                    <?php /* المعرّف type-id مطلوب: القائمة التابعة (DepDrop) تعتمد عليه، لذلك بقيت قائمة منسدلة */ ?>
                    <?= $form->field($model, 'type', ['errorOptions' => $errCfg])
                        ->dropDownList($catList, ['id' => 'type-id', 'prompt' => 'اختيار نوع العرض']) ?>

                    <?= $form->field($model, 'id', ['errorOptions' => $errCfg])->widget(DepDrop::class, [
                        'type' => DepDrop::TYPE_SELECT2,
                        'bsVersion' => '5.x',
                        'options' => ['id' => 'type1-id'],
                        'select2Options' => [
                            'bsVersion' => '5.x',
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'pluginOptions' => [
                            'depends' => ['type-id'],
                            'placeholder' => 'اختر العميل / المورد ...',
                            'url' => Url::to(['/client/type']),
                            'loadingText' => 'جارٍ التحميل ...',
                        ],
                    ])->label($model->getAttributeLabel('id')) ?>

                    <div class="mb-2">
                        <div class="small text-body-secondary mb-1">اختيار سريع للفترة</div>
                        <div class="d-flex flex-wrap gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-range="today">اليوم</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-range="yesterday">أمس</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-range="month">هذا الشهر</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-range="lastmonth">الشهر الماضي</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-range="year">هذه السنة</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'min_date', ['errorOptions' => $errCfg])->widget(DatePicker::class, $dateCfg()) ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'max_date', ['errorOptions' => $errCfg])->widget(DatePicker::class, $dateCfg()) ?>
                        </div>
                    </div>

                    <div class="form-check form-switch mt-1">
                        <?= Html::checkbox($model->formName() . '[allData]', (bool) $model->allData, [
                            'value' => 1,
                            'uncheck' => 0,
                            'id' => 'histrans-allData',
                            'class' => 'form-check-input',
                            'role' => 'switch',
                        ]) ?>
                        <label class="form-check-label" for="histrans-allData"><?= Html::encode($model->getAttributeLabel('allData')) ?></label>
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

$ids = Json::htmlEncode([
    'min' => Html::getInputId($model, 'min_date'),
    'max' => Html::getInputId($model, 'max_date'),
]);
$js = <<<'JS'
(function () {
    var ids = __IDS__, form = $('#client-histrans-form');
    function pad(n) { return (n < 10 ? '0' : '') + n; }
    function fmt(d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()); }
    function setVal(id, v) {
        var $el = $('#' + id).val(v).trigger('change');
        try { $el.kvDatepicker('update'); } catch (e) { try { $el.datepicker('update'); } catch (e2) {} }
    }
    form.on('click', '[data-range]', function () {
        var t = new Date(), a, b;
        switch ($(this).data('range')) {
            case 'today': a = b = t; break;
            case 'yesterday': a = b = new Date(t.getFullYear(), t.getMonth(), t.getDate() - 1); break;
            case 'month': a = new Date(t.getFullYear(), t.getMonth(), 1); b = t; break;
            case 'lastmonth': a = new Date(t.getFullYear(), t.getMonth() - 1, 1); b = new Date(t.getFullYear(), t.getMonth(), 0); break;
            case 'year': a = new Date(t.getFullYear(), 0, 1); b = t; break;
        }
        setVal(ids.min, fmt(a)); setVal(ids.max, fmt(b));
    });
    form.on('beforeSubmit', function () {
        $('#report-run').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جارٍ التشغيل...');
        return true;
    });
    $(window).on('pageshow', function (e) {
        if (e.originalEvent && e.originalEvent.persisted) {
            $('#report-run').prop('disabled', false).html('<i class="bi bi-play-fill"></i> ' + $('#report-run').data('label'));
        }
    });
})();
JS;
$this->registerJs(str_replace('__IDS__', $ids, $js), View::POS_READY, 'client-histrans-form-js');
