<?php

use app\components\WhatsappHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $columns */
/** @var array $promised */
/** @var string|null $user */
/** @var string|null $tag */
/** @var bool $all */
/** @var array $users */
/** @var array $tags */
/** @var app\models\CrmSetting|null $setting */

$this->title = 'لوحة التحصيل';
$this->params['breadcrumbs'][] = 'متابعة الزبائن';
$this->params['breadcrumbs'][] = $this->title;
$u = Yii::$app->user;
$canState = $u->can('crmStateChange');
$canCard = $u->can('crmCardView');
$csrf = Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken);
$today = date('Y-m-d');
?>
<div class="crm-board-page">
    <?= $this->render('_nav') ?>

    <?= Html::beginForm(['board'], 'get', ['class' => 'd-flex flex-wrap gap-2 align-items-end mb-3']) ?>
        <?php if ($all): ?>
            <div>
                <label class="form-label small mb-1" for="b-user">المسؤول</label>
                <?= Html::dropDownList('user', $user, ['' => 'الكل', 'none' => 'بلا مسؤول'] + $users, ['id' => 'b-user', 'class' => 'form-select form-select-sm']) ?>
            </div>
        <?php endif; ?>
        <?php if ($tags): ?>
            <div>
                <label class="form-label small mb-1" for="b-tag">المجموعة</label>
                <?= Html::dropDownList('tag', $tag, ['' => 'كل الزبائن'] + $tags, ['id' => 'b-tag', 'class' => 'form-select form-select-sm']) ?>
            </div>
        <?php endif; ?>
        <button class="btn btn-sm btn-outline-primary">عرض</button>
        <small class="ms-auto text-body-secondary">آخر تحديث للأرصدة: <?= Html::encode($setting->lastRunDate ?? '—') ?></small>
    <?= Html::endForm() ?>

    <div class="crm-board">
        <?php foreach ($columns as $key => $col): ?>
            <?php $sum = array_sum(array_map(fn($r) => (float) $r->balance, $col['items'])); ?>
            <section class="crm-col">
                <header class="p-2 border-bottom border-3 border-<?= Html::encode($col['color']) ?>">
                    <div class="fw-semibold"><?= Html::encode($col['title']) ?></div>
                    <small class="text-body-secondary"><?= count($col['items']) ?> زبون · <?= WhatsappHelper::money($sum) ?></small>
                </header>
                <div class="crm-col-body p-2 d-grid gap-2">
                    <?php foreach ($col['items'] as $r): ?>
                        <?php $due = $key === 'promise' && isset($promised[$r->clientId]) && $promised[$r->clientId]['d'] <= $today; ?>
                        <div class="card card-body p-2 shadow-sm<?= $due ? ' border-danger bg-danger-subtle' : '' ?>">
                            <div class="d-flex justify-content-between gap-1">
                                <strong class="small"><?= $canCard ? Html::a(Html::encode($r->client->name ?? ('#' . $r->clientId)), ['card', 'id' => $r->clientId]) : Html::encode($r->client->name ?? '') ?></strong>
                                <span class="small fw-bold text-nowrap"><?= WhatsappHelper::money($r->balance) ?></span>
                            </div>
                            <div class="small text-body-secondary d-flex justify-content-between">
                                <span><?= $r->days !== null ? (int) $r->days . ' يوم' : '' ?></span>
                                <span><?= Html::encode($r->user->username ?? '') ?></span>
                            </div>
                            <?php if (isset($promised[$r->clientId])): ?>
                                <div class="small <?= $due ? 'text-danger fw-semibold' : 'text-primary' ?>">وعد <?= WhatsappHelper::money($promised[$r->clientId]['amount']) ?> في <?= Html::encode($promised[$r->clientId]['d']) ?><?= $due ? ' · حلّ الموعد' : '' ?></div>
                            <?php endif; ?>
                            <?php if ($canState && $key !== 'promise'): ?>
                                <form method="post" action="<?= Url::to(['state', 'id' => $r->clientId]) ?>" class="mt-1">
                                    <?= $csrf ?>
                                    <?= Html::dropDownList('state', (int) $r->state, app\models\CrmClient::STATES, [
                                        'class' => 'form-select form-select-sm', 'aria-label' => 'نقل إلى', 'onchange' => 'this.form.submit()',
                                    ]) ?>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if (!$col['items']): ?><div class="small text-body-secondary text-center py-3">لا أحد</div><?php endif; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
    <p class="small text-body-secondary mt-2">المراحل تُحسب تلقائياً من الأيام منذ آخر دفعة. «متنازع عليه» يوقف المهام التلقائية، و«مصعّد للمدير» يضع الزبون في عمود المدير.</p>
</div>
