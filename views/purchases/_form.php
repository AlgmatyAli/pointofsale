<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Client;
use app\models\Currancy;
use app\models\PaymentTypes;
use app\models\ShippingType;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/** @var app\models\Purchases $model */
/** @var yii\widgets\ActiveForm $form */
?>


<div class="purchases-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'type')->dropDownList(['1' => 'فاتورة مشتريات', '2' => 'فاتورة مسترجع مشتريات', '3' => 'فاتورة مشتريات معلقة'], ['prompt' => 'نوع الحركـة']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?php
            echo $form->field($model, 'clinet')->widget(Select2::class, [
                'data' => ArrayHelper::map(Client::find()
                    ->where(['in', 'type', [1, 2]])
                    // ->andWhere(['in', 'id', explode(',', Yii::$app->user->identity->client)])
                    //->andWhere(['branch' => Yii::$app->user->identity->branch])
                    ->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => ['placeholder' => 'الرجاء اختيار اسم العميل ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,


                ],
            ]);
            ?>
            <?= $form->field($model, 'clientBill')->textInput(['maxlength' => true]) ?>

            <?php
            echo $form->field($model, 'currancy')->widget(Select2::class, [
                'data' => ArrayHelper::map(Currancy::find()
                    // ->where(['in', 'type', [1,2]])
                    // ->andWhere(['in', 'id', explode(',', Yii::$app->user->identity->client)])
                    //->andWhere(['branch' => Yii::$app->user->identity->branch])
                    ->all(), 'id', 'name'),
                'language' => 'ar',
                // 'options' => ['placeholder' => 'الرجاء اختيار اسم العميل ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,


                ],
            ]);
            ?>

            <?php
            echo $form->field($model, 'shippingType')->widget(Select2::class, [
                'data' => ArrayHelper::map(ShippingType::find()
                    ->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => ['placeholder' => 'الرجاء اختيار اسم طريقة الشحن ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,


                ],
            ]);
            ?>
        </div>
        <div class="col-md-4">
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
            <?= $form->field($model, 'totalCost')->textInput(['maxlength' => true, 'value' => '0']) ?>

            <?= $form->field($model, 'total_currancy')->textInput(['maxlength' => true, 'value' => '0']) ?>

            <?php
            echo $form->field($model, 'dateOfArrival')->widget(
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
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'payWay')->dropDownList(['0' => 'نقدا', '1' => 'آجـــل', '2' => 'دفعة على الحساب',], ['prompt' => '']) ?>

            <?php
            echo $form->field($model, 'payment_type')->widget(Select2::class, [
                'data' => ArrayHelper::map(PaymentTypes::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => [
                    'placeholder' => 'الرجاء اختيار  طريقة الدفع ...',
                    'value' => $model->isNewRecord ? 1 : $model->payment_type
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>

            <?= $form->field($model, 'total')->textInput() ?>

            <?= $form->field($model, 'paid')->textInput([
                'maxlength' => true,
                'onfocusout' => 'netTotalsPurchases( $(this) )',
            ]); ?>

        </div>
    </div>
    <?= $form->field($model, 'notes')->textarea(['rows' => 3, 'columns' => 20]) ?>

    <div class="form-group">
        <div class="btn-group">
            <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Cancel'), ['/temp-invoice-purchase/create'], ['class' => 'btn btn-danger']) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

</div>