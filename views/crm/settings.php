<?php

use app\models\CrmLevel;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\CrmSetting $setting */
/** @var CrmLevel[] $levels */
/** @var CrmLevel $newLevel */
/** @var array $users */

$this->title = 'إعدادات متابعة الزبائن';
$this->params['breadcrumbs'][] = 'متابعة الزبائن';
$this->params['breadcrumbs'][] = 'الإعدادات';
$csrf = Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken);
?>
<div class="crm-settings" style="max-width: 900px">
    <?= $this->render('_nav') ?>
    <h1 class="h4"><?= Html::encode($this->title) ?></h1>

    <div class="card card-body mb-4">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($setting, 'enabled')->checkbox() ?>
        <?= $form->field($setting, 'defaultUserId')->dropDownList($users, ['prompt' => 'لا أحد'])
            ->hint('تذهب إليه مهام الزبائن الذين لم يُعيَّن لهم مسؤول.') ?>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <?= Html::submitButton('حفظ الإعدادات', ['class' => 'btn btn-primary']) ?>
            <?php if ($setting->enabled): ?>
                <?= Html::a('<i class="bi bi-arrow-clockwise"></i> تحديث الآن', ['run-now'], ['class' => 'btn btn-outline-secondary', 'data' => ['method' => 'post']]) ?>
                <small class="text-body-secondary">آخر تحديث: <?= Html::encode($setting->lastRunDate ?? '—') ?>. يعمل تلقائياً مرة كل يوم مع أول فتح لشاشات المتابعة.</small>
            <?php endif; ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <h2 class="h5">مراحل المتابعة</h2>
    <p class="text-body-secondary">يدخل الزبون المرحلة حين تمر عليه هذه الأيام بدون دفع (منذ آخر إيصال قبض، أو منذ أقدم فاتورة آجلة إن لم يدفع أبداً). عند دخول مرحلة جديدة تُنشأ مهمة للمسؤول.</p>
    <div class="table-responsive">
    <table class="table align-middle">
        <thead><tr><th>اسم المرحلة</th><th>بعد كم يوم</th><th>الإجراء</th><th>اللون</th><th></th></tr></thead>
        <tbody>
        <?php foreach (array_merge($levels, [$newLevel]) as $l): $id = $l->isNewRecord ? null : $l->id; $f = 'lv' . ($id ?: 'new'); ?>
            <tr>
                <td><input form="<?= $f ?>" type="text" name="CrmLevel[name]" value="<?= Html::encode($l->name) ?>" class="form-control form-control-sm" required maxlength="100" aria-label="اسم المرحلة" <?= $id ? '' : 'placeholder="مرحلة جديدة"' ?>></td>
                <td style="width:110px"><input form="<?= $f ?>" type="number" name="CrmLevel[days]" value="<?= Html::encode($l->days) ?>" class="form-control form-control-sm" min="1" required aria-label="الأيام"></td>
                <td><?= Html::dropDownList('CrmLevel[action]', $l->action, CrmLevel::ACTIONS, ['form' => $f, 'class' => 'form-select form-select-sm', 'aria-label' => 'الإجراء']) ?></td>
                <td><?= Html::dropDownList('CrmLevel[color]', $l->color, CrmLevel::COLORS, ['form' => $f, 'class' => 'form-select form-select-sm', 'aria-label' => 'اللون']) ?></td>
                <td class="text-nowrap">
                    <form id="<?= $f ?>" method="post" action="<?= Url::to(['level-save', 'id' => $id]) ?>" class="d-inline"><?= $csrf ?>
                        <button class="btn btn-sm <?= $id ? 'btn-outline-secondary' : 'btn-primary' ?>"><?= $id ? 'حفظ' : 'إضافة' ?></button>
                    </form>
                    <?php if ($id): ?>
                        <?= Html::a('حذف', ['level-delete', 'id' => $id], ['class' => 'btn btn-sm btn-outline-danger',
                            'data' => ['method' => 'post', 'confirm' => 'حذف مرحلة «' . $l->name . '»؟']]) ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <p class="small text-body-secondary">اختياري: لتنفيذ التحديث ليلاً بدل أول فتح، أضف Cron يومي: <code>php yii crm/daily</code></p>
</div>
