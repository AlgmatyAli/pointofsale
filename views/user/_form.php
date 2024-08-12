<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\file\FileInput;
use app\models\Branches;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="user-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class='row'>
        <div class='col-md-5'>

            <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'isActive')->dropDownList(['active' => 'active', 'disabled' => 'disabled',], ['prompt' => 'Select User Status...']) ?>

            <?php
            echo $form->field($model, 'branch')->widget(Select2::class, [
                'data' => ArrayHelper::map(Branches::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => ['placeholder' => 'الرجاء اختيار اسم الفرع ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false
                ],
            ]);
            ?>

            <?= $form->field($model, 'permission')->widget(\kartik\widgets\Select2::class, [
                'data' => \yii\helpers\ArrayHelper::map(\app\models\AuthItem::find()->where(['!=', 'type', '2'])->asArray()->all(), 'type', 'name'),
                'options' => ['placeholder' => Yii::t('app', 'Choose User Type')],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>

            <?= $form->field($model, 'client')->widget(\kartik\widgets\Select2::class, [
                'data' => \yii\helpers\ArrayHelper::map(\app\models\Client::find()->orderBy('id')->asArray()->all(), 'id', 'name'),
                'options' => ['placeholder' => Yii::t('app', 'Choose User Type')],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                ],
            ]);
            ?>
            <br>
            <div class="form-group">

                <?= Html::a('<i class="fa fa-fw fa-eraser"></i>' . ' ' . "Erase all", Url::toRoute(['create']), ['class' => 'btn btn-danger']) ?>
                <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Create') : '<i class="fa fa-fw fa-edit"></i>' . ' ' . Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

            </div>
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
            <?php // echo $form->field($model, 'file')->widget(FileInput::class,['options' => ['accept' => '*/*','id'=>'files'],]);  
            ?>
        </div>
        <div class='col-md-1'></div>

        <div class='col-md-3'>
            <br>
            <div class="panel panel-primary">
                <div class="panel-heading">صلاحيات اضافية</div>
                <br>
                <div class="row">
                    <div class='col-md-1'></div>
                    <div class='col-md-11'>
                        <?= $form->field($model, 'seeCostPrice')->checkBox(['checked' => false]) ?>
                        <?= $form->field($model, 'editSalePrice')->checkBox(['checked' => false]) ?>
                        <?= $form->field($model, 'makeDiscount')->checkBox(['checked' => false]) ?>
                        <?= $form->field($model, 'printPurtchaseInvoice')->checkBox(['checked' => false]) ?>
                        <?= $form->field($model, 'seeOtherBranchQ')->checkBox(['checked' => false]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class='col-md-1'></div>
                    <div class='col-md-7'>
                        <hr>
                        <?= $form->field($model, 'maxDiscount')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'maxExpenses')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'maxReceipt')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class='col-md-4'></div>
                </div>

            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>