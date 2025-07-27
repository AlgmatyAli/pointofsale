<?php

use app\models\Currancy;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Items;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Expenses */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="expenses-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-3">
            <?php echo Html::button('<i class="fa fa-fw fa-plus"></i>' . ' ' . Yii::t('app', 'Create Items'), ['value' => Url::to(['items/create-items']), 'class' => 'btn btn-danger popup']); ?>
            <br><br>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-6">
            <?= $form->field($model, 'expenseTo')->textInput(['maxlength' => true]) ?>

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
            )->label(Yii::t('app', 'Expens Date'));
            ?>

            <?php
            echo $form->field($model, 'itemId')->widget(Select2::class, [
                'data' => ArrayHelper::map(Items::find()
                    ->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => ['placeholder' => 'الرجاء اختيار اسم المصروف ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false
                ],
            ]);
            ?>

            <?= $form->field($model, 'value')->textInput() ?>

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

            <?= $form->field($model, 'outBox')->checkbox() ?>

            <?= $form->field($model, 'why')->textarea(['rows' => 6]) ?>

            <div class="form-group">
                <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-danger']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>