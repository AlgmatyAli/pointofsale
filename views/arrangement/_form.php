<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/** @var app\models\Arrangement $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="arrangement-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    echo $form->field($model, 'at')->widget(
        DatePicker::class,
        [
            'language' => 'ar',
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
            ]
        ]
    );
    ?>
    <br>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>
    <br>
    <?php ActiveForm::end(); ?>

</div>