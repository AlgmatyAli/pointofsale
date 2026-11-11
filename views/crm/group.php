<?php

use app\components\WhatsappHelper;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var app\models\CrmTag $tag */
/** @var app\models\Client[] $members */
/** @var app\models\CrmClient[] $crm */
/** @var array $users */
/** @var string $month */
/** @var array $perUser */
/** @var array $paid */

$this->title = 'مجموعة: ' . $tag->name;
$this->params['breadcrumbs'][] = ['label' => 'مجموعات الزبائن', 'url' => ['groups']];
$this->params['breadcrumbs'][] = $tag->name;
$u = Yii::$app->user;
$csrf = Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken);
$total = array_sum(array_map(fn($c) => (float) $c->balance, $crm));
?>
<div class="crm-group">
    <?= $this->render('_nav') ?>
    <h1 class="h4"><span class="badge text-bg-<?= Html::encode($tag->color) ?>"><?= Html::encode($tag->name) ?></span>
        <small class="text-body-secondary fs-6"><?= count($members) ?> زبون · ديون <?= WhatsappHelper::money($total) ?></small></h1>

    <?php if ($members): ?>
    <div class="card mb-3">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="fw-semibold"><i class="bi bi-trophy"></i> محصلة الشهر لكل موظف</span>
            <?= Html::beginForm(['group', 'id' => $tag->id], 'get', ['class' => 'd-flex gap-2 align-items-center']) ?>
                <label class="small text-body-secondary" for="g-month">الشهر</label>
                <input type="month" name="month" id="g-month" value="<?= Html::encode($month) ?>" class="form-control form-control-sm" style="width: 11rem">
                <button class="btn btn-sm btn-outline-primary">عرض</button>
            <?= Html::endForm() ?>
        </div>
        <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead><tr><th>#</th><th>الموظف</th><th>عدد الزبائن</th><th>المحصّل في الشهر</th><th>الديون المتبقية</th></tr></thead>
            <tbody>
            <?php $rank = 0; $sumCollected = 0; foreach ($perUser as $uid => $row): $rank++; $sumCollected += $row['collected']; ?>
                <tr class="<?= $rank === 1 && $row['collected'] > 0 ? 'table-success' : '' ?>">
                    <td><?= $rank === 1 && $row['collected'] > 0 ? '<i class="bi bi-trophy-fill text-warning"></i>' : $rank ?></td>
                    <td><?= Html::encode($users[$uid] ?? 'بلا مسؤول') ?></td>
                    <td><?= (int) $row['customers'] ?></td>
                    <td class="fw-bold text-success"><?= WhatsappHelper::money($row['collected']) ?></td>
                    <td><?= WhatsappHelper::money($row['debt']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot><tr class="fw-bold"><td></td><td>الإجمالي</td><td><?= count($members) ?></td><td><?= WhatsappHelper::money($sumCollected) ?></td><td><?= WhatsappHelper::money($total) ?></td></tr></tfoot>
        </table>
        </div>
        <div class="card-footer small text-body-secondary">المحصّل = مجموع إيصالات القبض من زبائن المجموعة خلال الشهر، محسوباً للموظف المسؤول حالياً عن كل زبون.</div>
    </div>
    <?php endif; ?>

    <div class="row g-3 mb-3">
        <?php if ($u->can('crmGroupMembers')): ?>
        <div class="col-lg-7">
            <form method="post" action="<?= Url::to(['group-add', 'id' => $tag->id]) ?>" class="d-flex gap-2 align-items-end">
                <?= $csrf ?>
                <div class="flex-grow-1">
                    <label class="form-label small mb-1" for="g-clients">إضافة زبائن</label>
                    <?= Select2::widget([
                        'name' => 'clients',
                        'options' => ['id' => 'g-clients', 'multiple' => true, 'placeholder' => 'ابحث بالاسم أو الهاتف...', 'dir' => 'rtl'],
                        'pluginOptions' => [
                            'minimumInputLength' => 1,
                            'ajax' => [
                                'url' => Url::to(['client-list']),
                                'dataType' => 'json',
                                'delay' => 300,
                                'data' => new JsExpression('function (p) { return {q: p.term}; }'),
                                'processResults' => new JsExpression('function (d) { return {results: d.results}; }'),
                            ],
                        ],
                    ]) ?>
                </div>
                <button class="btn btn-primary text-nowrap">إضافة</button>
            </form>
        </div>
        <?php endif; ?>
        <?php if ($u->can('crmAssign') && $members): ?>
        <div class="col-lg-5">
            <form method="post" action="<?= Url::to(['group-assign', 'id' => $tag->id]) ?>" class="d-flex gap-2 align-items-end">
                <?= $csrf ?>
                <div class="flex-grow-1">
                    <label class="form-label small mb-1" for="g-user">مسؤول لكل الأعضاء</label>
                    <?= Html::dropDownList('userId', null, ['' => 'المسؤول الافتراضي'] + $users, ['id' => 'g-user', 'class' => 'form-select']) ?>
                </div>
                <?= Html::submitButton('تعيين للكل', ['class' => 'btn btn-outline-secondary text-nowrap',
                    'data' => ['confirm' => 'تعيين المسؤول لكل الـ ' . count($members) . ' زبون في المجموعة؟ تنتقل إليه مهامهم المفتوحة.']]) ?>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <?php if (!$members): ?>
        <div class="text-center text-body-secondary py-5 border rounded">لا يوجد زبائن في هذه المجموعة بعد.</div>
    <?php else: ?>
    <div class="table-responsive">
    <table class="table table-sm align-middle">
        <thead><tr><th>الزبون</th><th>الهاتف</th><th>الرصيد</th><th>محصّل الشهر</th><th>أيام بدون دفع</th><th>المرحلة</th><th>المسؤول</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($members as $m): $c = $crm[$m->id] ?? null; ?>
            <tr>
                <td><?= $u->can('crmCardView') ? Html::a(Html::encode($m->name), ['card', 'id' => $m->id]) : Html::encode($m->name) ?></td>
                <td><?= Html::encode($m->mobile ?: $m->phone) ?></td>
                <td><?= $c ? WhatsappHelper::money($c->balance) : '—' ?></td>
                <td><?= isset($paid[$m->id]) ? WhatsappHelper::money($paid[$m->id]) : '—' ?></td>
                <td><?= $c && $c->days !== null ? (int) $c->days : '—' ?></td>
                <td><?= $this->render('_badges', ['crm' => $c]) ?></td>
                <td><?= Html::encode($c->user->username ?? '') ?></td>
                <td class="text-end">
                    <?php if ($u->can('crmGroupMembers')): ?>
                        <?= Html::a('إزالة', ['group-remove', 'id' => $tag->id, 'client' => $m->id], ['class' => 'btn btn-sm btn-outline-danger',
                            'data' => ['method' => 'post', 'confirm' => 'إزالة ' . $m->name . ' من المجموعة؟']]) ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>
