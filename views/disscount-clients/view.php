<?php

use yii\helpers\Html;
use yii\web\YiiAsset;

/** @var yii\web\View $this */
/** @var app\models\DisscountClients $model */

$clientName = (string) $model->client0?->name;
$notes = trim((string) $model->notes);
$this->title = $clientName . ($notes !== '' ? ' - ' . mb_strimwidth($notes, 0, 40, '…') : '');

$isSale = (string) $model->type === '1';
$indexRoute = $isSale ? ['index'] : ['index_'];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', $isSale ? 'Disscount Clients' : 'Disscount Suppliers'),
    'url' => $indexRoute,
];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

$kind = $isSale
    ? ['تخفيض على المبيعات', 'text-bg-success', 'bi-tag']
    : ['خصم من المشتريات', 'text-bg-warning', 'bi-bag-dash'];

$row = function (string $label, string $html) {
    return '<div class="col-sm-6"><div class="small text-body-secondary">' . Html::encode($label) . '</div><div class="fw-semibold">' . $html . '</div></div>';
};
$dash = '<span class="text-body-secondary">—</span>';

$this->registerCss('@media print { .disscount-clients-view .d-print-none { display: none !important; } }');
?>
<div class="disscount-clients-view">

    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <h4 class="mb-0 me-auto"><i class="bi <?= \yii\helpers\Html::encode($kind[2]) ?>"></i> <?= Html::encode($this->title) ?></h4>
        <div class="d-flex gap-2 d-print-none">
            <?= Html::a('<i class="bi bi-pencil-square"></i> ' . Yii::t('app', 'Update'), ['update', 'id' => $model->id, 'type' => $model->type], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="bi bi-arrow-right"></i> ' . Yii::t('app', 'Back'), Yii::$app->request->referrer ?: $indexRoute, ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('<i class="bi bi-trash"></i> ' . Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-outline-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card h-100 border-<?= $isSale ? 'success' : 'warning' ?>-subtle">
                <div class="card-body">
                    <span class="badge <?= \yii\helpers\Html::encode($kind[1]) ?> mb-2"><i class="bi <?= \yii\helpers\Html::encode($kind[2]) ?>"></i> <?= Html::encode($kind[0]) ?></span>
                    <div class="small text-body-secondary"><?= Html::encode($model->getAttributeLabel('value')) ?></div>
                    <div class="fs-2 fw-bold lh-1"><?= Html::encode((string) $model->value) ?></div>
                    <div class="text-body-secondary mt-1"><?= Html::encode((string) $model->currancy0?->name) ?></div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row g-3">
                        <?= $row(Yii::t('app', 'Client Name'), Html::encode($clientName) ?: $dash) ?>
                        <?= $row($model->getAttributeLabel('at'), Html::encode((string) $model->at) ?: $dash) ?>
                        <?= $row(Yii::t('app', 'Currancy'), Html::encode((string) $model->currancy0?->name) ?: $dash) ?>
                        <?= $row(Yii::t('app', 'Branch'), Html::encode((string) $model->branch0?->name) ?: $dash) ?>
                        <?= $row(Yii::t('app', 'User Insert'), Html::encode((string) $model->createdBy?->username) ?: $dash) ?>
                        <?= $row($model->getAttributeLabel('created_at'), Html::encode((string) $model->created_at) ?: $dash) ?>
                        <div class="col-12">
                            <div class="small text-body-secondary"><?= Html::encode(Yii::t('app', 'Notes')) ?></div>
                            <div><?= $notes !== '' ? nl2br(Html::encode($notes)) : $dash ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
