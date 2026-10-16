<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/** @var yii\web\View $this */
/** @var yii\base\Model $model */

$this->title = Yii::t('app', 'Change Password');

$this->registerCss(<<<'CSS'
.site-changepassword .cp-card { max-width: 34rem; }
.site-changepassword .form-control { min-height: 2.9rem; }
.site-changepassword .cp-meter { height: .35rem; }
CSS);

// حقل كلمة مرور بزر إظهار/إخفاء
$passwordField = function (string $attribute, string $placeholder, string $autocomplete) use ($model) {
    return [
        'template' => '{label}<div class="input-group"><span class="input-group-text"><i class="bi bi-lock"></i></span>{input}'
            . '<button type="button" class="btn btn-outline-secondary cp-toggle" tabindex="-1" aria-label="إظهار / إخفاء">'
            . '<i class="bi bi-eye"></i></button></div>{error}',
        'inputOptions' => [
            'placeholder' => $placeholder,
            'autocomplete' => $autocomplete,
            'class' => 'form-control',
        ],
    ];
};

$newId = Html::getInputId($model, 'newpass');
$repeatId = Html::getInputId($model, 'repeatnewpass');
?>

<div class="site-changepassword">

    <div class="card cp-card mx-auto">
        <div class="card-body p-4">

            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary"
                    style="width:3.25rem;height:3.25rem"><i class="bi bi-shield-lock fs-3"></i></span>
                <div>
                    <h1 class="h5 fw-bold mb-0"><?= Html::encode($this->title) ?></h1>
                    <br>
                    <div class="text-body-secondary small">الرجاء تعبئة كافة الحقول لتغيير كلمة المرور</div>
                </div>
            </div>

            <?php $form = ActiveForm::begin(['id' => 'changepassword-form']); ?>

            <?= $form->errorSummary($model, ['class' => 'alert alert-danger py-2 small']) ?>

            <?= $form->field($model, 'oldpass', $passwordField('oldpass', Yii::t('app', 'Old Password'), 'current-password'))->passwordInput() ?>

            <hr class="opacity-25">

            <?= $form->field($model, 'newpass', $passwordField('newpass', Yii::t('app', 'New Password'), 'new-password'))->passwordInput() ?>

            <!-- مؤشر قوة كلمة المرور: للإرشاد فقط ولا يغيّر قواعد التحقق -->
            <div class="mb-3" id="cp-strength" hidden>
                <div class="progress cp-meter">
                    <div class="progress-bar" id="cp-strength-bar" style="width:0"></div>
                </div>
                <div class="small text-body-secondary mt-1" id="cp-strength-text"></div>
            </div>

            <?= $form->field($model, 'repeatnewpass', $passwordField('repeatnewpass', Yii::t('app', 'Repeat New Password'), 'new-password'))->passwordInput() ?>

            <div class="small mb-3" id="cp-match" hidden></div>

            <?= Html::submitButton(
                '<i class="bi bi-check2-circle"></i> ' . Yii::t('app', 'Change Password'),
                ['class' => 'btn btn-primary btn-lg w-100', 'id' => 'cp-submit']
            ) ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$this->registerJs(
    'var cpIds = ' . Json::htmlEncode(['new' => '#' . $newId, 'repeat' => '#' . $repeatId]) . ';',
    View::POS_READY,
    'cp-ids'
);
$this->registerJs(<<<'JS'
(function () {
    // إظهار / إخفاء
    $('#changepassword-form').on('click', '.cp-toggle', function () {
        var $input = $(this).closest('.input-group').find('input');
        var show = $input.attr('type') === 'password';
        $input.attr('type', show ? 'text' : 'password');
        $(this).find('i').toggleClass('bi-eye', !show).toggleClass('bi-eye-slash', show);
    });

    // قوة كلمة المرور (إرشادي)
    var levels = [
        {t: 'ضعيفة جداً', c: 'bg-danger', w: 20},
        {t: 'ضعيفة', c: 'bg-danger', w: 40},
        {t: 'متوسطة', c: 'bg-warning', w: 60},
        {t: 'جيدة', c: 'bg-info', w: 80},
        {t: 'قوية', c: 'bg-success', w: 100}
    ];
    function score(p) {
        var s = 0;
        if (p.length >= 8) { s++; }
        if (p.length >= 12) { s++; }
        if (/[a-z]/.test(p) && /[A-Z]/.test(p)) { s++; }
        if (/\d/.test(p)) { s++; }
        if (/[^A-Za-z0-9]/.test(p)) { s++; }
        return Math.min(s, 4);
    }

    var $new = $(cpIds['new']), $rep = $(cpIds.repeat);

    function updateStrength() {
        var v = $new.val();
        $('#cp-strength').prop('hidden', v === '');
        if (v === '') { return; }
        var l = levels[score(v)];
        $('#cp-strength-bar').attr('class', 'progress-bar ' + l.c).css('width', l.w + '%');
        $('#cp-strength-text').text('قوة كلمة المرور: ' + l.t);
    }

    function updateMatch() {
        var a = $new.val(), b = $rep.val();
        var $m = $('#cp-match');
        if (b === '') { $m.prop('hidden', true); return; }
        var ok = (a === b);
        $m.prop('hidden', false)
          .attr('class', 'small mb-3 ' + (ok ? 'text-success' : 'text-danger'))
          .html(ok ? '<i class="bi bi-check-circle"></i> كلمتا المرور متطابقتان'
                   : '<i class="bi bi-x-circle"></i> كلمتا المرور غير متطابقتين');
    }

    $new.on('input', function () { updateStrength(); updateMatch(); });
    $rep.on('input', updateMatch);

    // منع الإرسال المزدوج: beforeSubmit لا يُطلق إلا بعد نجاح التحقق من جهة العميل، فلا يبقى الزر معطّلاً عند وجود أخطاء
    $('#changepassword-form').on('beforeSubmit', function () {
        $('#cp-submit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> جارٍ الحفظ ...');
    });
})();
JS, View::POS_READY, 'cp-js');
