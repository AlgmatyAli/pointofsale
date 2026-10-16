<?php

use app\models\Client;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\DisscountClientsSearch $model */
/** @var yii\bootstrap5\ActiveForm $form */

$active = !empty($model->client) || !empty($model->at) || !empty($model->value);
$suppliers = ArrayHelper::map(
    Client::find()->select(['id', 'name'])->where(['in', 'type', [1]])->orderBy('name')->asArray()->all(),
    'id',
    'name'
);
?>

<div class="disscount-clients-search mb-3">

    <button class="btn btn-outline-secondary w-100 mb-2 d-lg-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#disc-search-body-sup" aria-expanded="<?= $active ? 'true' : 'false' ?>" aria-controls="disc-search-body-sup">
        <i class="bi bi-funnel"></i> بحث وتصفية
        <?php if ($active): ?><span class="badge text-bg-primary">مفعّل</span><?php endif; ?>
    </button>

    <div class="collapse d-lg-block<?= $active ? ' show' : '' ?>" id="disc-search-body-sup">
        <div class="card">
            <div class="card-body py-3">
                <?php $form = ActiveForm::begin([
                    'id' => 'disc-search-form-sup',
                    'action' => ['index_'],
                    'method' => 'get',
                    'options' => ['data-pjax' => 1],
                ]); ?>
                <div class="row g-2 align-items-end">
                    <div class="col-lg-4">
                        <?= $form->field($model, 'client', ['options' => ['class' => 'mb-0']])->widget(Select2::class, [
                            'data' => $suppliers,
                            'language' => 'ar',
                            'bsVersion' => '5.x',
                            'options' => ['placeholder' => 'الرجاء اختيار اسم المورد ...'],
                            'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                        ]) ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'at', ['options' => ['class' => 'mb-0']])->label('تاريخ الايصال')->widget(DateRangePicker::class, [
                            'language' => 'en',
                            'bsVersion' => '5.x',
                            'convertFormat' => false,
                            'options' => ['autocomplete' => 'off', 'placeholder' => 'كل التواريخ'],
                            'pluginOptions' => [
                                'timePicker' => false,
                                'timePickerIncrement' => 30,
                                'locale' => ['format' => 'YYYY-MM-DD'],
                            ],
                        ]) ?>
                    </div>
                    <div class="col-lg-2">
                        <?= $form->field($model, 'value', ['options' => ['class' => 'mb-0']])->textInput(['inputmode' => 'decimal', 'dir' => 'ltr', 'autocomplete' => 'off']) ?>
                    </div>
                    <div class="col-lg-3 d-flex gap-2">
                        <?= Html::submitButton('<i class="bi bi-search"></i> ' . Yii::t('app', 'Search'), ['class' => 'btn btn-primary flex-grow-1']) ?>
                        <?= Html::a('<i class="bi bi-eraser"></i> ' . Yii::t('app', 'Erase'), Url::toRoute(['index_']), ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
