<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Purchases $model */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = $model->billId;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Purchases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$identity = Yii::$app->user->identity;
$canSeeCost = $identity->seeCostPrice == 1;
$canPrint = $identity->printPurtchaseInvoice == 1;

// ---- الحسابات (نفس معادلات الملف الأصلي دون تغيير) ----
$items = $dataProvider->getModels();
$itemsCount = $dataProvider->getCount();

$qtyTotal = 0;
foreach ($items as $row) {
    $qtyTotal = $row->quantity + $qtyTotal;
}

$cost = 0;
$total = 0;
$profit = 0;
foreach ($items as $row) {
    $cost += ($row->quantity * $row->totalCost);
    $lineSale = $row->quantity * $row->salePrice;
    $total = $total + $lineSale;
    $profit = $profit + ($lineSale - ($row->quantity * $row->totalCost));
}

$fmt = fn($v) => number_format((float) $v, 3);

// ---- نصوص وحالات ----
$types = [
    1 => ['مشتريات', 'text-bg-success'],
    2 => ['مسترجع مشتريات', 'text-bg-danger'],
    3 => ['مشتريات معلقة', 'text-bg-warning'],
];
[$typeText, $typeClass] = $types[(int) $model->type] ?? ['—', 'text-bg-secondary'];

$payWays = ['0' => 'نقدا', '1' => 'آجل', '2' => 'دفعة على الحساب'];
$payWayText = $payWays[(string) $model->payWay] ?? '—';

$net = (float) $model->total - (float) $model->paid;

$none = '<span class="text-body-secondary">—</span>';
$clientName = $model->c?->name;
$currancyName = $model->currancy0?->name;
$branchName = $model->br?->name;
$userName = $model->userInsert?->username;

/** صف في قائمة التفاصيل */
$row = function (string $label, $value, bool $raw = false) use ($none) {
    $v = ($value === null || $value === '') ? $none : ($raw ? $value : Html::encode((string) $value));
    return '<div class="d-flex justify-content-between align-items-center py-2 border-bottom">'
        . '<span class="text-body-secondary">' . Html::encode($label) . '</span>'
        . '<span class="fw-semibold text-end">' . $v . '</span></div>';
};
?>

