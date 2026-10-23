<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\daterange\DateRangePicker;
use app\models\Client;
use app\models\Currancy;
use app\models\PaymentTypes;
use app\models\ShippingType;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */

/** @var app\models\PurchasesSearch $model*/
/* @var $form yii\widgets\ActiveForm */

/*
 * ما لم يُمَس عمداً: action = index، method = get، data-pjax = 1، وأسماء كل حقول البحث.
 * تغيير الواجهة فقط: المرشحات الشائعة ظاهرة دائماً، والباقية في قسم «مرشحات إضافية» يُفتح تلقائياً
 * إن كان فيه مرشح مفعّل.
 */

// عدد المرشحات المفعّلة في القسم الإضافي (لشارة العدد ولفتح القسم تلقائياً)
$advancedAttributes = ['payWay', 'payment_type', 'currancy', 'total_currancy', 'dateOfArrival', 'shippingType'];
$advancedActive = 0;
foreach ($advancedAttributes as $attr) {
    if ($model->$attr !== null && $model->$attr !== '') {
        $advancedActive++;
    }
}

$rangeOptions = [
    'language' => 'en',
    'convertFormat' => false,
    'pluginOptions' => [
        'timePicker' => false,
        'timePickerIncrement' => 30,
        'locale' => ['format' => 'YYYY-MM-DD'],
    ],
];
?>

<div class="purchases-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <!-- المرشحات الشائعة -->
    <div class="row">
        <div class="col-6 col-lg-2">
            <?= $form->field($model, 'billId')->textInput(['inputmode' => 'numeric']) ?>
        </div>
        <div class="col-6 col-lg-2">
            <?= $form->field($model, 'clientBill') ?>
        </div>
        <div class="col-12 col-lg-3">
            <?= $form->field($model, 'clinet')->widget(\app\widgets\LookupSelect2::class, [
                'lookup' => 'client', 'types' => [1, 2],
                'language' => 'ar',
                'options' => ['placeholder' => 'الرجاء اختيار اسم العميل ...'],
                'pluginOptions' => ['allowClear' => true, 'multiple' => false],
            ]) ?>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <?= $form->field($model, 'at')->widget(DateRangePicker::class, $rangeOptions)->label('تاريخ الفاتورة') ?>
        </div>
        <div class="col-12 col-sm-6 col-lg-2">
            <?= $form->field($model, 'type')->dropDownList(
                ['1' => 'مشتريات', '2' => 'مسترجع مشتريات', '3' => 'فاتورة مشتريات معلقة'],
                ['prompt' => 'اختيار نوع الفاتورة']
            ) ?>
        </div>
    </div>

    <!-- مرشحات إضافية -->
    <div class="mb-3">
        <button type="button" class="btn btn-sm btn-link text-decoration-none px-0" data-bs-toggle="collapse"
            data-bs-target="#purchases-search-more" aria-expanded="<?= $advancedActive ? 'true' : 'false' ?>"
            aria-controls="purchases-search-more">
            <i class="bi bi-sliders"></i> مرشحات إضافية
            <?php if ($advancedActive) : ?>
                <span class="badge text-bg-primary rounded-pill"><?= $advancedActive ?></span>
            <?php endif; ?>
        </button>
    </div>

    <div id="purchases-search-more" class="collapse<?= $advancedActive ? ' show' : '' ?>">
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-3">
                <?= $form->field($model, 'payWay')->dropDownList(
                    ['0' => 'نقدا', '1' => 'آجـــل', '2' => 'دفعة على الحساب'],
                    ['prompt' => 'اختيار طريقة الدفع']
                ) ?>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <?= $form->field($model, 'payment_type')->widget(Select2::class, [
                    'data' => ArrayHelper::map(PaymentTypes::find()->all(), 'id', 'name'),
                    'language' => 'ar',
                    'options' => ['placeholder' => 'الرجاء اختيار  طريقة الدفع ...'],
                    'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                ]) ?>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <?= $form->field($model, 'currancy')->widget(Select2::class, [
                    'data' => ArrayHelper::map(Currancy::find()->orderBy('id')->asArray()->all(), 'id', 'name'),
                    'options' => ['placeholder' => 'اختيار العملة'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <?= $form->field($model, 'total_currancy')->textInput(['maxlength' => true, 'inputmode' => 'decimal']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-3">
                <?= $form->field($model, 'dateOfArrival')->widget(DateRangePicker::class, $rangeOptions)->label('تاريخ الوصول') ?>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <?= $form->field($model, 'shippingType')->widget(Select2::class, [
                    'data' => ArrayHelper::map(ShippingType::find()->orderBy('id')->asArray()->all(), 'id', 'name'),
                    'options' => ['placeholder' => Yii::t('app', 'اختيار نوع الشحن')],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 border-top pt-3 mt-2">
        <?= Html::submitButton('<i class="bi bi-search"></i> ' . Yii::t('app', 'Search'), ['class' => 'btn btn-primary px-4']) ?>
        <?= Html::a('<i class="bi bi-eraser"></i> ' . Yii::t('app', 'Erase'), Url::toRoute(['index']), ['class' => 'btn btn-outline-secondary']) ?>
    </div>
    <br>
    <?php ActiveForm::end(); ?>

</div>