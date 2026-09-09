<?php

use app\models\Currancy;
use app\models\CompanyInfo;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */

/** @var app\models\Client $model */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="histrans">

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

    <?php
    $catList = [
        0 => 'دائنون',
        1 => 'مدينون',
        // 2 => 'كلاهما'
    ];

    echo $form->field($model, 'indebtedness')->dropDownList($catList, ['id' => 'indebtedness-id', 'prompt' => 'اختيار نوع العرض']);
    ?>

    <br>
    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Run'), ['class' => 'btn btn-success btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>' . ' ' . Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger btn-lg']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>