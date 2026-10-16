<?php

use app\models\Branches;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TransferItems $model */
/** @var yii\bootstrap5\ActiveForm $form */

$branches = ArrayHelper::map(Branches::find()->all(), 'id', 'name');
?>

<div class="transfer-items-form">
    <?php $form = ActiveForm::begin(['id' => 'transfer-items-form']); ?>

    <div class="row justify-content-center">
        <div class="col-lg-7 col-xl-12">
            <div class="card mb-3">
                <div class="card-header fw-semibold"><i class="bi bi-arrow-left-right"></i> بيانات النقل</div>
                <div class="card-body">

                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <?= $form->field($model, 'fromBranch')->widget(Select2::class, [
                                'data' => $branches,
                                'language' => 'ar',
                                'options' => ['placeholder' => 'اختيار اسم الفرع المسحوب منه...'],
                                'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                            ]) ?>
                        </div>
                        <div class="col-md-2 text-center text-primary fs-3 d-none d-md-block pt-2">
                            <i class="bi bi-arrow-left"></i>
                        </div>
                        <div class="col-md-5">
                            <?= $form->field($model, 'toBranch')->widget(Select2::class, [
                                'data' => $branches,
                                'language' => 'ar',
                                'options' => ['placeholder' => 'اختيار اسم الفرع المودع له...'],
                                'pluginOptions' => ['allowClear' => true, 'multiple' => false],
                            ]) ?>
                        </div>
                    </div>

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

            <!-- شريط الحفظ: يبقى ظاهراً أسفل الشاشة على الهاتف -->
            <div class="position-sticky bottom-0 bg-body border-top py-2 d-flex gap-2" style="z-index:5">
                <?= Html::submitButton(
                    '<i class="bi bi-check-lg"></i> ' . Yii::t('app', 'Save'),
                    ['class' => 'btn btn-success flex-fill flex-md-grow-0 px-md-5']
                ) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>