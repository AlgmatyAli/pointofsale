<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\BranchesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="branches-search">

     <div class="row">
   <div class="col-lg-2"></div>
   <div class="col-lg-4">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'name') ?>
    <br>
    <div class="form-group">
        <?= Html::a('<i class="fa fa-fw fa-plus"></i>'.' '.Yii::t('app', 'New Create'), ['create'], ['class' => 'btn btn-success btn-lg']) ?>
        <?= Html::submitButton('<i class="fa fa-fw fa-search"></i>'.' '.Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>'.' '.Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger btn-lg']) ?>
    </div>

    </div>
    </div>
    <br>

    <?php ActiveForm::end(); ?>

</div>
