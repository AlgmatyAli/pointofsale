<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
<div class='row'>
<div class='col-md-4'>
    
    <?= $form->field($model, 'username') ?>

</div>
<!-- end of class = row -->
</div>  
   <br>
    <div class="form-group">
        <?= Html::a(Yii::t('app', 'Reset'), Url::toRoute(['index']), ['class' => 'btn btn-danger']) ?>
        <?= Html::a(Yii::t('app', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
