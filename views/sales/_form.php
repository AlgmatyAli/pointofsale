<?php

use app\models\base\Currancy;
use app\models\CompanyInfo;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Sales */
/* @var $form yii\widgets\ActiveForm */

$company = CompanyInfo::find()->one();

?>

<div class="sales-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <?= $form->field($model, 'type')->dropDownList(['1' => 'فاتورة نهائية', '3' => 'فاتورة حجز', '4' => 'فاتورة مبدئية'])->label(yii::t('app', 'Invoice Type')) ?>
    <div class="row">
        <div class="col-md-6">

            <?php
            if (Yii::$app->user->identity->client <> null) {
                echo $form->field($model, 'clinet')->widget(\kartik\widgets\Select2::classname(), [
                    'data' => \yii\helpers\ArrayHelper::map(\app\models\Client::find()
                        ->where(['in', 'type', [0, 2]])
                        //->andWhere(['branch' => Yii::$app->user->identity->branch])
                        ->andWhere(['in', 'id', explode(',', Yii::$app->user->identity->client)])

                        ->orderBy('id')->asArray()->all(), 'id', 'name'),
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            } else {
                echo $form->field($model, 'clinet')->widget(\kartik\widgets\Select2::classname(), [
                    'data' => \yii\helpers\ArrayHelper::map(\app\models\Client::find()
                        ->where(['in', 'type', [0, 2]])
                        //->andWhere(['branch' => Yii::$app->user->identity->branch])                       
                        ->orderBy('id')->asArray()->all(), 'id', 'name'),
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            }
            ?>

            <?= $form->field($model, 'agent')->widget(\kartik\widgets\Select2::classname(), [
                'data' => \yii\helpers\ArrayHelper::map(\app\models\Agent::find()
                    ->where(['in', 'branch', [Yii::$app->user->identity->branch]])
                    //->andWhere(['branch' => Yii::$app->user->identity->branch])
                    ->orderBy('id')->asArray()->all(), 'id', 'name'),
                'options' => ['placeholder' => 'إختر ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>

            <?=
            $form->field($model, 'at')->widget(
                DatePicker::className(),
                [
                    'value' => '02-16-2012',
                    'language' => 'ar',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'todayBtn' => true,
                    ]
                ]
            ) ?>


            <?php
            if (Yii::$app->user->identity->client <> null) {
                echo $form->field($model, 'payWay')->dropDownList(['1' => 'آجـــل'], ['prompt' => 'اختيار طريقة الدفع']);
            } else {
                echo $form->field($model, 'payWay')->dropDownList(['0' => 'نقدا', '1' => 'آجـــل', '2' => 'دفعة على الحساب',], ['prompt' => 'اختيار طريقة الدفع']);
            }
            ?>
            <?php
            echo $form->field($model, 'currancy')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Currancy::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>
            <br>
            <?php
            if (Yii::$app->user->identity->client == null && $company->invoiceState == 1) {
                echo $form->field($model, 'deleviried')->checkbox(['checked' => true]);
            }
            if (Yii::$app->user->identity->client == null && $company->invoiceState == 0) {
                echo $form->field($model, 'deleviried')->checkbox(['checked' => false]);
            }
            // if( Yii::$app->user->identity->client != null && $company->invoiceState == 1){
            //     echo $form->field($model, 'deleviried')->checkbox(['checked' => true]);
            //     }
            if (Yii::$app->user->identity->client != null) {
                echo $form->field($model, 'deleviried')->checkbox(['checked' => false]);
            }
            ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'total')->textInput(['placeholder' => 'Total']) ?>

            <?php
            if (Yii::$app->user->identity->makeDiscount == 1) {
                echo $form->field($model, 'disscount')->textInput(['placeholder' => 'Discount', 'disabled' => false]);
            } else {
                echo $form->field($model, 'disscount')->textInput(['placeholder' => 'Discount', 'disabled' => true]);
            }
            ?>

            <?php
            if (Yii::$app->user->identity->client == NULL) {
                echo $form->field($model, 'paid')->textInput(['placeholder' => 'Paid']);
            } else {
                echo $form->field($model, 'paid')->textInput(['placeholder' => 'Paid', 'disabled' => true]);
            }

            ?>

            <?=
            $form->field($model, 'deserving')->widget(
                DatePicker::className(),
                [
                    'value' => '02-16-2012',
                    'language' => 'ar',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'todayBtn' => true,
                    ]
                ]
            ) ?>
            <br>

            <?php echo $form->field($model, 'wholesale')->checkbox(['checked' => false]); ?>

        </div>

    </div>

    <?= $form->field($model, 'notes')->textInput(['maxlength' => true, 'placeholder' => 'Notes']) ?>

    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

    <?php ActiveForm::end(); ?>




</div>