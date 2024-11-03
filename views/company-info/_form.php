<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use dosamigos\ckeditor\CKEditor;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Currancy;
use kartik\widgets\SwitchInput;

/* @var $this yii\web\View */
/* @var $model app\models\CompanyInfo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-info-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'], 'id' => 'company-info-form']); ?>

    <div class="row">

        <div class="col-md-4">

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'work')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'address')->textarea(['rows' => 9]) ?>

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
            <?= $form->field($model, 'rate')->textInput(['maxlength' => true])->label('نسبة  الزيادة') ?>

        </div>

        <div class='col-md-4'>

            <?= $form->field($model, 'phone1')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'phone2')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'phone3')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'fax')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'skin')->dropDownList([
                'skin-blue' => 'skin-blue',
                'skin-blue-light' => 'skin-blue-light',
                'skin-yellow' => 'skin-yellow',
                'skin-yellow-light' => 'skin-yellow-light',
                'skin-green' => 'skin-green',
                'skin-green-light' => 'skin-green-light',
                'skin-red' => 'skin-red',
                'skin-red-light' => 'skin-red-light'
            ], ['prompt' => 'اختيار لون الخلفية']) ?>

            <?= $form->field($model, 'criteriaـvalue')->textInput(['maxlength' => true])->label('قيمة البحث') ?>

        </div>

        <div class='col-md-3'>
            <?php if (empty($model->path)) {

                echo $form->field($model, 'file')->widget(FileInput::class, ['options' => ['accept' => 'image/*'],]);
            } else {
                $allimage[] = Html::img($model->path,  ['class' => 'file-preview-image']);

                echo $form->field($model, 'file')->widget(
                    FileInput::class,
                    [
                        'options' => ['accept' => 'image/*'],
                        'pluginOptions' => [
                            'initialPreview' => [$allimage],
                            'overwriteInitial' => false
                        ],
                    ]
                );
            }
            ?>
        </div>

    </div>

    <div class='row'>
        <div class='col-md-6'>
        <?= $form->field($model, 'terms')->textarea(['rows' => 6]) ?>

        </div>
        <div class='col-md-2' style="height: 3%;">
            <?php
            echo $form->field($model, 'searchById')->widget(SwitchInput::class, [
                'pluginOptions' => [
                    'size' => 'small',
                    'onColor' => 'success',
                    'offColor' => 'danger',
                ]
            ]); ?>
            <?php
            echo $form->field($model, 'repeatCategory')->widget(SwitchInput::class, [
                'pluginOptions' => [
                    'size' => 'small',
                    'onColor' => 'success',
                    'offColor' => 'danger',
                ]
            ]);
            ?>
            <?php
            echo $form->field($model, 'payWayCash')->widget(SwitchInput::class, [
                'pluginOptions' => [
                    'size' => 'small',
                    'onColor' => 'success',
                    'offColor' => 'danger',
                ]
            ]); ?>
            <?php
            echo $form->field($model, 'invoiceState')->widget(SwitchInput::class, [
                'pluginOptions' => [
                    'size' => 'small',
                    'onColor' => 'success',
                    'offColor' => 'danger',
                ]
            ]); ?>
            <?php
            echo $form->field($model, 'waitQnty')->widget(SwitchInput::class, [
                'pluginOptions' => [
                    'size' => 'small',
                    'onColor' => 'success',
                    'offColor' => 'danger',
                ]
            ]); ?>
                        <?php
            echo $form->field($model, 'zeroQnty')->widget(SwitchInput::class, [
                'pluginOptions' => [
                    'size' => 'small',
                    'onColor' => 'success',
                    'offColor' => 'danger',
                ]
            ]); ?>
        </div>
    </div>
    <?php //echo $form->field($model, 'file')->widget(FileInput::class,['options' => ['accept' => '*/*','id'=>'files'],]);  
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-fw fa-save"></i>' . '' . Yii::t('app', 'Create') : '<i class="fa fa-fw fa-save"></i>' . '' . Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success btn-lg' : 'btn btn-success btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Cancel'), ['/company-info/index'], ['class' => 'btn btn-primary btn-lg']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>