<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\CompanyInfo;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Sign In';
 $path = CompanyInfo::find()->select(['path','name'])->one(); 

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-user form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>

<div class="login-box">
    <div>
        <br><br><br><br>
        <a href="9#" style="color:white"><b></a>
    </div>


    <!-- /.login-logo login-box-body-->
    <div class="login-form">
        <p class="login-box-msg ">
           <?php echo $path->name ?>
        </p>
        
        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'username', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>
        <br>
        <div class="row">
            <!-- /.col -->
            <div class="col-xs-12">
                <?= Html::submitButton('Sign in', ['class' => 'btn btn-success btn-block btn-flat', 'name' => 'login-button']) ?>

        <?php ActiveForm::end(); ?>
    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->
