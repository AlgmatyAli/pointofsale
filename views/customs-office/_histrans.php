<?php

use app\models\base\Currancy;
use app\models\CustomsDeclaration;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\ReceiptSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="histrans">

    <?php $form = ActiveForm::begin(); ?>
    <?php
    echo $form->field($model, 'customId')->widget(Select2::class, [
        'data' => ArrayHelper::map(CustomsDeclaration::find()->all(), 'id', 'name'),
        'language' => 'ar',
        'options' => ['placeholder' => 'الرجاء اختيار اسم المصرح الجمركي ...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple' => false
        ],
    ]);
    ?>

    <?php
    echo $form->field($model, 'currancy')->widget(Select2::class, [
        'data' => ArrayHelper::map(Currancy::find()->all(), 'id', 'name'),
        'language' => 'ar',
        'options' => ['placeholder' => 'الرجاء اختيار اسم العملة ...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple' => false
        ],
    ]);
    ?>

    <?=
    $form->field($model, 'min_date')->widget(
        DatePicker::class,
        [
            'language' => 'ar',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'todayBtn' => true,
            ]
        ]
    );
    ?>
    <?=
    $form->field($model, 'max_date')->widget(
        DatePicker::class,
        [
            'language' => 'ar',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'todayBtn' => true,
            ]
        ]
    );
    ?>

    <?= $form->field($model, 'allData')->checkBox(['checked' => false]) ?>


    <br>
    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Run'), ['class' => 'btn btn-success btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>' . ' ' . Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger btn-lg']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>