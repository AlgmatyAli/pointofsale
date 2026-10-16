<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

/** @var app\models\EmpSalary $model */

$this->title = '#' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Emp Salaries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

// نوع الحركة كما في صفحة index: 1 سحب، 2 خصم، 3 اضافي
$types = [
    1 => ['سحب', 'success', 'bi-cash-coin'],
    2 => ['خصم', 'warning', 'bi-scissors'],
    3 => ['اضافي', 'danger', 'bi-plus-circle'],
];
[$typeText, $accent, $icon] = $types[(int) $model->type] ?? ['حركة', 'primary', 'bi-wallet2'];

// شاشة تعديل كل نوع: السحب update، الخصم cdiscount، الإضافي cextrajob
$updateRoutes = [1 => 'update', 2 => 'u-disscount', 3 => 'u-extra-job'];
$updateRoute = $updateRoutes[(int) $model->type] ?? 'update';

$none = '<span class="text-body-secondary">—</span>';
$row = function (string $label, $value, bool $raw = false) use ($none) {
    $v = ($value === null || $value === '') ? $none : ($raw ? $value : Html::encode((string) $value));
    return '<div class="d-flex justify-content-between align-items-center py-2 border-bottom gap-3">'
        . '<span class="text-body-secondary">' . Html::encode($label) . '</span>'
        . '<span class="fw-semibold text-end">' . $v . '</span></div>';
};
$why = trim((string) $model->why);
$period = ($model->month && $model->year) ? ($model->month . ' / ' . $model->year) : null;
?>
<div class="emp-salary-view">

    <div class="d-flex flex-wrap gap-2 mb-3">
        <?= Html::a('<i class="bi bi-pencil-square"></i> ' . Yii::t('app', 'Update'), [$updateRoute, 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="bi bi-arrow-right"></i> ' . Yii::t('app', 'Back'), Yii::$app->request->referrer ?: ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::a('<i class="bi bi-trash"></i> ' . Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-outline-danger ms-auto',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </div>

    <!-- بطاقة الحركة -->
    <div class="card border-<?= $accent ?> mb-3">
        <div class="card-body d-flex flex-wrap align-items-center gap-3">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-<?= $accent ?>-subtle text-<?= $accent ?>"
                style="width:56px;height:56px"><i class="bi <?= $icon ?> fs-3"></i></span>
            <div class="flex-grow-1">
                <div class="mb-1"><span class="badge text-bg-<?= $accent ?>"><?= Html::encode($typeText) ?></span></div>
                <div class="fw-bold fs-5"><?= Html::encode((string) $model->emp?->name) ?></div>
                <div class="text-body-secondary small">
                    حركة رقم <?= Html::encode((string) $model->id) ?>
                    <?php if (!empty($model->at)): ?> · <?= Html::encode((string) $model->at) ?><?php endif; ?>
                        <?php if ($period): ?> · عن شهر <?= Html::encode($period) ?><?php endif; ?>
                </div>
            </div>
            <div class="fs-3 fw-bold text-<?= $accent ?>" dir="ltr"><?= Html::encode(Yii::$app->formatter->asDecimal((float) $model->value, 3)) ?></div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-bold"><i class="bi bi-receipt"></i> تفاصيل الحركة</div>
                <div class="card-body pt-2">
                    <?= $row($model->getAttributeLabel('id'), $model->id) ?>
                    <?= $row($model->getAttributeLabel('employee'), $model->emp?->name ?? $model->employee) ?>
                    <?= $row($model->getAttributeLabel('at'), $model->at) ?>
                    <?= $row(Yii::t('app', 'Value'), Yii::$app->formatter->asDecimal((float) $model->value, 3)) ?>
                    <div class="pt-2">
                        <div class="text-body-secondary mb-1"><?= Html::encode($model->getAttributeLabel('why')) ?></div>
                        <div class="fw-semibold"><?= $why === '' ? $none : nl2br(Html::encode($why)) ?></div>
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
                    <?= $row($model->getAttributeLabel('update_at'), $model->updated_at) ?>
                </div>
            </div>
        </div>
    </div>

</div>