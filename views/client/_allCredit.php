<?php

use app\models\Currancy;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Client;
use app\models\CompanyInfo;
use kartik\daterange\DateRangePicker;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\ReceiptSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="credits">

    <?php $form = ActiveForm::begin(); ?>

    <?php 
    $currency = CompanyInfo::find()->one();
    $model->currency = $currency->currancy;
    echo $form->field($model, 'currency')->widget(Select2::class, [
        'data' => ArrayHelper::map(Currancy::find()->where('id = (select currancy from company_info)')->all(), 'id', 'name'),
        'language' => 'ar',
        'options' => ['placeholder' => 'الرجاء اختيار اسم العملة ...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple' => false,
        ],
    ]); ?>

    <?= $form->field($model, 'type')->dropDownList(['0' => 'زبائـن', '1' => 'موردين'], ['prompt' => 'اختيار نوع العرض']) ?>

    <br>
    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Run'), ['class' => 'btn btn-success btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>' . ' ' . Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger btn-lg']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>