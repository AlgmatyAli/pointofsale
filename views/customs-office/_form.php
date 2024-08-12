<?php

use app\models\base\Currancy;
use app\models\CustomsDeclaration;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\models\CustomsOffice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customs-office-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    echo $form->field($model, 'customId')->widget(Select2::class, [
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

    <?= $form->field($model, 'why')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-danger btn-lg']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>