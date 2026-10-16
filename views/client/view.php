<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Client */
/* @var $providerPurchases yii\data\ActiveDataProvider */
/* @var $providerDebit yii\data\ActiveDataProvider */
/* @var $providerSales yii\data\ActiveDataProvider */
/* @var $providerInitsales yii\data\ActiveDataProvider */
/* @var $providerCredit yii\data\ActiveDataProvider */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Client'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$types = [
    0 => ['زبون', 'text-bg-info', 'bi-person'],
    1 => ['مورد', 'text-bg-warning', 'bi-truck'],
    2 => ['كلاهما', 'text-bg-primary', 'bi-arrow-left-right'],
];
$t = $types[(int) $model->type] ?? ['—', 'text-bg-secondary', 'bi-person'];

// عمود زر الطباعة / العرض
$actionCol = fn(string $route, string $icon, string $title) => [
    'class' => 'yii\grid\ActionColumn',
    'template' => '{view}',
    'contentOptions' => ['class' => 'text-center', 'style' => 'width:3.5rem'],
    'buttons' => [
        'view' => function ($url, $row, $key) use ($route, $icon, $title) {
            return Html::a('<i class="bi ' . $icon . '"></i>', Url::to([$route, 'id' => $row['id']]), [
                'class' => 'btn btn-sm btn-outline-secondary',
                'title' => $title,
                'data-pjax' => 0,
            ]);
        },
    ],
];
$money = fn(string $attr) => ['attribute' => $attr, 'pageSummary' => true];

$sections = [];

if ($providerPurchases->totalCount) {
    $sections[] = [
        'id' => 'purchases', 'title' => Yii::t('app', 'Purchases'), 'icon' => 'bi-bag', 'provider' => $providerPurchases,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            'billId',
            'clientBill',
            $money('total'),
            'notes',
            'at',
            $actionCol('purchases/print-bill', 'bi-printer', 'طباعة'),
        ],
    ];
}
if ($providerDebit->totalCount) {
    $sections[] = [
        'id' => 'receipt-debit', 'title' => Yii::t('app', 'Receipt') . ' (مدين)', 'icon' => 'bi-receipt', 'provider' => $providerDebit,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            'rId',
            $money('value'),
            'why',
            'at',
            $actionCol('receipt/view', 'bi-eye', 'عرض'),
        ],
    ];
}
if ($providerSales->totalCount) {
    $sections[] = [
        'id' => 'sales', 'title' => Yii::t('app', 'Sales'), 'icon' => 'bi-cart-check', 'provider' => $providerSales,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            'billId',
            $money('total'),
            $money('paid'),
            'notes',
            'at',
            $actionCol('sales/print', 'bi-printer', 'طباعة'),
        ],
    ];
}
if ($providerInitsales->totalCount) {
    $sections[] = [
        'id' => 'initsales', 'title' => Yii::t('app', 'Proforma Invoice'), 'icon' => 'bi-file-earmark-text', 'provider' => $providerInitsales,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            'billId',
            $money('total'),
            'notes',
            'at',
            $actionCol('sales/print', 'bi-printer', 'طباعة'),
        ],
    ];
}
if ($providerCredit->totalCount) {
    $sections[] = [
        'id' => 'receipt-credit', 'title' => Yii::t('app', 'Receipt') . ' (دائن)', 'icon' => 'bi-receipt-cutoff', 'provider' => $providerCredit,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            'rId',
            $money('value'),
            'why',
            'at',
            $actionCol('receipt/view', 'bi-eye', 'عرض'),
        ],
    ];
}

$this->registerCss(<<<'CSS'
.client-view .nav-tabs { flex-wrap: nowrap; overflow-x: auto; overflow-y: hidden; white-space: nowrap; }
.client-view .nav-tabs .nav-link { display: flex; align-items: center; gap: .35rem; }
.client-view .avatar { width: 3.5rem; height: 3.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; background: var(--bs-primary-bg-subtle); color: var(--bs-primary-text-emphasis); flex: none; }
.client-view .stat .val { font-size: 1.5rem; font-weight: 700; }
@media print { .client-view .d-print-none { display: none !important; } }
CSS);

