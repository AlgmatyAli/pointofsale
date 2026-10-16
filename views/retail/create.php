<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JqueryAsset;

/** @var yii\web\View $this */
/** @var app\models\CompanyInfo $company */
/** @var string[] $classes */
/** @var array $paymentTypes */
/** @var app\models\Client|null $defaultClient */
/** @var bool $canDiscount */
/** @var bool $canEditPrice */
/** @var array $cart */

$this->title = 'نقطة البيع';
$identity = Yii::$app->user->identity;

$config = [
    'urls' => [
        'products' => Url::to(['retail/products']),
        'clients' => Url::to(['retail/clients']),
        'cart' => Url::to(['retail/cart']),
        'scan' => Url::to(['retail/scan']),
        'add' => Url::to(['retail/add']),
        'qty' => Url::to(['retail/qty']),
        'price' => Url::to(['retail/price']),
        'minus' => Url::to(['retail/minus']),
        'delete' => Url::to(['retail/delete']),
        'deleteAll' => Url::to(['retail/delete-all']),
        'hold' => Url::to(['retail/hold']),
        'holded' => Url::to(['retail/holded']),
        'unhold' => Url::to(['retail/unhold']),
        'checkout' => Url::to(['retail/checkout']),
        'receipt' => Url::to(['retail/receipt']),
        'home' => Url::to(['/site/index']),
    ],
    'canDiscount' => $canDiscount,
    'canEditPrice' => $canEditPrice,
    'defaultClient' => $defaultClient ? $defaultClient->name : 'زبون نقدي',
    'cart' => $cart,
];
$this->registerJs('window.RETAIL = ' . Json::htmlEncode($config) . ';', \yii\web\View::POS_HEAD);
$this->registerJsFile('@web/js/retail.js', ['depends' => [JqueryAsset::class, \app\assets\AdminLteAsset::class]]);
?>

<div class="pos-app">
    <!-- الشريط العلوي -->
    <header class="pos-topbar">
        <div class="d-flex align-items-center gap-3">
            <a href="<?= Html::encode(Url::to(['/site/index'])) ?>" class="btn btn-outline-secondary btn-sm" title="الخروج من نقطة البيع">
                <i class="bi bi-box-arrow-right"></i>
            </a>
            <strong class="fs-5"><?= Html::encode($company->name) ?></strong>
            <span class="text-muted small"><i class="bi bi-person"></i> <?= Html::encode($identity->username) ?></span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span id="pos-clock" class="text-muted small"></span>
            <button type="button" class="btn btn-warning btn-sm" id="btn-holded" title="الفواتير المعلّقة">
                <i class="bi bi-pause-circle"></i> المعلّقة <span class="badge bg-dark" id="holded-count">0</span>
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-reprint" title="إعادة طباعة آخر إيصال" disabled>
                <i class="bi bi-printer"></i> آخر إيصال
            </button>
        </div>
    </header>

    <div class="pos-main">
        <!-- السلة (يمين) -->
        <section class="pos-cart">
            <div class="pos-cart-head">
                <span class="fw-bold"><i class="bi bi-cart3"></i> الفاتورة الحالية</span>
                <button type="button" class="btn btn-outline-danger btn-sm" id="btn-clear">
                    <i class="bi bi-trash"></i> إلغاء (F7)
                </button>
            </div>
            <div class="pos-cart-body" id="cart-body">
                <div class="text-center text-muted p-5" id="cart-empty">
                    <i class="bi bi-upc-scan fs-1"></i>
                    <div class="mt-2">امسح الباركود أو اختر صنفاً</div>
                </div>
                <table class="table table-sm align-middle mb-0 d-none" id="cart-table">
                    <thead>
                        <tr>
                            <th>الصنف</th>
                            <th class="text-center" style="width:92px">الكمية</th>
                            <th class="text-end" style="width:84px">السعر</th>
                            <th class="text-end" style="width:90px">الإجمالي</th>
                            <th style="width:64px"></th>
                        </tr>
                    </thead>
                    <tbody id="cart-rows"></tbody>
                </table>
            </div>
            <div class="pos-cart-foot">
                <div class="d-flex justify-content-between align-items-end mb-2">
                    <span class="fw-bold">الإجمالي <small class="text-muted" id="cart-count"></small></span>
                    <span class="pos-total" id="cart-total">0.000</span>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-warning" id="btn-hold" title="تعليق الفاتورة (F8)">
                        <i class="bi bi-pause-fill"></i> تعليق (F8)
                    </button>
                    <button type="button" class="btn btn-success flex-grow-1 pos-pay" id="btn-pay" disabled>
                        <i class="bi bi-cash-coin"></i> إتمام الدفع (F9)
                    </button>
                </div>
            </div>
        </section>

        <!-- البحث والأصناف (يسار) -->
        <section class="pos-products">
            <div class="pos-search position-relative" id="scan-wrap">
                <div class="input-group input-group-lg">
                    <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                    <input type="text" id="scan" class="form-control" autocomplete="off" autofocus
                           placeholder="امسح الباركود أو اكتب اسم الصنف  —  3*باركود لإضافة 3 قطع">
                    <button class="btn btn-outline-secondary" type="button" id="scan-clear" title="مسح الحقل"><i class="bi bi-x-lg"></i></button>
                </div>
                <div class="list-group pos-suggest d-none" id="scan-results" role="listbox"></div>
                <div id="scan-msg" class="small mt-1"></div>
            </div>

            <div class="pos-classes" id="class-tabs">
                <button type="button" class="btn btn-sm btn-primary class-tab" data-class="">الكل</button>
                <?php foreach ($classes as $class): ?>
                    <button type="button" class="btn btn-sm btn-outline-secondary class-tab" data-class="<?= Html::encode($class) ?>"><?= Html::encode($class) ?></button>
                <?php endforeach; ?>
            </div>

            <div class="pos-grid" id="product-grid"></div>
        </section>
    </div>
