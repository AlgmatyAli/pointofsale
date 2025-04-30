<?php

use app\models\Currancy;
use app\models\CustomsDeclaration;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ShipmentData */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shipment-data-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <?= $form->field($model, 'shipmentId')->textInput() ?>

            <?php
            echo $form->field($model, 'customsOffice')->widget(Select2::class, [
                'data' => ArrayHelper::map(CustomsDeclaration::find()
                    ->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => ['placeholder' => ' اختيار اسم المصرح الجمركي ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false
                ],
            ]);
            ?>

            <?=
            $form->field($model, 'at')->widget(
                DatePicker::class,
                [
                    'value' => '02-16-2012',
                    'language' => 'ar',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'todayBtn' => true,
                    ]
                ]
            ) ?>

            <?= $form->field($model, 'value')->textInput() ?>

            <?php
            echo $form->field($model, 'currancy')->widget(Select2::class, [
                'data' => ArrayHelper::map(Currancy::find()
                    ->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => ['placeholder' => ' ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>

            <?= $form->field($model, 'size')->textInput() ?>

            <?= $form->field($model, 'country')->textInput() ?>

            <?= $form->field($model, 'type')->textInput() ?>

            <?= $form->field($model, 'notes')->textarea(['rows' => 6]) ?>
            <br>
            <div class="form-group">
                <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-lg']) ?>
                <?= Html::a('<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-danger btn-lg']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>