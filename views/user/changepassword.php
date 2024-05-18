<?php 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'Change Password') ;
// $this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-changepassword">
<br><h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <p><h5>الرجاء تعبئة كافة الحقول لتغيير كلمة المرور</h5></p>
    <br>
    <?php $form = ActiveForm::begin([
        'id'=>'changepassword-form',
        'options'=>['class'=>'form-horizontal'],
        'fieldConfig'=>[
            'template'=>"{label}\n<div class=\"col-lg-3\">
                        {input}</div>\n<div class=\"col-lg-5\">
                        {error}</div>",
            'labelOptions'=>['class'=>'col-lg-2 control-label'],
        ],
    ]); ?>
     <?= $form->field($model,'oldpass',['inputOptions'=>['placeholder'=>Yii::t('app','Old Password')]])->passwordInput() ?>
     
     <?= $form->field($model,'newpass',['inputOptions'=>['placeholder'=>Yii::t('app','New Password')]])->passwordInput() ?>
     
     <?= $form->field($model,'repeatnewpass',['inputOptions'=>['placeholder'=>Yii::t('app','Repeat New Password')]])->passwordInput() ?>
    

        <div class="form-group">
        <!-- class="col-lg-offset-2 col-lg-11" -->
            <div class="col-lg-offset-2 col-lg-11">
                <?= Html::submitButton(Yii::t('app', 'Change Password') ,[
                    'class'=>'btn btn-primary'
                ]) ?>
                
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>