<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */

/** @var  app\models\Receipt $model*/

$this->title = $model->id . ' - ' . ($model->c?->name ?? '');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Receipts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

// 1 = سند قبض، 2 = سند صرف (كما في أزرار الطباعة الأصلية)
$isReceive = ((int) $model->type === 1);
$accent = $isReceive ? 'success' : 'danger';
$typeText = $isReceive ? 'سند قبض' : 'سند صرف';
$icon = $isReceive ? 'bi-box-arrow-in-down' : 'bi-box-arrow-up';

$none = '<span class="text-body-secondary">—</span>';
$row = function (string $label, $value, bool $raw = false) use ($none) {
    $v = ($value === null || $value === '') ? $none : ($raw ? $value : Html::encode((string) $value));
    return '<div class="d-flex justify-content-between align-items-center py-2 border-bottom gap-3">'
        . '<span class="text-body-secondary">' . Html::encode($label) . '</span>'
        . '<span class="fw-semibold text-end">' . $v . '</span></div>';
};
$why = trim((string) $model->why);
$currency = $model->currancy0?->name;
?>
<div class="receipt-view">

    <!-- شريط الإجراءات -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <?= Html::a('<i class="bi bi-pencil-square"></i> ' . Yii::t('app', 'Update'), ['update', 'id' => $model->id, 'type' => $model->type], ['class' => 'btn btn-primary']) ?>

        <?php if ((int) $model->type === 1): ?>
            <?= Html::a('<i class="bi bi-printer"></i> ' . Yii::t('app', 'Print Reciept Receive'), ['print-reciept-reciver', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?php elseif ((int) $model->type === 2): ?>
            <?= Html::a('<i class="bi bi-printer"></i> ' . Yii::t('app', 'Print Reciept'), ['print-reciept', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>

        <?= Html::a(
            '<i class="bi bi-journal-text"></i> ' . Yii::t('app', 'Create Client Histrans'),
            ['sales/histrans', 'client' => $model->clinet, 'allData' => 1, 'type' => 0],
            ['class' => 'btn btn-info']
        ) ?>

        <?= Html::button(
            '<i class="bi bi-safe"></i> ' . Yii::t('app', 'ترحيل الى الخزينة'),
            ['value' => Url::to(['receipt/transfer-to-safe', 'receiptId' => $model->id]), 'class' => 'btn btn-outline-primary popup']
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

    <!-- بطاقة السند -->
    <div class="card border-<?= $accent ?> mb-3">
        <div class="card-body d-flex flex-wrap align-items-center gap-3">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-<?= $accent ?>-subtle text-<?= $accent ?>"
                style="width:56px;height:56px"><i class="bi <?= $icon ?> fs-3"></i></span>
            <div class="flex-grow-1">
                <div class="mb-1"><span class="badge text-bg-<?= $accent ?>"><?= $typeText ?></span></div>
                <div class="fw-bold fs-5"><?= Html::encode((string) $model->c?->name) ?></div>
                <div class="text-body-secondary small">
                    سند رقم <?= Html::encode((string) $model->id) ?>
                    <?php if (!empty($model->at)): ?> · <?= Html::encode((string) $model->at) ?><?php endif; ?>
                </div>
            </div>
            <div class="text-end">
                <div class="fs-3 fw-bold text-<?= $accent ?>" dir="ltr"><?= Html::encode(Yii::$app->formatter->asDecimal((float) $model->value, 3)) ?></div>
                <?php if ($currency): ?><div class="small text-body-secondary"><?= Html::encode($currency) ?></div><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-bold"><i class="bi bi-receipt"></i> تفاصيل السند</div>
                <div class="card-body pt-2">
                    <?= $row(Yii::t('app', 'Br ID'), $model->br?->name) ?>
                    <?= $row($model->getAttributeLabel('rId'), $model->rId) ?>
                    <?= $row(Yii::t('app', 'Client Name'), $model->c?->name) ?>
                    <?= $row($model->getAttributeLabel('at'), $model->at) ?>
                    <?= $row($model->getAttributeLabel('value'), Yii::$app->formatter->asDecimal((float) $model->value, 3)) ?>
                    <?= $row(Yii::t('app', 'Currancy'), $currency) ?>
                    <?= $row($model->getAttributeLabel('payWay'), $model->payWay) ?>
                    <?= $row($model->getAttributeLabel('tafqet'), $model->tafqet) ?>
                    <div class="pt-2">
                        <div class="text-body-secondary mb-1"><?= Html::encode(Yii::t('app', 'Why')) ?></div>
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