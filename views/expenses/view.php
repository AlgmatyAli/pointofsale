<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Expenses */

$this->title = '#' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Expenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$none = '<span class="text-body-secondary">—</span>';

// صف عرض (عنوان + قيمة)؛ القيم تُهرَّب افتراضياً
$row = function (string $label, $value, bool $raw = false) use ($none) {
    $v = ($value === null || $value === '') ? $none : ($raw ? $value : Html::encode((string) $value));
    return '<div class="d-flex justify-content-between align-items-center py-2 border-bottom gap-3">'
        . '<span class="text-body-secondary">' . Html::encode($label) . '</span>'
        . '<span class="fw-semibold text-end">' . $v . '</span></div>';
};

$outBox = !empty($model->outBox);
$outBoxBadge = $outBox
    ? '<span class="badge text-bg-danger"><i class="bi bi-box-arrow-up"></i> ' . Html::encode(Yii::t('app', 'Yes')) . '</span>'
    : '<span class="badge text-bg-secondary">' . Html::encode(Yii::t('app', 'No')) . '</span>';

$why = trim((string) $model->why);
?>
<div class="expenses-view">

    <!-- شريط الإجراءات -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <?= Html::a(
            '<i class="bi bi-pencil-square"></i> ' . Yii::t('app', 'Update'),
            ['update', 'id' => $model->id],
            ['class' => 'btn btn-primary']
        ) ?>
        <?= Html::a(
            '<i class="bi bi-printer"></i> ' . Yii::t('app', 'Print Reciept'),
            ['print-recipt', 'id' => $model->id],
            ['class' => 'btn btn-success']
        ) ?>
        <?= Html::a(
            '<i class="bi bi-arrow-right"></i> ' . Yii::t('app', 'Back'),
            Yii::$app->request->referrer ?: ['index'],
            ['class' => 'btn btn-outline-secondary']
        ) ?>
        <?= Html::a(
            '<i class="bi bi-trash"></i> ' . Yii::t('app', 'Delete'),
            ['delete', 'id' => $model->id],
            [
                'class' => 'btn btn-outline-danger ms-auto',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]
        ) ?>
    </div>

    <!-- بطاقة المبلغ -->
    <div class="card border-danger mb-3">
        <div class="card-body d-flex flex-wrap align-items-center gap-3">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger"
                  style="width:56px;height:56px">
                <i class="bi bi-cash-coin fs-3"></i>
            </span>
            <div class="flex-grow-1">
                <div class="mb-1">
                    <span class="badge text-bg-danger"><?= Html::encode(Yii::t('app', 'Expenses')) ?></span>
                    <?= $outBox ? $outBoxBadge : '' ?>
                </div>
                <div class="text-body-secondary">
                    <?= Html::encode(Yii::t('app', 'Expenses')) ?> #<?= Html::encode((string) $model->id) ?>
                    <?php if (!empty($model->at)): ?> · <?= Html::encode((string) $model->at) ?><?php endif; ?>
                </div>
                <?php if (!empty($model->expenseTo)): ?>
                    <div class="fw-semibold"><i class="bi bi-person"></i> <?= Html::encode((string) $model->expenseTo) ?></div>
                <?php endif; ?>
            </div>
            <div class="fs-3 fw-bold text-danger" dir="ltr">
                <?= Html::encode(Yii::$app->formatter->asDecimal((float) $model->value, 3)) ?>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- تفاصيل المصروف -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-bold"><i class="bi bi-receipt"></i> <?= Html::encode(Yii::t('app', 'Details')) ?></div>
                <div class="card-body pt-2">
                    <?= $row(Yii::t('app', 'Expense To'), $model->expenseTo) ?>
                    <?= $row(Yii::t('app', 'At'), $model->at) ?>
                    <?= $row(Yii::t('app', 'Name'), $model->item?->name) ?>
                    <?= $row(Yii::t('app', 'Value'), Yii::$app->formatter->asDecimal((float) $model->value, 3)) ?>
                    <?= $row(Yii::t('app', 'Out Box'), $outBoxBadge, true) ?>
                    <div class="pt-2">
                        <div class="text-body-secondary mb-1"><?= Html::encode(Yii::t('app', 'Why')) ?></div>
                        <div class="fw-semibold"><?= $why === '' ? $none : nl2br(Html::encode($why)) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- بيانات الإدخال -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-bold"><i class="bi bi-person-badge"></i> بيانات الإدخال</div>
                <div class="card-body pt-2">
                    <?= $row(Yii::t('app', 'User Insert'), $model->userInsert?->username) ?>
                    <?= $row(Yii::t('app', 'Created At'), $model->created_at) ?>
                </div>
            </div>
        </div>
    </div>

</div>
