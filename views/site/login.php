<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

// يعتمد على التخطيط main-login (يوفّر <html> و Bootstrap 5 و AppAsset) — لا حاجة لوسوم html/head/body هنا
$this->title = Yii::t('app', 'Sign in');

$this->registerCss(<<<'CSS'
.login-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem; }
.login-card { width: 100%; max-width: 26rem; border: 0; box-shadow: 0 .5rem 1.5rem rgba(0, 0, 0, .12); }
.login-badge { width: 4rem; height: 4rem; }
.login-card .form-control { min-height: 3rem; }
.login-card .input-group-text { min-width: 3rem; justify-content: center; }
.login-card .field-loginform-username, .login-card .field-loginform-password { margin-bottom: 1rem; }
CSS);
?>

<div class="card login-card">
    <div class="card-body p-4 p-sm-5">

        <div class="text-center mb-4">
            <span class="login-badge d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary mb-3">
                <i class="bi bi-shop fs-2"></i>
            </span>
            <h1 class="h4 fw-bold mb-1">شاشة تسجيل الدخول</h1>
            <p class="text-body-secondary small mb-0">أدخل بيانات حسابك للمتابعة</p>
        </div>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'enableClientValidation' => false,
        ]); ?>

        <?= $form->field($model, 'username', [
            'template' => '<div class="input-group"><span class="input-group-text"><i class="bi bi-person"></i></span>{input}</div>{error}',
        ])->textInput([
            'placeholder' => $model->getAttributeLabel('username'),
            'autofocus' => true,
            'autocomplete' => 'username',
            'autocapitalize' => 'none',
            'spellcheck' => 'false',
        ])->label(false) ?>

        <?= $form->field($model, 'password', [
            'template' => '<div class="input-group"><span class="input-group-text"><i class="bi bi-lock"></i></span>{input}'
                . '<button type="button" class="btn btn-outline-secondary" id="toggle-password" tabindex="-1" aria-label="إظهار / إخفاء كلمة المرور">'
                . '<i class="bi bi-eye"></i></button></div>{error}',
        ])->passwordInput([
            'placeholder' => $model->getAttributeLabel('password'),
            'autocomplete' => 'current-password',
        ])->label(false) ?>

        <div id="caps-warning" class="small text-warning d-none mb-3">
            <i class="bi bi-exclamation-triangle"></i> زر Caps Lock مفعّل
        </div>

        <?= Html::submitButton(
            '<i class="bi bi-box-arrow-in-left"></i> ' . Yii::t('app', 'Sign in'),
            ['class' => 'btn btn-primary btn-lg w-100 mt-2', 'name' => 'login-button', 'id' => 'login-submit']
        ) ?>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<?php
$this->registerJs(<<<'JS'
(function () {
    var $pwd = $('#loginform-password');

    // إظهار / إخفاء كلمة المرور
    $('#toggle-password').on('click', function () {
        var show = $pwd.attr('type') === 'password';
        $pwd.attr('type', show ? 'text' : 'password');
        $(this).find('i').toggleClass('bi-eye', !show).toggleClass('bi-eye-slash', show);
        $pwd.trigger('focus');
    });

    // تنبيه Caps Lock
    $pwd.on('keyup keydown', function (e) {
        if (e.originalEvent && e.originalEvent.getModifierState) {
            $('#caps-warning').toggleClass('d-none', !e.originalEvent.getModifierState('CapsLock'));
        }
    });

    // منع الضغط المزدوج وإظهار حالة الانتظار
    $('#login-form').on('submit', function () {
        $('#login-submit').prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm"></span> جارٍ الدخول ...');
    });
})();
JS, View::POS_READY, 'login-js');
