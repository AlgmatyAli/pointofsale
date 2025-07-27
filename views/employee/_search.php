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
        <div class="col-lg-6">
            <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>

            <?= $form->field($model, 'name') ?>

            <div class="form-group">
                <?= Html::a('<i class="fa fa-fw fa-eraser"></i>' . ' ' . Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger']) ?>
                <?php echo Html::button('<i class="fa fa-fw fa-plus"></i>' . ' ' . Yii::t('app', 'New Create'), ['value' => Url::to(['employee/create']), 'class' => 'btn btn-success popup']); ?>
                <?= Html::submitButton('<i class="fa fa-fw fa-search"></i>' . ' ' . Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
            </div>

        </div>
        
    </div>
    <?php ActiveForm::end(); ?>

</div>