</div>

<!-- نافذة الدفع -->
<div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cash-coin"></i> إتمام الدفع</h5>
                <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                <div class="btn-group w-100 mb-3" role="group">
                    <input type="radio" class="btn-check" name="payWay" id="pw-cash" value="0" checked>
                    <label class="btn btn-outline-success" for="pw-cash">نقدي</label>
                    <input type="radio" class="btn-check" name="payWay" id="pw-credit" value="1">
                    <label class="btn btn-outline-danger" for="pw-credit">آجل</label>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <div class="text-muted small">الإجمالي</div>
                        <div class="fs-5 fw-bold" id="pay-total">0.000</div>
                    </div>
                    <div class="col-6 <?= $canDiscount ? '' : 'd-none' ?>">
                        <label class="small text-muted" for="pay-discount">الخصم</label>
                        <input type="number" min="0" step="any" class="form-control" id="pay-discount" value="0">
                    </div>
                </div>

                <div class="pos-net mb-3">
                    <span>الصافي</span>
                    <strong id="pay-net">0.000</strong>
                </div>

                <!-- نقدي -->
                <div id="pay-cash-box">
                    <label class="small text-muted" for="pay-tendered">المبلغ المستلم</label>
                    <input type="number" min="0" step="any" class="form-control form-control-lg" id="pay-tendered" inputmode="decimal">
                    <div class="d-flex flex-wrap gap-1 my-2" id="quick-cash"></div>
                    <div class="pos-change">
                        <span>الباقي للزبون</span>
                        <strong id="pay-change">0.000</strong>
                    </div>
                </div>

                <!-- آجل -->
                <div id="pay-credit-box" class="d-none">
                    <label class="small text-muted" for="pay-client">العميل</label>
                    <input type="text" class="form-control" id="pay-client" autocomplete="off" placeholder="ابحث بالاسم أو الهاتف">
                    <div class="list-group mt-1" id="pay-client-results"></div>
                    <div class="alert alert-info py-1 px-2 mt-2 d-none" id="pay-client-chosen"></div>
                    <label class="small text-muted mt-2" for="pay-paid">المدفوع مقدماً (اختياري)</label>
                    <input type="number" min="0" step="any" class="form-control" id="pay-paid" value="0">
                    <div class="small text-muted mt-1">المتبقي يُضاف إلى دين العميل: <strong id="pay-remaining">0.000</strong></div>
                </div>

                <?php if (count($paymentTypes) > 1): ?>
                    <div class="mt-3">
                        <label class="small text-muted" for="pay-type">طريقة الدفع</label>
                        <select class="form-select" id="pay-type">
                            <?php foreach ($paymentTypes as $t): ?>
                                <option value="<?= (int) $t['id'] ?>"><?= Html::encode($t['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="alert alert-danger py-2 mt-3 d-none" id="pay-error"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">رجوع</button>
                <button type="button" class="btn btn-success btn-lg px-5" id="btn-confirm-pay">
                    <i class="bi bi-check2-circle"></i> تأكيد وطباعة (Enter)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- الفواتير المعلّقة -->
<div class="modal fade" id="holdedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pause-circle"></i> الفواتير المعلّقة</h5>
                <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body" id="holded-body"></div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 start-0 p-3" id="toasts"></div>
