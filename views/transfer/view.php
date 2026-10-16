<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Transfer */

$this->title = '#' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Transfers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

// 1 = صادر، 2 = وارد (كما في الكود الأصلي)
$isOut = ((int) $model->type === 1);
$known = in_array((int) $model->type, [1, 2], true);
$accent = !$known ? 'primary' : ($isOut ? 'danger' : 'success');
$typeText = !$known ? 'حركة' : ($isOut ? 'صادر' : 'وارد');

$none = '<span class="text-body-secondary">—</span>';
$row = function (string $label, $value, bool $raw = false) use ($none) {
    $v = ($value === null || $value === '') ? $none : ($raw ? $value : Html::encode((string) $value));
    return '<div class="d-flex justify-content-between align-items-center py-2 border-bottom gap-3">'
        . '<span class="text-body-secondary">' . Html::encode($label) . '</span>'
        . '<span class="fw-semibold text-end">' . $v . '</span></div>';
};
$why = trim((string) $model->why);
$from = $model->fromBr0?->name;
$to = $model->toBr0?->name;
?>
<div class="transfer-view">

    <div class="d-flex flex-wrap gap-2 mb-3">
        <?= Html::a('<i class="bi bi-pencil-square"></i> ' . Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="bi bi-arrow-right"></i> ' . Yii::t('app', 'Back'), Yii::$app->request->referrer ?: ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::a('<i class="bi bi-trash"></i> ' . Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-outline-danger ms-auto',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </div>

    <!-- بطاقة الحركة: من فرع ← إلى فرع -->
    <div class="card border-<?= $accent ?> mb-3">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-<?= $accent ?>-subtle text-<?= $accent ?>"
                      style="width:56px;height:56px"><i class="bi bi-arrow-left-right fs-3"></i></span>
                <div class="flex-grow-1">
                    <div class="mb-1"><span class="badge text-bg-<?= $accent ?>"><?= $typeText ?></span></div>
                    <div class="d-flex flex-wrap align-items-center gap-2 fw-bold fs-5">
                        <span><?= $from !== null ? Html::encode($from) : $none ?></span>
                        <i class="bi bi-arrow-left text-body-secondary"></i>
                        <span><?= $to !== null ? Html::encode($to) : $none ?></span>
                    </div>
                    <div class="text-body-secondary small">
                        حركة رقم <?= Html::encode((string) $model->id) ?>
                        <?php if (!empty($model->at)): ?> · <?= Html::encode((string) $model->at) ?><?php endif; ?>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-<?= $accent ?>" dir="ltr"><?= Html::encode(Yii::$app->formatter->asDecimal((float) $model->value, 3)) ?></div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-bold"><i class="bi bi-receipt"></i> تفاصيل الحركة</div>
                <div class="card-body pt-2">
                    <?= $row(Yii::t('app', 'From Br'), $from) ?>
                    <?= $row(Yii::t('app', 'To Br'), $to) ?>
                    <?= $row($model->getAttributeLabel('value'), Yii::$app->formatter->asDecimal((float) $model->value, 3)) ?>
                    <?= $row($model->getAttributeLabel('at'), $model->at) ?>
                    <?= $row(Yii::t('app', 'Type'), $known ? '<span class="badge text-bg-' . $accent . '">' . $typeText . '</span>' : null, true) ?>
                    <div class="pt-2">
                        <div class="text-body-secondary mb-1"><?= Html::encode($model->getAttributeLabel('why')) ?></div>
                        <div class="fw-semibold"><?= $why === '' ? $none : nl2br(Html::encode($why)) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-bold"><i class="bi bi-person-badge"></i> بيانات الإدخال</div>
                <div class="card-body pt-2">
                    <?= $row(Yii::t('app', 'User Insert'), $model->userInsert?->username) ?>
                    <?= $row($model->getAttributeLabel('created_at'), $model->created_at) ?>
                </div>
            </div>
        </div>
    </div>

</div>
