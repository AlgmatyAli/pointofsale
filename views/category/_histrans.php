<?php

use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/** @var yii\web\View $this */
/** @var app\models\ReceiptSearch $model */
/** @var yii\bootstrap5\ActiveForm $form */

$errCfg = ['class' => 'invalid-feedback d-block'];
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

// قالب نتائج Select2: الاسم في سطر ثم شارات (مع تهريب HTML لأن escapeMarkup معطّل)
$formatJs = <<<'JS'
var esc = function (s) { return $('<div>').text(s == null ? '' : String(s)).html(); };
var hsChip = function (label, val) {
    return (val === undefined || val === null || val === '') ? '' :
        '<span class="badge text-bg-light border fw-normal me-1">' + label + ' <bdi>' + esc(val) + '</bdi></span>';
};
var formatProduct = function (product) {
    if (product.loading) { return esc(product.text); }
    return '<div class="py-1">' +
        '<div class="fw-semibold">' + esc(product.text) + '</div>' +
        '<div class="small mt-1">' +
            hsChip('رقم القطعة 1', product.serialNo) +
            hsChip('رقم القطعة 2', product.commCode) +
            hsChip('التسلسل', product.id) +
            hsChip('الشركة', product.company) +
            ((product.quantity === undefined || product.quantity === null || product.quantity === '') ? '' :
                '<span class="badge text-bg-primary">الكمية ' + esc(product.quantity) + '</span>') +
        '</div></div>';
};
var formatProductSelection = function (product) { return esc(product.name || product.text); };
JS;
$this->registerJs($formatJs, View::POS_HEAD, 'histrans-select2-format');

$resultsJs = <<<'JS'
function (data, params) {
    params.page = params.page || 1;
    return { results: data.results };
}
JS;
?>
<div class="histrans report-form">
    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8">
            <?php $form = ActiveForm::begin(['id' => 'histrans-form']); ?>
            <div class="card">
                <div class="card-header fw-semibold"><i class="bi bi-clock-history me-1"></i> حركة الأصناف</div>
                <div class="card-body">

                    <?= $form->field($model, 'id', ['errorOptions' => $errCfg])->widget(Select2::class, [
                        'name' => 'kv-repo-template',
                        'id' => 'focus_first',
                        'language' => 'en',
                        'bsVersion' => '5.x',
                        'options' => [
                            'placeholder' => Yii::t('app', 'Search...'),
                            'dir' => 'rtl',
                            'multiple' => true,
                        ],
                        'pluginOptions' => [
                            'minimumInputLength' => 1,
                            'ajax' => [
                                'url' => Url::to(['/except/itemlist']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q: params.term, page: params.page}; }'),
                                'processResults' => new JsExpression($resultsJs),
                                'cache' => true,
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('formatProduct'),
                            'templateSelection' => new JsExpression('formatProductSelection'),
                        ],
                    ]) ?>

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
                            <?= $form->field($model, 'min_date', ['errorOptions' => $errCfg])->widget(DatePicker::class, $dateCfg())->label(Yii::t('app', 'Min Date')) ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'max_date', ['errorOptions' => $errCfg])->widget(DatePicker::class, $dateCfg())->label(Yii::t('app', 'Max Date')) ?>
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
    var ids = __IDS__, form = $('#histrans-form');
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
$this->registerJs(str_replace('__IDS__', $ids, $js), View::POS_READY, 'histrans-form-js');
