<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use app\models\Branches;
use wbraganca\dynamicform\DynamicFormWidget;
use app\models\Client;
use app\models\Currancy;
use app\models\ShippingType;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\PurchasesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="purchases-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($model, 'billId') ?>

            <?= $form->field($model, 'clinet')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Client::find()
                    ->where(['in', 'type', [1, 2]])
                    //->andWhere(['branch' => Yii::$app->user->identity->branch])
                    ->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => ['placeholder' => 'الرجاء اختيار اسم العميل ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,


                ],
            ]); ?>
        </div>

        <div class="col-sm-2">
            <?php
            echo '<label class="control-label">تاريخ الفاتورة</label>';
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

            <?= $form->field($model, 'payWay')->dropDownList(['0' => 'نقدا', '1' => 'آجـــل', '2' => 'دفعة على الحساب',], ['prompt' => 'اختيار طريقة الدفع']) ?>
        </div>
        <div class="col-sm-2">

            <?php echo $form->field($model, 'clientBill') ?>
            <?= $form->field($model, 'type')->dropDownList(['1' => 'مشتريات', '2' => 'مسترجع مشتريات', '3' => 'فاتورة مشتريات معلقة'], ['prompt' => 'اختيار نوع الفاتورة']) ?>
        </div>
        <div class="col-md-2">
            <?php echo $form->field($model, 'total_currancy')->textInput(['maxlength' => true,])  ?>

            <?= $form->field($model, 'currancy')->widget(\kartik\widgets\Select2::classname(), [
                'data' => ArrayHelper::map(Currancy::find()->orderBy('id')->asArray()->all(), 'id', 'name'),
                // 'options' => ['placeholder' => Yii::t('app', 'Choose Category')],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="col-md-2">
           <?php
            echo '<label class="control-label">تاريخ الوصول</label>';
            echo DateRangePicker::widget([
                'model' => $model,
                'attribute' => 'dateOfArrival',
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

            <?= $form->field($model, 'shippingType')->widget(\kartik\widgets\Select2::classname(), [
                'data' => ArrayHelper::map(ShippingType::find()->orderBy('id')->asArray()->all(), 'id', 'name'),
                'options' => ['placeholder' => Yii::t('app', 'اختيار نوع الشحن')],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
    </div>
    <hr>
    <br>
    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-search"></i>' . ' ' . Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>' . ' ' . Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger btn-lg']) ?>
    </div>
    <br>
    <?php ActiveForm::end(); ?>

</div>