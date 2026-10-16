<?php

use app\models\Client;
use app\models\Currancy;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/** @var yii\web\View $this */
/** @var app\models\DisscountClients $model */
/** @var yii\bootstrap5\ActiveForm $form */

// نفس منطق الاختيار القديم: type=1 => عملاء (0,2)، غير ذلك => موردون (1)
$typeParam = Yii::$app->request->get('type', $model->type);
$partyTypes = ((string) $typeParam === '1') ? [0, 2] : [1];
$parties = ArrayHelper::map(
    Client::find()->select(['id', 'name'])->where(['in', 'type', $partyTypes])->orderBy('name')->asArray()->all(),
    'id',
    'name'
);
$currencies = ArrayHelper::map(Currancy::find()->select(['id', 'name'])->asArray()->all(), 'id', 'name');

if ($model->isNewRecord && empty($model->at)) {
    $model->at = date('Y-m-d');
}

// النوع حقل مخفي: عند الإنشاء من الرابط، وعند التعديل من السجل نفسه (الافتراضي 2 = خصم من المشتريات)
$typeVal = $model->isNewRecord ? ($typeParam ?: '2') : $model->type;
$errCfg = ['class' => 'invalid-feedback d-block'];

$this->registerCss(<<<'CSS'
.disscount-clients-form .save-bar { position: sticky; bottom: 0; z-index: 5; background: var(--bs-body-bg); border-top: 1px solid var(--bs-border-color); padding: .6rem 0; margin-top: 1rem; }
CSS);
?>

<div class="disscount-clients-form">

    <?php $form = ActiveForm::begin(['id' => 'disc-supplier-form']); ?>

    <div class="row justify-content-center">
        <div class="col-xl-7 col-lg-9">
            <div class="card">
                <div class="card-body">

                    <?= Html::activeHiddenInput($model, 'type', ['value' => $typeVal]) ?>
                    <?= Html::error($model, 'type', $errCfg) ?>

                    <?= $form->field($model, 'client', ['errorOptions' => $errCfg])->widget(Select2::class, [
                        'data' => $parties,
                        'language' => 'ar',
                        'bsVersion' => '5.x',
                        'options' => ['placeholder' => 'الرجاء اختيار اسم المورد ...'],
                        'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                    ]) ?>

                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'currancy', ['errorOptions' => $errCfg])->widget(Select2::class, [
                                'data' => $currencies,
                                'language' => 'ar',
                                'bsVersion' => '5.x',
                                'options' => ['id' => 'currancy', 'placeholder' => 'الرجاء اختيار العملة ...'],
                                'pluginOptions' => ['allowClear' => false, 'multiple' => false],
                            ]) ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'at', ['errorOptions' => $errCfg])->widget(DatePicker::class, [
                                'language' => 'ar',
                                'bsVersion' => '5.x',
                                'options' => ['autocomplete' => 'off'],
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd',
                                    'todayHighlight' => true,
                                    'todayBtn' => true,
                                ],
                            ]) ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'value', [
                        'inputTemplate' => '<div class="input-group"><span class="input-group-text"><i class="bi bi-cash-stack"></i></span>{input}</div>',
                        'errorOptions' => $errCfg,
                    ])->textInput(['maxlength' => true, 'inputmode' => 'decimal', 'dir' => 'ltr', 'autocomplete' => 'off']) ?>

                    <?= $form->field($model, 'notes')->textarea(['rows' => 3]) ?>
                </div>
            </div>

            <div class="save-bar d-flex gap-2">
                <?= Html::submitButton('<i class="bi bi-save"></i> ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-lg flex-grow-1', 'id' => 'disc-save']) ?>
                <?= Html::a('<i class="bi bi-x-lg"></i> ' . Yii::t('app', 'Cancel'), Yii::$app->request->referrer ?: ['index_'], ['class' => 'btn btn-lg btn-outline-secondary']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs(<<<'JS'
$('#disc-supplier-form').on('beforeSubmit', function () {
    $('#disc-save').prop('disabled', true);
    return true;
});
JS, View::POS_READY, 'disc-supplier-form-js');
