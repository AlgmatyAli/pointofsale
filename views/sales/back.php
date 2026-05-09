<?php

use app\models\Currancy;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Client;
use app\models\PaymentTypes;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/** @var app\models\Sales $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sales-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-5">

            <?php
            echo '<label class="form-label">تاريخ الفاتورة</label>';
            echo  DatePicker::widget([
                'model' => $model,
                'attribute' => 'at',
                'options' => ['placeholder' => 'Enter date ...'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                ]
            ]);
            ?>
            <br>

        </div>
        <div class="col-md-5">
            <?= $form->field($model, 'clinet')->widget(Select2::class, [
                'data' => ArrayHelper::map(Client::find()->where(['in', 'type', [0, 2]])->all(), 'id', 'name'),
                'language' => 'ar',
                'id' => 'clinet',
                'options' => ['placeholder' => 'الرجاء اختيار اسم العميل ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]); ?>

        </div>
    </div>
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-5">
            <?= $form->field($model, 'payWay')->dropDownList(['1' => 'نقدا', '2' => 'على الحساب',], ['prompt' => '']) ?>

            <?php
            echo $form->field($model, 'currancy')->widget(Select2::class, [
                'data' => ArrayHelper::map(Currancy::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>
        </div>

        <div class="col-md-5">
            <?= $form->field($model, 'total')->textInput(['placeholder' => 'Total']) ?>

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
        </div>
    </div>
    <?= $form->field($model, 'notes')->textInput(['maxlength' => true, 'placeholder' => 'Notes']) ?>

    <hr>
    <!-- =================== -->

    <div class="panel-footer">
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-danger btnx']) ?>
        </div>
    </div>
</div>

</div>

</div>
</div>
<?php ActiveForm::end(); ?>
</div>