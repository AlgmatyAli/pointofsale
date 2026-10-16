<?php

use app\models\Branches;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Safe $model */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<div class="safe-form">
    <?php $form = ActiveForm::begin(['id' => 'safe-form']); ?>

    <div class="row justify-content-center">
        <div class="col-lg-7 col-xl-8">

            <!-- بيانات الحركة -->
            <div class="card mb-3">
                <div class="card-header fw-semibold"><i class="bi bi-safe2"></i> بيانات حركة الخزينة</div>
                <div class="card-body">

                    <?= $form->field($model, 'branch')->widget(Select2::class, [
                        'data' => ArrayHelper::map(Branches::find()->all(), 'id', 'name'),
                        'language' => 'ar',
                        'options' => ['placeholder' => 'الرجاء اختيار اسم الفرع ...'],
                        'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                    ]) ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'safeNo')->dropDownList(
                                ['1' => ' خزينة رقـم 1', '2' => 'خزينة رقـم 2'],
                                ['prompt' => 'اختيار اسم الخزينة']
                            ) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'type')->dropDownList(
                                ['1' => ' صادر', '2' => 'وارد'],
                                ['prompt' => 'اختيار نوع الحركة']
                            ) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'value')->textInput(['inputmode' => 'decimal']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'at')->widget(DatePicker::class, [
                                'language' => 'ar',
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd',
                                    'todayHighlight' => true,
                                    'todayBtn' => true,
                                ],
                            ]) ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'why')->textarea(['rows' => 3]) ?>
                </div>
            </div>

            <!-- شريط الحفظ: يبقى ظاهراً أسفل الشاشة على الهاتف -->
            <div class="position-sticky bottom-0 bg-body border-top py-2 d-flex gap-2" style="z-index:5">
                <?= Html::submitButton(
                    '<i class="bi bi-check-lg"></i> ' . Yii::t('app', 'Save'),
                    ['class' => 'btn btn-success flex-fill flex-md-grow-0 px-md-5']
                ) ?>
                <?= Html::a(
                    '<i class="bi bi-eraser"></i> ' . Yii::t('app', 'Clear'),
                    ['/safe/create'],
                    ['class' => 'btn btn-outline-secondary flex-fill flex-md-grow-0']
                ) ?>
            </div>

        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>