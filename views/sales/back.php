<?php

use app\models\Currancy;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Client;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $model app\models\Sales */
/* @var $form yii\widgets\ActiveForm */
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