<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Client;
use app\models\Agent;
use app\models\PaymentTypes;
use kartik\daterange\DateRangePicker;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\ReceiptSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="receipt-search">
    <div class="row">

        <div class="col-lg-6">
            <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>

            <?= $form->field($model, 'rId') ?>

            <?= $form->field($model, 'clinet')->widget(Select2::class, [
                'data' => ArrayHelper::map(Client::find()
                    // ->where(['branch' => Yii::$app->user->identity->branch])
                    ->where(['in', 'type', [0, 2]])
                    ->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => ['placeholder' => 'الرجاء اختيار اسم العميل ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false
                ],
            ]);
            ?>

            <?php
            echo '<label class="control-label">تاريخ الايصال</label>';
            echo DateRangePicker::widget([
                'model' => $model,
                'attribute' => 'at',
                'language' => 'en',
                'convertFormat' => false,
                'pluginOptions' => [
                    'timePicker' => false,
                    'timePickerIncrement' => 30,
                    'locale' => [
                        'format' => 'YYYY-MM-DD'
                    ]
                ]
            ]);

            ?><br>

            <br>
            <div class="form-group">
                <?= Html::submitButton('<i class="fa fa-fw fa-search"></i>' . ' ' . Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-lg']) ?>
                <?= Html::a('<i class="fa fa-fw fa-eraser"></i>' . ' ' . Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger btn-lg']) ?>

            </div>

        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'value') ?>

            <?php
            echo $form->field($model, 'payment_type')->widget(Select2::class, [
                'data' => ArrayHelper::map(PaymentTypes::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => [
                    'placeholder' => 'الرجاء اختيار  طريقة الدفع ...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>

            <?= $form->field($model, 'type')->widget(Select2::class, [
                'data' => [
                    '1' => 'قبض',
                    '2' => 'صرف'
                ],
                'language' => 'ar',
                'options' => [
                    //  'placeholder' => 'الرجاء اختيار اسم العميل ...'
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,


                ],
            ]); ?>
        </div>

    </div>


    <?php ActiveForm::end(); ?>

</div>