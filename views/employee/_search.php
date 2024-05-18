<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\EmployeeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-search">
    <div class="row">
    <div class="col-lg-1"></div>
    <div class="col-lg-4">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'salary') ?>

   <div class="form-group">
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>'.' '.Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-plus"></i>'.' '.Yii::t('app', 'New Create'), ['create'], ['class' => 'btn btn-success btn-lg']) ?>
        <?= Html::submitButton('<i class="fa fa-fw fa-search"></i>'.' '.Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-lg']) ?>
   </div>

    </div>
    <div class="col-lg-4">
    <?= $form->field($model, 'dayOfWork') ?>

    <?= $form->field($model, 'salaryByDay') ?>

    <?php // echo $form->field($model, 'startWork') ?>

    <?php // echo $form->field($model, 'notes') ?>

    <?php // echo $form->field($model, 'state') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
