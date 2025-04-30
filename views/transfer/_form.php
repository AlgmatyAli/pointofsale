<?php

use app\models\Currancy;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Branches;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Transfer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transfer-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php
    echo $form->field($model, 'fromBr')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Branches::find()->all(), 'id', 'name'),
        'language' => 'ar',
        'options' => ['placeholder' => 'اختيار اسم الفرع المسحوب منه...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple' => false,
        ],
    ]);
    ?>
    <?php
    echo $form->field($model, 'toBr')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Branches::find()->all(), 'id', 'name'),
        'language' => 'ar',
        'options' => ['placeholder' => 'اختيار اسم الفرع المودع له...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple' => false,
        ],
    ]);
    ?>

    <?= $form->field($model, 'value')->textInput() ?>

    <?php
    echo $form->field($model, 'at')->widget(
        DatePicker::className(),
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

    <?= $form->field($model, 'type')->dropDownList(['1' => ' صادر', '2' => 'وارد'], ['prompt' => 'اختيار نوع الحركة']) ?>

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

    <?= $form->field($model, 'why')->textarea(['rows' => 6]) ?>
    <br>
    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Clear'), ['/safe/create'], ['class' => 'btn btn-danger btn-lg']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>