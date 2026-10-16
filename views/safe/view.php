<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Safe $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Saves'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$isOut = ($model->type == 1);   // 1 = صادر، 2 = وارد
$typeText = $model->type == 1 ? 'صادر' : ($model->type == 2 ? 'وارد' : '—');
$typeClass = $isOut ? 'text-bg-danger' : 'text-bg-success';
$accent = $isOut ? 'danger' : 'success';

$safeText = $model->safeNo == 1 ? 'خزينة رقم 1' : ($model->safeNo == 2 ? 'خزينة رقم 2' : '—');

$none = '<span class="text-body-secondary">—</span>';
$row = function (string $label, $value, bool $raw = false) use ($none) {
    $v = ($value === null || $value === '') ? $none : ($raw ? $value : Html::encode((string) $value));
    return '<div class="d-flex justify-content-between align-items-center py-2 border-bottom">'
        . '<span class="text-body-secondary">' . Html::encode($label) . '</span>'
        . '<span class="fw-semibold text-end">' . $v . '</span></div>';
};
?>
<div class="safe-view">

    <!-- شريط الإجراءات -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <?= Html::a(
            '<i class="bi bi-pencil-square"></i> ' . Yii::t('app', 'Update'),
            ['update', 'id' => $model->id],
            ['class' => 'btn btn-primary']
        ) ?>
        <?= Html::a(
            '<i class="bi bi-arrow-right-circle"></i> ' . Yii::t('app', 'Back'),
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
    <div class="card border-<?= $accent ?> mb-3">
        <div class="card-body d-flex flex-wrap align-items-center gap-3">
            <span class="rounded-circle bg-<?= $accent ?>-subtle text-<?= $accent ?>-emphasis d-inline-flex align-items-center justify-content-center"
                  style="width:56px;height:56px">
                <i class="bi <?= $isOut ? 'bi-box-arrow-up' : 'bi-box-arrow-in-down' ?> fs-3"></i>
            </span>
            <div class="me-auto">
                <span class="badge <?= $typeClass ?> mb-1"><?= Html::encode($typeText) ?></span>
                <div class="text-body-secondary small">حركة رقم <?= Html::encode((string) $model->id) ?> · <?= Html::encode((string) $model->at) ?></div>
            </div>
            <div class="fs-3 fw-bold text-<?= $accent ?>" dir="ltr">
                <?= Yii::$app->formatter->asDecimal($model->value) ?>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-semibold"><i class="bi bi-info-circle"></i> تفاصيل الحركة</div>
                <div class="card-body pt-2">
                    <?= $row(Yii::t('app', 'Br ID'), $model->br?->name) ?>
                    <?= $row(Yii::t('app', 'Safe No.'), $safeText) ?>
                    <?= $row($model->getAttributeLabel('value'), Yii::$app->formatter->asDecimal($model->value)) ?>
                    <?= $row($model->getAttributeLabel('at'), $model->at) ?>
                    <?= $row(Yii::t('app', 'Type'), '<span class="badge ' . $typeClass . '">' . Html::encode($typeText) . '</span>', true) ?>
                    <div class="pt-2">
                        <div class="text-body-secondary mb-1"><?= Html::encode($model->getAttributeLabel('why')) ?></div>
                        <div class="fw-semibold"><?= $model->why ? nl2br(Html::encode($model->why)) : $none ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-semibold"><i class="bi bi-person-gear"></i> بيانات الإدخال</div>
                <div class="card-body pt-2">
                    <?= $row(Yii::t('app', 'User Insert'), $model->userInsert?->username) ?>
                    <?= $row($model->getAttributeLabel('created_at'), $model->created_at) ?>
                </div>
            </div>
        </div>
    </div>
</div>
