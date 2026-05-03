<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Client;
use app\models\Currancy;
use app\models\PaymentTypes;
use kartik\date\DatePicker;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Receipt $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="receipt-form">

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
                    //->andWhere(['in','id' => Yii::$app->user->identity->client])
                    // ->andWhere(['branch' => Yii::$app->user->identity->branch])
                    ->all(), 'id', 'name');
            } else {
                $data = ArrayHelper::map(Client::find()
                    ->where(['in', 'type', [1, 2]])
                    //->andWhere(['in', 'id', explode(',', Yii::$app->user->identity->client)])
                    // ->andWhere(['branch' => Yii::$app->user->identity->branch])
                    ->all(), 'id', 'name');
            }

            echo $form->field($model, 'clinet')->widget(Select2::class, [
                'data' => $data,
                'language' => 'ar',
                'options' => ['placeholder' => 'الرجاء اختيار اسم العميل ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false
                ],
                'pluginEvents' => [
                    'change' => 'function(event){
                var client = event.currentTarget.value;
                var currancy = $("#currancy").val();
                $.get("' . Url::to(['receipt/get-balance']) . '&client="+client+"&currancy="+currancy, function(data){
                    
                    if(data != null){
                    $("#remainingBalance").text(data);
                    }else{
                        $("#remainingBalance").text(0);
                    }
                });
            }',
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

            <?= $form->field($model, 'value')->textInput(['onfocusout' => 'main( $(this) )']) ?>

            <?= $form->field($model, 'tafqet')->textarea(['rows' => 1]) ?>

            <?= $form->field($model, 'why')->textarea(['rows' => 6]) ?>

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
            <br>

            <?= $form->field($model, 'type')->hiddenInput(['readonly' => true, 'value' => $model->isNewRecord ? $_GET['type'] : $model->type])->label(false) ?>

            <div class="form-group">
                <?= Html::a('<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Clear'), ['/receipt/create', 'type' => $_GET['type']], ['class' => 'btn btn-danger btn-md']) ?>

                <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-md']) ?>
            </div>
        </div>

        <?php
        echo  '<div class="col-sm-3 col-md-3 col-lg-3">
                <div class="panel panel-danger">
                    <div class="panel-heading"> رصيد الزبون </div>
                    <div class="panel-body">
                      <center> <h3 class="text-danger"><span id="remainingBalance">0</span></h3> </center>
                    </div>
                </div>
            </div>';
        ?>

        <?php ActiveForm::end(); ?>

    </div>