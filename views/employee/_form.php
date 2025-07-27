<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Employee */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'salary')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dayOfWork')->textInput(['maxlength' => true]) ?>

    <?php
    echo $form->field($model, 'startWork')->widget(
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

    <?= $form->field($model, 'notes')->textarea(['rows' => 3, 'maxlength' => true]) ?>

    <?= $form->field($model, 'state')->dropDownList(['0' => 'يعمل', '1' => 'موقوف'], ['prompt' => 'اختيار الحالة']) ?>

    <div class="form-group">
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>' . ' ' . Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-default']) ?>

        <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php
    $script = <<< JS
    $('form').on('beforeSubmit', function(e) {
        e.preventDefault();
        var form = $(this);
        if (form.find('.has-error').length) {
            return false;
        }
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            success: function(result) {
                if(result === 'success') {
                    $.pjax.reload({container: '#grid-id'});
                    form.trigger('reset');
                } else {
                    alert('Error occurred while submitting the form');
                }
            },
            error: function() {
                alert('Error occurred while submitting the form');
            }
        });
        return false;
    });
    JS;
    $this->registerJs($script);
    ?>
</div>