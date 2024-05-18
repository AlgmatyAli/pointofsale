<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Sign In';
 
?>

<!DOCTYPE html>
<html lang="ar">
<head>
	<title>Login V11</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100 p-l-50 p-r-50 p-t-77 p-b-30">
				
					 <span class="login100-form-title p-b-55">
						شاشة تسجيل الدخول
                    </span>

                    <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>
                    <div class="wrap-input100 validate-input m-b-16">
                        <?= $form
                        ->field($model, 'username')
                        ->label(false)
                        ->textInput(['placeholder' => $model->getAttributeLabel('username'), 'class' => 'input100']) ?>						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<span class="lnr lnr-lock"></span>
						</span>
					</div>
                    
                    <div class="wrap-input100 validate-input m-b-16" data-validate = "Password is required">
                    <?= $form
                        ->field($model, 'password')
                        ->label(false)
                        ->passwordInput(['placeholder' => $model->getAttributeLabel('password'), 'class' => 'input100']) ?>
                        <span class="focus-input100"></span>
						<span class="symbol-input100">
							<span class="lnr lnr-lock"></span>
						</span>
					</div>
                     <br> <br> <br>
                   
					<div class="container-login100-form-btn p-t-25">
					<?= Html::submitButton(Yii::t('app','Sign in'), 
                           ['class' => 'login100-form-btn',
                            'name' => 'login-button']) ?>
					</div>
					
                    
                    <?php ActiveForm::end(); ?>
        
                  <div class="text-center w-full p-t-42 p-b-22">
						<span class="txt1">
							<!-- Or login with -->
						</span>
					</div>
                    
					<div class="text-center w-full p-t-115">
						<span class="txt1">
							<!-- Not a member? -->
						</span>

						<a class="txt1 bo1 hov1" href="#">
							<!-- Sign up now							 -->
						</a>
					</div>
			</div>
		</div>
	</div>
</body>
<?php  $this->registerCssFile("@web/css/util.css"); ?>

</html>