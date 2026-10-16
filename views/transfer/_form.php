<?php

use app\models\Branches;
use app\models\Currancy;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Transfer */

$branches = ArrayHelper::map(Branches::find()->all(), 'id', 'name');

$this->registerCss(<<<'CSS'
.transfer-form .form-label { font-size: .85rem; margin-bottom: .2rem; }
.transfer-form .tr-amount input { font-size: 1.5rem; font-weight: 700; text-align: center; min-height: 3.4rem; }
.transfer-form .btn-group > .btn { white-space: nowrap; }
.transfer-form .tr-save { position: sticky; bottom: 0; z-index: 5; background: var(--bs-body-bg); border-top: 1px solid var(--bs-border-color); padding: .75rem 0; margin-top: 1rem; }
CSS);
?>

<div class="transfer-form">

    <?php $form = ActiveForm::begin(['id' => 'transfer-form']); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger py-2 small']) ?>

    <div class="card mb-3">
        <div class="card-header fw-bold"><i class="bi bi-arrow-left-right text-primary"></i> الحركة بين الفروع</div>
        <div class="card-body">

            <!-- نوع الحركة -->
            <?= $form->field($model, 'type')->radioList(
                ['1' => 'صادر', '2' => 'وارد'],
                [
                    'class' => 'btn-group w-100',
                    'role' => 'group',
                    'item' => function ($index, $label, $name, $checked, $value) {
                        $id = 'transfer-type-' . $index;
                        $color = $value === '1' ? 'danger' : 'success';
                        return Html::radio($name, $checked, ['value' => $value, 'id' => $id, 'class' => 'btn-check', 'autocomplete' => 'off'])
                            . Html::label(Html::encode($label), $id, ['class' => 'btn btn-outline-' . $color . ' flex-fill']);
                    },
                ]
            )->label('نوع الحركة') ?>

            <!-- من فرع → إلى فرع -->
            <div class="row g-2 align-items-end">
                <div class="col-md">
                    <?= $form->field($model, 'fromBr')->widget(Select2::class, [
                        'data' => $branches,
                        'language' => 'ar',
                        'options' => ['placeholder' => 'اختيار اسم الفرع المسحوب منه...'],
                        'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                    ]) ?>
                </div>
                <div class="col-md-auto text-center mb-3">
                    <button type="button" class="btn btn-outline-secondary" id="transfer-swap" title="تبديل الفرعين" aria-label="تبديل الفرعين">
                        <i class="bi bi-arrow-left-right"></i>
                    </button>
                </div>
                <div class="col-md">
                    <?= $form->field($model, 'toBr')->widget(Select2::class, [
                        'data' => $branches,
                        'language' => 'ar',
                        'options' => ['placeholder' => 'اختيار اسم الفرع المودع له...'],
                        'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                    ]) ?>
                </div>
            </div>
            <div id="transfer-same" class="small text-warning mb-3 d-none">
                <i class="bi bi-exclamation-triangle"></i> الفرع المسحوب منه هو نفسه الفرع المودع له.
            </div>

            <div class="row g-3">
                <div class="col-md-5 tr-amount">
                    <?= $form->field($model, 'value')->textInput(['inputmode' => 'decimal', 'autocomplete' => 'off', 'placeholder' => '0.000']) ?>
                </div>
                <div class="col-6 col-md-4">
                    <?= $form->field($model, 'currancy')->widget(Select2::class, [
                        'data' => ArrayHelper::map(Currancy::find()->all(), 'id', 'name'),
                        'language' => 'ar',
                        'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                    ]) ?>
                </div>
                <div class="col-6 col-md-3">
                    <?= $form->field($model, 'at')->widget(DatePicker::class, [
                        'language' => 'ar',
                        'options' => ['placeholder' => 'yyyy-mm-dd', 'autocomplete' => 'off'],
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,
                            'todayBtn' => true,
                        ],
                    ]) ?>
                </div>
            </div>

            <?= $form->field($model, 'why')->textarea(['rows' => 3, 'placeholder' => 'البيان / ملاحظات (اختياري)']) ?>
        </div>
    </div>

    <div class="tr-save d-flex gap-2">
        <?= Html::submitButton('<i class="bi bi-check2-circle"></i> ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-lg flex-grow-1']) ?>
        <?php // كان زر Clear يشير إلى /safe/create (نموذج الخزينة) ، وصار يعيد فتح نموذج التحويل نفسه ?>
        <?= Html::a('<i class="bi bi-arrow-counterclockwise"></i> ' . Yii::t('app', 'Clear'), ['create'], ['class' => 'btn btn-lg btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$from = '#' . Html::getInputId($model, 'fromBr');
$to = '#' . Html::getInputId($model, 'toBr');
$this->registerJs(
    'var trFrom = ' . json_encode($from) . ', trTo = ' . json_encode($to) . ';',
    View::POS_READY,
    'transfer-ids'
);
$this->registerJs(<<<'JS'
function trCheck() {
    var a = $(trFrom).val(), b = $(trTo).val();
    $('#transfer-same').toggleClass('d-none', !(a && b && a === b));
}
// تبديل الفرعين
$('#transfer-swap').on('click', function () {
    var a = $(trFrom).val(), b = $(trTo).val();
    $(trFrom).val(b).trigger('change');
    $(trTo).val(a).trigger('change');
});
$(trFrom + ',' + trTo).on('change', trCheck);
trCheck();
JS, View::POS_READY, 'transfer-js');
