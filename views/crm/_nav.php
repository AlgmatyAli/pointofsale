<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** شريط تنقّل بين شاشات متابعة الزبائن، كل رابط حسب صلاحيته */
$u = Yii::$app->user;
$current = Yii::$app->controller->action->id;
$links = [
    ['tasks', 'bi-list-check', 'مهامي اليوم', 'crmMyTasks', ['tasks']],
    ['board', 'bi-kanban', 'لوحة التحصيل', 'crmBoardView', ['board']],
    ['promises', 'bi-hand-thumbs-up', 'وعود الدفع', 'crmPromiseView', ['promises', 'promise-update']],
    ['groups', 'bi-collection', 'مجموعات الزبائن', 'crmGroupView', ['groups', 'group']],
    ['settings', 'bi-gear', 'الإعدادات', 'crmSettings', ['settings']],
];
$this->registerCss(<<<'CSS'
.crm-pane { max-height: 70vh; overflow-y: auto; }
.crm-task.active { background: var(--bs-primary-bg-subtle); border-inline-start: 3px solid var(--bs-primary); }
.crm-board { display: flex; gap: .75rem; overflow-x: auto; padding-bottom: .5rem; }
.crm-col { flex: 0 0 220px; background: var(--bs-tertiary-bg); border-radius: .5rem; }
.crm-col-body { max-height: 65vh; overflow-y: auto; }
.crm-timeline { list-style: none; padding: 0; margin: 0; }
.crm-timeline li { display: flex; gap: .75rem; padding: .6rem 0; border-bottom: 1px solid var(--bs-border-color-translucent); }
.crm-timeline .ico { flex: 0 0 2rem; height: 2rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
CSS);
?>
<ul class="nav nav-pills mb-3 flex-wrap gap-1">
    <?php foreach ($links as [$id, $icon, $label, $perm, $actions]): ?>
        <?php if ($u->can($perm)): ?>
            <li class="nav-item">
                <?= Html::a('<i class="bi ' . $icon . '"></i> ' . $label, [$id], ['class' => 'nav-link' . (in_array($current, $actions, true) ? ' active' : '')]) ?>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if ($u->can('crmRunNow') || $u->can('crmSettings')): ?>
        <li class="nav-item ms-auto d-flex align-items-center gap-2">
            <small class="text-body-secondary">آخر تحديث: <?= Html::encode(\app\models\CrmSetting::find()->select('lastRunDate')->where(['id' => 1])->scalar() ?: '—') ?></small>
            <?= Html::a('<i class="bi bi-arrow-clockwise"></i> تحديث الآن', ['run-now'], [
                'class' => 'btn btn-sm btn-outline-secondary',
                'title' => 'تحديث المهام والتحصيل والوعود فوراً بدل انتظار اليوم التالي',
                'data' => ['method' => 'post'],
            ]) ?>
        </li>
    <?php endif; ?>
</ul>
