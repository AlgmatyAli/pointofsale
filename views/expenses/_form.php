<?php

use app\models\Currancy;
use app\models\Items;
use app\models\PaymentTypes;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Expenses $model */
/** @var yii\bootstrap5\ActiveForm $form */

// مفتاح تشغيل/إيقاف: يرسل 0 عند الإيقاف و1 عند التشغيل (كمربع الاختيار الأصلي)
$outBoxInput = Html::activeCheckbox($model, 'outBox', [
    'class' => 'form-check-input',
    'role' => 'switch',
    'label' => null,
]);
?>

<div class="expenses-form">
    <?php $form = ActiveForm::begin(['id' => 'expenses-form']); ?>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">

            <!-- بيانات المصروف -->
            <div class="card mb-3">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span class="fw-semibold"><i class="bi bi-receipt"></i> بيانات المصروف</span>
                    <?= Html::button(
                        '<i class="bi bi-plus-lg"></i> ' . Yii::t('app', 'Create Items'),
                        [
                            'value' => Url::to(['items/create-items']),
                            'class' => 'btn btn-sm btn-outline-danger popup',
                        ]
                    ) ?>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'expenseTo')->textInput(['maxlength' => true, 'autofocus' => true]) ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'at')->widget(DatePicker::class, [
                                'language' => 'ar',
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd',
                                    'todayHighlight' => true,
                                    'todayBtn' => true,
                                ],
                            ])->label(Yii::t('app', 'Expens Date')) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'itemId')->widget(Select2::class, [
                                'data' => ArrayHelper::map(Items::find()->all(), 'id', 'name'),
                                'language' => 'ar',
                                'options' => ['placeholder' => 'الرجاء اختيار اسم المصروف ...'],
                                'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                            ]) ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'why')->textarea(['rows' => 3]) ?>
                </div>
            </div>

            <!-- المبلغ والدفع -->
            <div class="card mb-3">
                <div class="card-header fw-semibold"><i class="bi bi-cash-coin"></i> المبلغ والدفع</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'value')->textInput(['inputmode' => 'decimal']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'currancy')->widget(Select2::class, [
                                'data' => ArrayHelper::map(Currancy::find()->all(), 'id', 'name'),
                                'language' => 'ar',
                                'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                            ]) ?>
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
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch mb-3">
                                <?= $outBoxInput ?>
                                <label class="form-check-label fw-semibold" for="<?= Html::getInputId($model, 'outBox') ?>">
                                    <?= Html::encode($model->getAttributeLabel('outBox')) ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- شريط الحفظ: يبقى ظاهراً أسفل الشاشة على الهاتف -->
            <div class="position-sticky bottom-0 bg-body border-top py-2 d-flex gap-2" style="z-index:5">
                <?= Html::submitButton(
                    '<i class="bi bi-check-lg"></i> ' . Yii::t('app', 'Save'),
                    ['class' => 'btn btn-success flex-fill flex-md-grow-0 px-md-5']
                ) ?>
                <?= Html::a(
                    '<i class="bi bi-x-lg"></i> ' . Yii::t('app', 'Cancel'),
                    Yii::$app->request->referrer ?: ['index'],
                    ['class' => 'btn btn-outline-secondary flex-fill flex-md-grow-0']
                ) ?>
            </div>

        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
