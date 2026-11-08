<?php

use app\components\WhatsappHelper;
use app\models\CrmActivity;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var CrmActivity[] $tasks */
/** @var CrmActivity|null $selected */
/** @var float $balance */
/** @var string|null $user */
/** @var string $when */
/** @var bool $all */
/** @var array $users */

$this->title = 'مهامي اليوم';
$this->params['breadcrumbs'][] = 'متابعة الزبائن';
$this->params['breadcrumbs'][] = $this->title;
$u = Yii::$app->user;
$today = date('Y-m-d');
$filter = ['user' => $user, 'when' => $when];
?>
<div class="crm-tasks">
    <?= $this->render('_nav') ?>

    <form method="get" action="<?= Url::to(['tasks']) ?>" class="d-flex flex-wrap gap-2 align-items-end mb-3">
        <?php if ($all): ?>
            <div>
                <label class="form-label small mb-1" for="f-user">المكلّف</label>
                <?= Html::dropDownList('user', $user, ['' => 'الكل', 'none' => 'بلا مكلّف'] + $users, ['id' => 'f-user', 'class' => 'form-select form-select-sm']) ?>
            </div>
        <?php endif; ?>
        <div>
            <label class="form-label small mb-1" for="f-when">الفترة</label>
            <?= Html::dropDownList('when', $when, ['due' => 'اليوم والمتأخر', 'later' => 'القادمة', 'all' => 'كل المفتوحة'], ['id' => 'f-when', 'class' => 'form-select form-select-sm']) ?>
        </div>
        <button class="btn btn-sm btn-outline-primary">عرض</button>
        <span class="ms-auto text-body-secondary small"><?= count($tasks) ?> مهمة</span>
    </form>

    <?php if (!$tasks): ?>
        <div class="text-center text-body-secondary py-5 border rounded">
            <i class="bi bi-check2-circle fs-1 d-block mb-2 text-success"></i>
            لا توجد مهام في هذه الفترة.
        </div>
    <?php else: ?>
    <div class="row g-3">
        <div class="col-lg-5">
            <div class="list-group crm-pane">
                <?php foreach ($tasks as $t): ?>
                    <?php $late = $t->dueDate < $today; ?>
                    <a href="<?= Url::to(['tasks', 'id' => $t->id] + $filter) ?>"
                       class="list-group-item list-group-item-action crm-task<?= $selected && $selected->id == $t->id ? ' active' : '' ?>">
                        <div class="d-flex justify-content-between gap-2">
                            <strong><i class="bi <?= CrmActivity::ICONS[$t->type] ?? 'bi-dot' ?>"></i> <?= Html::encode($t->client->name ?? ('#' . $t->clientId)) ?></strong>
                            <small class="<?= $late ? 'text-danger fw-bold' : 'text-body-secondary' ?>"><?= Html::encode($t->dueDate) ?></small>
                        </div>
                        <div class="small text-body-secondary d-flex justify-content-between gap-2">
                            <span><?= Html::encode(CrmActivity::TYPES[$t->type] ?? $t->type) ?><?= $all ? ' · ' . Html::encode($t->user->username ?? 'بلا مكلّف') : '' ?></span>
                            <?php if ($t->crm): ?><span><?= WhatsappHelper::money($t->crm->balance) ?></span><?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-lg-7">
            <?php if ($selected): $c = $selected->client; ?>
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="mb-0">
                            <?= $u->can('crmCardView') ? Html::a(Html::encode($c->name), ['card', 'id' => $c->id]) : Html::encode($c->name) ?>
                        </h5>
                        <small class="text-body-secondary"><?= Html::encode(trim(($c->mobile ?: '') . ' ' . ($c->phone ?: ''))) ?></small>
                    </div>
                    <div><?= $this->render('_badges', ['crm' => $selected->crm]) ?></div>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col"><div class="small text-body-secondary">الرصيد</div><div class="fs-5 fw-bold"><?= WhatsappHelper::money($balance) ?></div></div>
                        <div class="col"><div class="small text-body-secondary">أيام بدون دفع</div><div class="fs-5 fw-bold"><?= $selected->crm && $selected->crm->days !== null ? (int) $selected->crm->days : '—' ?></div></div>
                        <div class="col"><div class="small text-body-secondary">المهمة</div><div class="fs-6"><?= Html::encode(CrmActivity::TYPES[$selected->type] ?? '') ?></div></div>
                    </div>
                    <?php if ($selected->note): ?>
                        <p class="border rounded p-2 bg-body-tertiary"><?= nl2br(Html::encode($selected->note)) ?></p>
                    <?php endif; ?>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <?php if ($u->can('crmWhatsapp')): ?>
                            <?= Html::a('<i class="bi bi-whatsapp"></i> تذكير بالرصيد', ['whatsapp', 'id' => $c->id, 'task' => $selected->id], ['class' => 'btn btn-success', 'target' => '_blank', 'rel' => 'noopener']) ?>
                        <?php endif; ?>
                        <?php if ($c->mobile || $c->phone): ?>
                            <?= Html::a('<i class="bi bi-telephone"></i> اتصال', 'tel:' . preg_replace('/[^\d+]/', '', $c->mobile ?: $c->phone), ['class' => 'btn btn-outline-secondary']) ?>
                        <?php endif; ?>
                    </div>

                    <?php if ($u->can('crmTaskDone')): ?>
                    <form method="post" action="<?= Url::to(['task-done', 'id' => $selected->id]) ?>" class="border rounded p-3 mb-3">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        <?= Html::hiddenInput('fUser', $user) ?><?= Html::hiddenInput('fWhen', $when) ?>
                        <h6>إغلاق المهمة</h6>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small" for="t-result">النتيجة</label>
                                <?= Html::dropDownList('result', null, ['' => 'اختر...'] + array_combine(CrmActivity::RESULTS, CrmActivity::RESULTS), ['id' => 't-result', 'class' => 'form-select', 'required' => true]) ?>
                            </div>
                            <?php if ($u->can('crmTaskCreate')): ?>
                            <div class="col-md-6">
                                <label class="form-label small" for="t-next">متابعة تالية (اختياري)</label>
                                <input type="date" name="nextDate" id="t-next" class="form-control" min="<?= $today ?>">
                            </div>
                            <?php endif; ?>
                            <div class="col-12">
                                <label class="form-label small" for="t-note">ملاحظة</label>
                                <input type="text" name="note" id="t-note" class="form-control" maxlength="200">
                            </div>
                        </div>
                        <button class="btn btn-primary mt-2"><i class="bi bi-check2"></i> إغلاق المهمة</button>
                    </form>
                    <?php endif; ?>

                    <?php if ($u->can('crmPromiseCreate')): ?>
                    <details class="border rounded p-3 mb-3">
                        <summary class="fw-semibold">تسجيل وعد بالدفع</summary>
                        <?= $this->render('_promise_form', ['clientId' => $c->id, 'balance' => $balance]) ?>
                    </details>
                    <?php endif; ?>

                    <div class="d-flex flex-wrap gap-2 align-items-end">
                        <?php if ($u->can('crmAssign')): ?>
                        <form method="post" action="<?= Url::to(['task-assign', 'id' => $selected->id]) ?>" class="d-flex gap-2 align-items-end">
                            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                            <div>
                                <label class="form-label small mb-1" for="t-user">تحويل إلى</label>
                                <?= Html::dropDownList('userId', $selected->userId, ['' => 'بلا مكلّف'] + $users, ['id' => 't-user', 'class' => 'form-select form-select-sm']) ?>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary">تحويل</button>
                        </form>
                        <?php endif; ?>
                        <?php if ($u->can('crmTaskDelete')): ?>
                            <?= Html::a('<i class="bi bi-trash"></i> حذف المهمة', ['task-delete', 'id' => $selected->id], [
                                'class' => 'btn btn-sm btn-outline-danger ms-auto',
                                'data' => ['method' => 'post', 'confirm' => 'حذف مهمة متابعة ' . $c->name . '؟'],
                            ]) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
