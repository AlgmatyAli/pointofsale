<?php

use app\models\Client;
use app\models\Currancy;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\DisscountClients $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="disscount-clients-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-lg-1"></div>
        <div class="col-lg-5">
            <?php
            echo $form->field($model, 'currancy')->widget(Select2::class, [
                'data' => ArrayHelper::map(Currancy::find()
                    ->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => ['id' => 'currancy', 'placeholder' => 'الرجاء اختيار العملة ...'],
                'pluginOptions' => [
                    'allowClear' => false,
                    'multiple' => false,
                ],
            ]);
            ?>

            <?php
            if ($_GET['type'] == '1') {
                $data = ArrayHelper::map(Client::find()
                    ->where(['in', 'type', [0, 2]])
                    ->all(), 'id', 'name');
            } else {
                $data = ArrayHelper::map(Client::find()
                    ->where(['in', 'type', [1]])
                    ->all(), 'id', 'name');
            }
            echo $form->field($model, 'client')->widget(Select2::class, [
                'data' => $data,
                'language' => 'ar',
                'options' => ['placeholder' => 'الرجاء اختيار اسم العميل ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false
                ],
            ]);
            ?>

            <?php
            echo $form->field($model, 'at')->widget(
                DatePicker::class,
                [
                    'language' => 'ar',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'todayBtn' => true,
                    ]
                ]
            );
            ?>

            <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'notes')->textarea(['rows' => 6]) ?>

            <?= $form->field($model, 'type')->dropDownList([
                '1' => 'تخفيض على المبيعات',
                '2' => 'خصم من المشتريات'
            ], ['prompt' => 'اختيار نوع الحركة']) ?>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>