<div class="purchases-view">

    <!-- رأس الصفحة -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-receipt"></i>
                <?= Html::encode($this->title) ?>
            </h3>
            <span class="badge <?= $typeClass ?>"><?= Html::encode($typeText) ?></span>
            <span class="badge text-bg-light border"><i class="bi bi-wallet2"></i> <?= Html::encode($payWayText) ?></span>
            <span class="text-body-secondary small ms-2"><i class="bi bi-calendar3"></i> <?= Html::encode((string) $model->at) ?></span>
        </div>
        <?= Html::a(
            '<i class="bi bi-arrow-right-circle"></i> ' . Yii::t('app', 'Back'),
            Yii::$app->request->referrer ?: ['index'],
            ['class' => 'btn btn-outline-secondary']
        ) ?>
    </div>

    <!-- البطاقات الإحصائية -->
    <div class="row g-3 mb-3">
        <div class="col-xl-3 col-md-6">
            <div class="info-box mb-0">
                <span class="info-box-icon text-bg-primary shadow-sm"><i class="bi bi-box-seam"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?= Yii::t('app', 'Items Count') ?></span>
                    <span class="info-box-number"><?= (int) $itemsCount ?></span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="info-box mb-0">
                <span class="info-box-icon text-bg-success shadow-sm"><i class="bi bi-stack"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?= Yii::t('app', 'اجمالي الكمية') ?></span>
                    <span class="info-box-number"><?= Html::encode((string) $qtyTotal) ?></span>
                </div>
            </div>
        </div>

        <?php if ($canSeeCost): ?>
            <div class="col-xl-3 col-md-6">
                <div class="info-box mb-0">
                    <span class="info-box-icon text-bg-warning shadow-sm"><i class="bi bi-cash-stack"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= Yii::t('app', 'اجمالي التكلفة بالمصاريف') ?></span>
                        <span class="info-box-number"><?= $fmt($cost) ?></span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="info-box mb-0">
                    <span class="info-box-icon text-bg-info shadow-sm"><i class="bi bi-graph-up-arrow"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= Yii::t('app', 'اجمالي البيع') ?></span>
                        <span class="info-box-number"><?= $fmt($total) ?></span>
                        <span class="progress-description small <?= $profit >= 0 ? 'text-success' : 'text-danger' ?>">
                            هامش الربح: <?= $fmt($profit) ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- شريط الإجراءات -->
    <div class="card mb-3">
        <div class="card-body d-flex flex-wrap gap-2 align-items-center">

            <?= Html::a(
                '<i class="bi bi-pencil-square"></i> ' . Yii::t('app', 'Update'),
                ['update', 'id' => $model->id],
                ['class' => 'btn btn-primary']
            ) ?>

            <?= Html::a(
                '<i class="bi bi-tags"></i> ' . Yii::t('app', 'Update Sales Prices'),
                ['purchases-details', 'id' => $model->id],
                ['class' => 'btn btn-outline-primary']
            ) ?>

            <!-- قائمة الطباعة -->
            <div class="btn-group">
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-printer"></i> <?= Yii::t('app', 'Print') ?>
                </button>
                <ul class="dropdown-menu">
                    <?php if ($canPrint): ?>
                        <li><?= Html::a('<i class="bi bi-printer me-2"></i>' . Yii::t('app', 'Print Bill'), ['print-bill', 'id' => $model->id], ['class' => 'dropdown-item']) ?></li>
                        <li><?= Html::a('<i class="bi bi-printer me-2"></i>' . Yii::t('app', 'Print Bill Dirham'), ['print-d-bill', 'id' => $model->id], ['class' => 'dropdown-item']) ?></li>
                        <li><hr class="dropdown-divider"></li>
                    <?php endif; ?>
                    <li><?= Html::a('<i class="bi bi-printer me-2"></i>' . Yii::t('app', 'Print With Hang OUt Q'), ['noprice', 'id' => $model->id], ['class' => 'dropdown-item']) ?></li>
                    <li><?= Html::a('<i class="bi bi-printer me-2"></i>' . Yii::t('app', 'Print Bill With Prices'), ['print-bill-with-out-price', 'id' => $model->id], ['class' => 'dropdown-item']) ?></li>
                    <li><?= Html::a('<i class="bi bi-geo-alt me-2"></i>' . Yii::t('app', 'Print Bill With Place'), ['print-bill-with-place', 'id' => $model->id], ['class' => 'dropdown-item']) ?></li>
                </ul>
            </div>

            <!-- إجراءات إضافية (تبقى بالفئة popup لتعمل مع السكربت الحالي) -->
            <?= Html::button(
                '<i class="bi bi-files"></i> ' . Yii::t('app', 'SaveAsNew'),
                ['value' => Url::to(['purchases/save-as-new', 'oldId' => $model->id]), 'class' => 'btn btn-outline-secondary popup']
            ) ?>

            <?= Html::button(
                '<i class="bi bi-arrow-left-right"></i> ' . Yii::t('app', 'نقل الاصناف بين الفروع'),
                ['value' => Url::to(['add-to-transfer-items', 'id' => $model->id]), 'class' => 'btn btn-outline-info popup']
            ) ?>

            <!-- زر الحذف في آخر الشريط -->
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
    </div>

    <!-- تفاصيل الفاتورة -->
    <div class="row g-3">

        <!-- بيانات الفاتورة -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header fw-semibold"><i class="bi bi-info-circle"></i> بيانات الفاتورة</div>
                <div class="card-body pt-2">
                    <?= $row($model->getAttributeLabel('billId'), $model->billId) ?>
                    <?= $row(Yii::t('app', 'Client Name'), $clientName) ?>
                    <?= $row($model->getAttributeLabel('clientBill'), $model->clientBill) ?>
                    <?= $row($model->getAttributeLabel('at'), $model->at) ?>
                    <?= $row(Yii::t('app', 'Type'), '<span class="badge ' . $typeClass . '">' . Html::encode($typeText) . '</span>', true) ?>
                    <?= $row(Yii::t('app', 'Br ID'), $branchName) ?>
                </div>
            </div>
        </div>

        <!-- المبالغ -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header fw-semibold"><i class="bi bi-calculator"></i> المبالغ</div>
                <div class="card-body pt-2">
                    <?= $row(Yii::t('app', 'Total'), $fmt($model->total)) ?>
                    <?php if ($canSeeCost): ?>
                        <?= $row(Yii::t('app', 'Total Cost'), $fmt($model->totalCost)) ?>
                    <?php endif; ?>
                    <?= $row(Yii::t('app', 'Paid'), $fmt($model->paid)) ?>
                    <?= $row(Yii::t('app', 'Pay Way'), $payWayText) ?>
                    <?= $row(Yii::t('app', 'Currancy'), $currancyName) ?>
                    <?= $row($model->getAttributeLabel('total_currancy'), $model->total_currancy) ?>

                    <div class="d-flex justify-content-between align-items-center mt-3 p-3 rounded <?= $net > 0 ? 'bg-danger-subtle' : 'bg-success-subtle' ?>">
                        <span class="fw-semibold"><?= Yii::t('app', 'Net') ?></span>
                        <span class="fs-5 fw-bold"><?= $fmt($net) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- بيانات النظام -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header fw-semibold"><i class="bi bi-person-gear"></i> بيانات الإدخال</div>
                <div class="card-body pt-2">
                    <?= $row(Yii::t('app', 'User Insert'), $userName) ?>
                    <?= $row($model->getAttributeLabel('created_at'), $model->created_at) ?>
                </div>
            </div>
        </div>
    </div>

</div>
