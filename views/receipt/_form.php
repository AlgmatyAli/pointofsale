<?php

use app\models\Client;
use app\models\Currancy;
use app\models\PaymentTypes;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/** @var yii\web\View $this */
/** @var app\models\Receipt $model */

// نوع السند: 1 = سند قبض (من عميل)، غير ذلك = سند صرف (لمورد). كان الكود يقرأ $_GET['type'] مباشرة فيُنتج تحذيراً إن غاب
$type = (string) ($model->isNewRecord ? Yii::$app->request->get('type', $model->type) : $model->type);
$isReceive = ($type === '1');
$accent = $isReceive ? 'success' : 'danger';

$clients = ArrayHelper::map(
    Client::find()->where(['in', 'type', $isReceive ? [0, 2] : [1, 2]])->all(),
    'id',
    'name'
);

$this->registerCss(<<<'CSS'
.receipt-form .form-label { font-size: .85rem; margin-bottom: .2rem; }
.receipt-form .rc-amount input { font-size: 1.5rem; font-weight: 700; text-align: center; min-height: 3.4rem; }
.receipt-form .rc-balance { position: sticky; top: 1rem; }
.receipt-form .rc-save { position: sticky; bottom: 0; z-index: 5; background: var(--bs-body-bg); border-top: 1px solid var(--bs-border-color); padding: .75rem 0; margin-top: 1rem; }
CSS);
?>

<div class="receipt-form">

    <?php $form = ActiveForm::begin(['id' => 'receipt-form']); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger py-2 small']) ?>

    <div class="row g-3">

        <!-- رصيد العميل: يظهر أولاً على الهاتف وفي الجانب على الشاشات الكبيرة -->
        <div class="col-lg-4 order-first order-lg-last">
            <div class="card rc-balance border-<?= $accent ?>">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-<?= $accent ?>-subtle text-<?= $accent ?>"
                          style="width:3.25rem;height:3.25rem"><i class="bi bi-wallet2 fs-3"></i></span>
                    <div>
                        <div class="text-body-secondary small"><?= $isReceive ? 'رصيد الزبون' : 'رصيد المورد' ?></div>
                        <div class="fs-3 fw-bold text-<?= $accent ?>" dir="ltr" id="remainingBalance">0</div>
                    </div>
                </div>
                <div class="card-footer small text-body-secondary">يُحدَّث عند اختيار العميل أو تغيير العملة</div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header fw-bold text-<?= $accent ?>">
                    <i class="bi <?= $isReceive ? 'bi-box-arrow-in-down' : 'bi-box-arrow-up' ?>"></i>
                    <?= $isReceive ? 'سند قبض' : 'سند صرف' ?>
                </div>
                <div class="card-body">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <?= $form->field($model, 'clinet')->widget(Select2::class, [
                                'data' => $clients,
                                'language' => 'ar',
                                'options' => ['placeholder' => 'الرجاء اختيار اسم العميل ...'],
                                'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                                'pluginEvents' => ['change' => 'function () { receiptBalance(); }'],
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'currancy')->widget(Select2::class, [
                                'data' => ArrayHelper::map(Currancy::find()->all(), 'id', 'name'),
                                'language' => 'ar',
                                'options' => ['id' => 'currancy', 'placeholder' => 'الرجاء اختيار العملة ...'],
                                'pluginOptions' => ['allowClear' => false, 'multiple' => false],
                                'pluginEvents' => ['change' => 'function () { receiptBalance(); }'],
                            ]) ?>
                        </div>

                        <div class="col-md-6 rc-amount">
                            <?php // main() دالة عامة معرّفة في مكان آخر؛ نتحقق من وجودها كي لا يحدث خطأ JS إن غابت ?>
                            <?= $form->field($model, 'value')->textInput([
                                'inputmode' => 'decimal',
                                'autocomplete' => 'off',
                                'placeholder' => '0.000',
                                'onfocusout' => 'if (window.main) { main($(this)); }',
                            ]) ?>
                        </div>
                        <div class="col-md-6">
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

                        <div class="col-12">
                            <?= $form->field($model, 'tafqet')->textarea(['rows' => 2]) ?>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($model, 'payment_type')->widget(Select2::class, [
                                'data' => ArrayHelper::map(PaymentTypes::find()->all(), 'id', 'name'),
                                'language' => 'ar',
                                'options' => [
                                    'placeholder' => 'الرجاء اختيار  طريقة الدفع ...',
                                    'value' => $model->isNewRecord ? 1 : $model->payment_type,
                                ],
                                'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                            ]) ?>
                        </div>

                        <div class="col-12">
                            <?= $form->field($model, 'why')->textarea(['rows' => 3, 'placeholder' => 'البيان / ملاحظات (اختياري)']) ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'type')->hiddenInput([
                        'readonly' => true,
                        'value' => $model->isNewRecord ? $type : $model->type,
                    ])->label(false) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="rc-save d-flex gap-2">
        <?= Html::submitButton(
            '<i class="bi bi-check2-circle"></i> ' . Yii::t('app', 'Save'),
            ['class' => 'btn btn-lg flex-grow-1 btn-' . $accent]
        ) ?>
        <?= Html::a(
            '<i class="bi bi-arrow-counterclockwise"></i> ' . Yii::t('app', 'Clear'),
            ['/receipt/create', 'type' => $type],
            ['class' => 'btn btn-lg btn-outline-secondary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs(
    'window.receiptBalanceUrl = ' . json_encode(Url::to(['receipt/get-balance'])) . ';'
    . 'window.receiptClientSel = ' . json_encode('#' . Html::getInputId($model, 'clinet')) . ';',
    View::POS_HEAD,
    'receipt-balance-url'
);
$this->registerJs(<<<'JS'
// منع النقر المزدوج على «حفظ» (كان يُنشئ سنداً مكرراً)
$('#receipt-form').on('beforeSubmit', function () {
    $(this).find('button[type=submit]').prop('disabled', true);
    return true;
});
// رصيد العميل بالعملة المختارة (كان الطلب يُبنى بإلحاق &client=... على الرابط، فينكسر مع الروابط الجميلة)
window.receiptBalance = function () {
    var client = $(window.receiptClientSel).val();
    var currancy = $('#currancy').val();
    if (!client) { $('#remainingBalance').text(0); return; }
    $.get(window.receiptBalanceUrl, {client: client, currancy: currancy}, function (data) {
        $('#remainingBalance').text(data != null ? data : 0);
    });
};
JS, View::POS_READY, 'receipt-balance-js');
