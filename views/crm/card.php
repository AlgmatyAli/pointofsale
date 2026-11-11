<?php

use app\components\WhatsappHelper;
use app\models\CrmActivity;
use app\models\CrmClient;
use app\models\CrmLevel;
use app\models\CrmPromise;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Client $client */
/** @var CrmClient $crm */
/** @var float $balance */
/** @var float|null $usage */
/** @var array|null $lastReceipt */
/** @var CrmActivity[] $openTasks */
/** @var CrmPromise|null $promise */
/** @var app\models\CrmTag[] $tags */
/** @var array $allTags */
/** @var array $timeline */
/** @var array $users */

$this->title = $client->name;
$this->params['breadcrumbs'][] = 'متابعة الزبائن';
$this->params['breadcrumbs'][] = $this->title;
$u = Yii::$app->user;
$csrf = Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken);
$tagIds = array_map(fn($t) => (int) $t->id, $tags);
?>
<div class="crm-card">
    <?= $this->render('_nav') ?>

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h1 class="h3 mb-1"><?= Html::encode($client->name) ?></h1>
            <div class="text-body-secondary small">
                <?= Html::encode(implode(' · ', array_filter([$client->mobile, $client->phone, $client->address]))) ?>
            </div>
            <div class="mt-2">
                <?= $this->render('_badges', ['crm' => $crm, 'promise' => $promise !== null]) ?>
                <?php foreach ($tags as $t): ?>
                    <span class="badge rounded-pill text-bg-<?= Html::encode($t->color) ?>">
                        <?= Html::encode($t->name) ?>
                        <?php if ($u->can('crmGroupMembers')): ?>
                            <?= Html::a('&times;', ['group-remove', 'id' => $t->id, 'client' => $client->id], [
                                'class' => 'text-reset text-decoration-none ms-1', 'title' => 'إزالة من المجموعة',
                                'data' => ['method' => 'post', 'confirm' => 'إزالة ' . $client->name . ' من مجموعة ' . $t->name . '؟'],
                            ]) ?>
                        <?php endif; ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <?php if ($u->can('crmWhatsapp')): ?>
                <?= Html::a('<i class="bi bi-whatsapp"></i> تذكير بالرصيد', ['whatsapp', 'id' => $client->id], ['class' => 'btn btn-success', 'target' => '_blank', 'rel' => 'noopener']) ?>
            <?php endif; ?>
            <?= Html::a('<i class="bi bi-file-earmark-pdf"></i> كشف الحساب', WhatsappHelper::statementUrl($client->id, (int) $client->type), ['class' => 'btn btn-outline-secondary', 'target' => '_blank', 'rel' => 'noopener']) ?>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3"><div class="border rounded p-3 h-100">
            <div class="small text-body-secondary">الرصيد</div>
            <div class="fs-4 fw-bold"><?= WhatsappHelper::money($balance) ?></div>
        </div></div>
        <div class="col-6 col-md-3"><div class="border rounded p-3 h-100">
            <div class="small text-body-secondary">سقف الدين</div>
            <?php if ($usage === null): ?>
                <div class="fs-5 fw-bold">سقف مفتوح</div>
            <?php else: ?>
                <div class="fs-5 fw-bold <?= $usage >= 80 ? 'text-danger' : '' ?>"><?= Html::encode($usage) ?>%</div>
                <div class="progress mt-1" style="height:6px" role="progressbar" aria-valuenow="<?= (int) min(100, $usage) ?>" aria-valuemin="0" aria-valuemax="100" aria-label="استهلاك سقف الدين">
                    <div class="progress-bar <?= $usage >= 80 ? 'bg-danger' : '' ?>" style="width: <?= (int) min(100, max(0, $usage)) ?>%"></div>
                </div>
                <div class="small text-body-secondary">من <?= WhatsappHelper::money($client->debt) ?></div>
            <?php endif; ?>
        </div></div>
        <div class="col-6 col-md-3"><div class="border rounded p-3 h-100">
            <div class="small text-body-secondary">أيام بدون دفع</div>
            <div class="fs-4 fw-bold"><?= $crm->days !== null ? (int) $crm->days : '—' ?></div>
            <div class="small text-body-secondary">
                <?= $lastReceipt ? 'آخر دفعة ' . WhatsappHelper::money($lastReceipt['value']) . ' في ' . Html::encode($lastReceipt['at']) : 'لم يدفع أي إيصال' ?>
            </div>
        </div></div>
        <div class="col-6 col-md-3"><div class="border rounded p-3 h-100">
            <div class="small text-body-secondary">البيع الآجل</div>
            <div class="fs-5 fw-bold <?= (int) $client->post_paid === 1 ? 'text-success' : 'text-danger' ?>"><?= (int) $client->post_paid === 1 ? 'مسموح' : 'موقوف' ?></div>
            <?php if ($u->can('updatClient')): ?>
                <?= Html::a('تعديل من بيانات الزبون', ['/client/update', 'id' => $client->id], ['class' => 'small']) ?>
            <?php endif; ?>
        </div></div>
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
            <?php if ($promise): ?>
                <div class="alert alert-primary">
                    <strong>وعد بالدفع:</strong> <?= WhatsappHelper::money($promise->amount) ?> بتاريخ <?= Html::encode($promise->promiseDate) ?>
                    (المقبوض منذ الوعد <?= WhatsappHelper::money($promise->paidSince) ?>)
                    <?= $promise->note ? '<br>' . Html::encode($promise->note) : '' ?>
                    <?php if ($u->can('crmWhatsapp')): ?>
                        <div class="mt-2">
                            <?= Html::a('<i class="bi bi-whatsapp"></i> تذكير بالوعد', ['promise-whatsapp', 'id' => $promise->id], ['class' => 'btn btn-sm btn-success', 'target' => '_blank', 'rel' => 'noopener']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="card mb-3">
                <div class="card-header fw-semibold">المهام المفتوحة</div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($openTasks as $t): ?>
                        <li class="list-group-item d-flex justify-content-between gap-2">
                            <span><i class="bi <?= CrmActivity::ICONS[$t->type] ?? 'bi-dot' ?>"></i> <?= Html::encode(CrmActivity::TYPES[$t->type] ?? $t->type) ?>
                                <small class="text-body-secondary d-block"><?= Html::encode($t->note) ?></small></span>
                            <span class="text-end small">
                                <span class="<?= $t->dueDate < date('Y-m-d') ? 'text-danger' : '' ?>"><?= Html::encode($t->dueDate) ?></span><br>
                                <?= Html::encode($t->user->username ?? 'بلا مكلّف') ?>
                                <?php if ($u->can('crmMyTasks') && ((int) $t->userId === (int) $u->id || $u->can('crmAllTasks'))): ?>
                                    <br><?= Html::a('فتح', ['tasks', 'id' => $t->id, 'when' => 'all', 'user' => $u->can('crmAllTasks') ? '' : null]) ?>
                                <?php endif; ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                    <?php if (!$openTasks): ?><li class="list-group-item text-body-secondary">لا توجد مهام مفتوحة.</li><?php endif; ?>
                </ul>
            </div>

            <?php if ($u->can('crmTaskCreate')): ?>
            <details class="card card-body mb-3">
                <summary class="fw-semibold">مهمة جديدة</summary>
                <form method="post" action="<?= Url::to(['task-create']) ?>" class="row g-2 mt-1">
                    <?= $csrf ?>
                    <?= Html::hiddenInput('CrmActivity[clientId]', $client->id) ?>
                    <div class="col-md-6">
                        <label class="form-label small" for="n-type">النوع</label>
                        <?= Html::dropDownList('CrmActivity[type]', 'call', CrmActivity::TYPES, ['id' => 'n-type', 'class' => 'form-select']) ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small" for="n-date">التاريخ</label>
                        <input type="date" name="CrmActivity[dueDate]" id="n-date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <?php if ($u->can('crmAssign')): ?>
                    <div class="col-12">
                        <label class="form-label small" for="n-user">المكلّف</label>
                        <?= Html::dropDownList('CrmActivity[userId]', \app\components\CrmService::responsibleOf($crm) ?: $u->id, $users, ['id' => 'n-user', 'class' => 'form-select']) ?>
                    </div>
                    <?php endif; ?>
                    <div class="col-12">
                        <label class="form-label small" for="n-note">ملاحظة</label>
                        <input type="text" name="CrmActivity[note]" id="n-note" class="form-control" maxlength="500">
                    </div>
                    <div class="col-12"><button class="btn btn-primary">إضافة المهمة</button></div>
                </form>
            </details>
            <?php endif; ?>

            <?php if ($u->can('crmPromiseCreate') && !$promise): ?>
            <details class="card card-body mb-3">
                <summary class="fw-semibold">تسجيل وعد بالدفع</summary>
                <?= $this->render('_promise_form', ['clientId' => $client->id, 'balance' => $balance]) ?>
            </details>
            <?php endif; ?>

            <div class="card card-body mb-3">
                <div class="row g-2">
                    <div class="col-12 small text-body-secondary">المسؤول عن الزبون:
                        <strong><?= Html::encode($crm->user->username ?? ('افتراضي: ' . ($users[\app\components\CrmService::responsibleOf(null)] ?? 'لا أحد'))) ?></strong>
                    </div>
                    <?php if ($u->can('crmAssign')): ?>
                    <form method="post" action="<?= Url::to(['assign', 'id' => $client->id]) ?>" class="col-12 d-flex gap-2">
                        <?= $csrf ?>
                        <?= Html::dropDownList('userId', $crm->userId, ['' => 'المسؤول الافتراضي'] + $users, ['class' => 'form-select form-select-sm', 'aria-label' => 'المسؤول']) ?>
                        <button class="btn btn-sm btn-outline-secondary text-nowrap">تعيين المسؤول</button>
                    </form>
                    <?php endif; ?>
                    <?php if ($u->can('crmStateChange')): ?>
                    <form method="post" action="<?= Url::to(['state', 'id' => $client->id]) ?>" class="col-12 d-flex gap-2">
                        <?= $csrf ?>
                        <?= Html::dropDownList('state', (int) $crm->state, CrmClient::STATES, ['class' => 'form-select form-select-sm', 'aria-label' => 'الحالة']) ?>
                        <button class="btn btn-sm btn-outline-secondary text-nowrap">تغيير الحالة</button>
                    </form>
                    <?php endif; ?>
                    <?php $free = array_diff_key($allTags, array_flip($tagIds)); ?>
                    <?php if ($u->can('crmGroupMembers') && $free): ?>
                    <form method="post" class="col-12 d-flex gap-2" onsubmit="this.action=this.dataset.base.replace('__ID__', this.tag.value)"
                          data-base="<?= Url::to(['group-add', 'id' => '__ID__']) ?>">
                        <?= $csrf ?>
                        <?= Html::hiddenInput('clients[]', $client->id) ?>
                        <?= Html::dropDownList('tag', null, $free, ['class' => 'form-select form-select-sm', 'aria-label' => 'المجموعة']) ?>
                        <button class="btn btn-sm btn-outline-secondary text-nowrap">إضافة لمجموعة</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header fw-semibold">الخط الزمني</div>
                <div class="card-body">
                    <?php if ($u->can('crmNoteAdd')): ?>
                    <form method="post" action="<?= Url::to(['note', 'id' => $client->id]) ?>" class="d-flex gap-2 mb-3">
                        <?= $csrf ?>
                        <input type="text" name="text" class="form-control" maxlength="2000" required aria-label="ملاحظة" placeholder="مثال: اتصلت وقال سيمر يوم الخميس">
                        <button class="btn btn-outline-primary text-nowrap">إضافة ملاحظة</button>
                    </form>
                    <?php endif; ?>
                    <ul class="crm-timeline">
                        <?php foreach ($timeline as $e): ?>
                            <li>
                                <span class="ico text-bg-<?= Html::encode($e['color']) ?>"><i class="bi <?= Html::encode($e['icon']) ?>"></i></span>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between gap-2">
                                        <strong>
                                            <?= isset($e['salesId']) ? Html::a(Html::encode($e['title']), ['/sales/print', 'id' => $e['salesId']]) : Html::encode($e['title']) ?>
                                        </strong>
                                        <small class="text-body-secondary text-nowrap"><?= Html::encode(substr((string) $e['date'], 0, 16)) ?></small>
                                    </div>
                                    <div><?= Html::encode($e['text']) ?></div>
                                    <?php if ($e['user']): ?><small class="text-body-secondary"><?= Html::encode($e['user']) ?></small><?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        <?php if (!$timeline): ?><li class="text-body-secondary">لا توجد حركات بعد.</li><?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
