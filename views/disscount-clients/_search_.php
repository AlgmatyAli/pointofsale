<?php

use app\models\Client;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\DisscountClientsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="disscount-clients-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index_'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="col-lg-4">
        <?= $form->field($model, 'client')->widget(Select2::class, [
            'data' => ArrayHelper::map(Client::find()
                ->where(['in', 'type', [1]])
                ->all(), 'id', 'name'),
            'language' => 'ar',
            'options' => ['placeholder' => 'الرجاء اختيار اسم المورد ...'],
            'pluginOptions' => [
                'allowClear' => true,
                'multiple' => false
            ],
        ]);
        ?>

        <br>
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-fw fa-search"></i>' . ' ' . Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fa fa-fw fa-eraser"></i>' . ' ' . Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger']) ?>
        </div>
    </div>
    <div class="col-lg-4">
        <?php
        echo '<label class="control-label">تاريخ الايصال</label>';
        echo DateRangePicker::widget([
            'model' => $model,
            'attribute' => 'at',
            'language' => 'en',
            'convertFormat' => false,
            'pluginOptions' => [
                'timePicker' => false,
                'timePickerIncrement' => 30,
                'locale' => [
                    'format' => 'YYYY-MM-DD'
                ]
            ]
        ]);
        ?>
    </div>
    <div class="col-lg-4">
        <?= $form->field($model, 'value') ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
</div>