$initial = mb_substr(trim((string) $model->name), 0, 1) ?: '?';
?>
<div class="client-view">

    <!-- العنوان + الأزرار -->
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <h4 class="mb-0 me-auto"><i class="bi bi-person-vcard"></i> <?= Html::encode(Yii::t('app', 'Client') . ' ' . $this->title) ?></h4>
        <div class="d-flex gap-2 d-print-none">
            <?= Html::a('<i class="bi bi-pencil-square"></i> تعديل', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="bi bi-arrow-right"></i> رجوع', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('<i class="bi bi-trash"></i> حذف', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-outline-danger',
                'data' => ['confirm' => 'هل أنت متأكد من حذف هذا العميل؟', 'method' => 'post'],
            ]) ?>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <!-- بطاقة العميل -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar"><?= Html::encode($initial) ?></div>
                        <div>
                            <div class="fs-5 fw-bold"><?= Html::encode($model->name) ?></div>
                            <span class="badge <?= $t[1] ?>"><i class="bi <?= $t[2] ?>"></i> <?= Html::encode($t[0]) ?></span>
                            <?php if ($model->post_paid): ?>
                                <span class="badge text-bg-success"><i class="bi bi-clock-history"></i> بيع آجل</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <?php if (!empty($model->mobile)): ?>
                            <li class="mb-1"><i class="bi bi-phone text-body-secondary me-1"></i>
                                <a href="tel:<?= Html::encode($model->mobile) ?>" dir="ltr"><?= Html::encode($model->mobile) ?></a></li>
                        <?php endif; ?>
                        <?php if (!empty($model->phone)): ?>
                            <li class="mb-1"><i class="bi bi-telephone text-body-secondary me-1"></i>
                                <a href="tel:<?= Html::encode($model->phone) ?>" dir="ltr"><?= Html::encode($model->phone) ?></a></li>
                        <?php endif; ?>
                        <?php if (!empty($model->email)): ?>
                            <li class="mb-1"><i class="bi bi-envelope text-body-secondary me-1"></i>
                                <a href="mailto:<?= Html::encode($model->email) ?>"><?= Html::encode($model->email) ?></a></li>
                        <?php endif; ?>
                        <?php if (!empty($model->address)): ?>
                            <li><i class="bi bi-geo-alt text-body-secondary me-1"></i> <?= Html::encode($model->address) ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- الأرصدة -->
        <div class="col-lg-6">
            <div class="row g-3 h-100">
                <div class="col-6">
                    <div class="card stat h-100 border-success-subtle">
                        <div class="card-body">
                            <div class="small text-body-secondary"><i class="bi bi-cash-stack"></i> <?= Html::encode($model->getAttributeLabel('balance')) ?></div>
                            <div class="val text-success"><?= Html::encode((string) $model->balance) ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card stat h-100 border-danger-subtle">
                        <div class="card-body">
                            <div class="small text-body-secondary"><i class="bi bi-receipt"></i> <?= Html::encode($model->getAttributeLabel('debt')) ?></div>
                            <div class="val text-danger"><?= Html::encode((string) $model->debt) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- السجلات -->
    <?php if ($sections): ?>
        <div class="card">
            <div class="card-header pb-0 border-bottom-0">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <?php foreach ($sections as $i => $s): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link<?= $i === 0 ? ' active' : '' ?>" id="tab-<?= $s['id'] ?>" data-bs-toggle="tab"
                                    data-bs-target="#pane-<?= $s['id'] ?>" type="button" role="tab"
                                    aria-controls="pane-<?= $s['id'] ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>">
                                <i class="bi <?= $s['icon'] ?>"></i> <?= Html::encode($s['title']) ?>
                                <span class="badge text-bg-secondary"><?= (int) $s['provider']->totalCount ?></span>
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="card-body tab-content p-2 p-md-3">
                <?php foreach ($sections as $i => $s): ?>
                    <div class="tab-pane fade<?= $i === 0 ? ' show active' : '' ?>" id="pane-<?= $s['id'] ?>" role="tabpanel"
                         aria-labelledby="tab-<?= $s['id'] ?>" tabindex="0">
                        <?= GridView::widget([
                            'dataProvider' => $s['provider'],
                            'bsVersion' => '5.x',
                            'pjax' => true,
                            'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-' . $s['id']]],
                            'showPageSummary' => true,
                            'panel' => false,
                            'toolbar' => false,
                            'export' => false,
                            'striped' => true,
                            'hover' => true,
                            'condensed' => true,
                            'responsiveWrap' => false,
                            'columns' => $s['columns'],
                        ]) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-secondary mb-0"><i class="bi bi-info-circle"></i> لا توجد حركات مسجلة لهذا العميل بعد.</div>
    <?php endif; ?>

</div>
