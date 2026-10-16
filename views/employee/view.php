<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Employee */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Employees'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$none = '<span class="text-body-secondary">—</span>';
$row = function (string $label, $value, bool $raw = false) use ($none) {
    $v = ($value === null || $value === '') ? $none : ($raw ? $value : Html::encode((string) $value));
    return '<div class="d-flex justify-content-between align-items-center py-2 border-bottom gap-3">'
        . '<span class="text-body-secondary">' . Html::encode($label) . '</span>'
        . '<span class="fw-semibold text-end">' . $v . '</span></div>';
};
$money = fn($v) => ($v === null || $v === '') ? null : Yii::$app->formatter->asDecimal((float) $v, 3);

// نفس منطق الكود القديم: 1 = موقوف، 0 = نشط
$stopped = ((string) $model->state === '1');
$accent = $stopped ? 'secondary' : 'success';
$stateBadge = '<span class="badge text-bg-' . $accent . '">' . ($stopped ? 'موقوف' : 'نشط') . '</span>';
$initial = mb_substr(trim((string) $model->name), 0, 1);
$notes = trim((string) $model->notes);
?>
<div class="employee-view">

    <!-- شريط الإجراءات -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <?= Html::a('<i class="bi bi-pencil-square"></i> ' . Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(
            '<i class="bi bi-arrow-repeat"></i> ' . Yii::t('app', 'Change Status'),
            ['state', 'id' => $model->id],
            [
                'class' => 'btn btn-' . ($stopped ? 'success' : 'warning'),
                'data-confirm' => Yii::t('app', 'Change Status') . '?',
            ]
        ) ?>
        <?= Html::a('<i class="bi bi-arrow-right"></i> ' . Yii::t('app', 'Back'), Yii::$app->request->referrer ?: ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::a('<i class="bi bi-trash"></i> ' . Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-outline-danger ms-auto',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </div>

    <!-- بطاقة الموظف -->
    <div class="card border-<?= $accent ?> mb-3">
        <div class="card-body d-flex flex-wrap align-items-center gap-3">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-<?= $accent ?>-subtle text-<?= $accent ?> fw-bold fs-3"
                  style="width:56px;height:56px"><?= $initial !== '' ? Html::encode($initial) : '<i class="bi bi-person"></i>' ?></span>
            <div class="flex-grow-1">
                <div class="fs-5 fw-bold"><?= Html::encode((string) $model->name) ?></div>
                <div class="text-body-secondary small">
                    <?= $stateBadge ?>
                    <span class="ms-1">#<?= Html::encode((string) $model->id) ?></span>
                    <?php if (!empty($model->startWork)): ?> · بدأ العمل <?= Html::encode((string) $model->startWork) ?><?php endif; ?>
                </div>
            </div>
            <div class="text-end">
                <div class="text-body-secondary small">الراتب</div>
                <div class="fs-3 fw-bold text-<?= $accent ?>" dir="ltr"><?= Html::encode((string) $money($model->salary)) ?></div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-bold"><i class="bi bi-cash-coin"></i> الراتب والعمل</div>
                <div class="card-body pt-2">
                    <?= $row($model->getAttributeLabel('salary'), $money($model->salary)) ?>
                    <?= $row($model->getAttributeLabel('dayOfWork'), $model->dayOfWork) ?>
                    <?= $row($model->getAttributeLabel('salaryByDay'), $money($model->salaryByDay)) ?>
                    <?= $row($model->getAttributeLabel('startWork'), $model->startWork) ?>
                    <?= $row(Yii::t('app', 'State'), $stateBadge, true) ?>
                    <div class="pt-2">
                        <div class="text-body-secondary mb-1"><?= Html::encode($model->getAttributeLabel('notes')) ?></div>
                        <div class="fw-semibold"><?= $notes === '' ? $none : nl2br(Html::encode($notes)) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-bold"><i class="bi bi-clock-history"></i> سجل التعديلات</div>
                <div class="card-body pt-2">
                    <?= $row($model->getAttributeLabel('created_by'), $model->created_by) ?>
                    <?= $row($model->getAttributeLabel('created_at'), $model->created_at) ?>
                    <?= $row($model->getAttributeLabel('updated_by'), $model->updated_by) ?>
                    <?= $row($model->getAttributeLabel('updated_at'), $model->updated_at) ?>
                </div>
            </div>
        </div>
    </div>

</div>
