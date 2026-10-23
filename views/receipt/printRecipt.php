<?php

use app\models\CompanyInfo;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Receipts $model */
/** @var float $balance */

// تخطيط مستقل بلا الشريط الجانبي والعلوي. زر Print يستدعي printInvoice('receipts') (طباعة داخل إطار معزول)
$this->context->layout = 'invoice';
$this->title = 'ايصال';

$company = CompanyInfo::currentArray() ?: [];

$logo = (string) ($company['path'] ?? '');
if ($logo !== '' && !preg_match('~^(https?:)?//|^/~', $logo)) {
    $logo = Yii::getAlias('@web/') . ltrim($logo, '/');
}

// قبض (type = 1) أو صرف: نفس منطق الملف الأصلي
$isReceipt = ($model->type == 1);
$voucherTitle = $isReceipt ? 'ايصال قبض رقم' : 'ايصال صرف رقم';
$currency = (string) ($model->currancy0?->name ?? '');
$clientName = (string) ($model->c?->name ?? '');
$companyName = (string) ($company['name'] ?? '');

/**
 * نسخة واحدة من الإيصال. تُعرض مرتين (الأصل + نسخة) ويفصل بينهما خط قص.
 * $balanceLabel: عنوان خانة الرصيد في هذه النسخة.
 */
$voucher = function (string $balanceLabel) use ($model, $company, $logo, $isReceipt, $voucherTitle, $currency, $clientName, $companyName, $balance) {
    ob_start(); ?>
    <section class="rcpt <?= $isReceipt ? 'rcpt-in' : 'rcpt-out' ?>">
        <header class="rcpt-head">
            <div class="rcpt-brand">
                <?php if ($logo !== ''): ?>
                    <img src="<?= Html::encode($logo) ?>" alt="<?= Html::encode($companyName) ?>" class="rcpt-logo">
                <?php endif; ?>
                <div>
                    <div class="rcpt-company"><?= Html::encode($companyName) ?></div>
                    <?php if (!empty($company['work'])): ?>
                        <div class="rcpt-muted"><?= Html::encode($company['work']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="rcpt-badge">
                <div class="rcpt-badge-title"><?= Html::encode($voucherTitle) ?></div>
                <div class="rcpt-badge-no"><?= Html::encode((string) $model->rId) ?></div>
            </div>
        </header>

        <div class="rcpt-meta">
            <div><span class="rcpt-label">التاريخ:</span> <?= Html::encode((string) $model->at) ?></div>
            <div class="rcpt-amount">
                <span class="rcpt-label">المبلغ:</span>
                <strong><?= number_format((float) $model->value, 3) ?></strong>
                <span><?= Html::encode($currency) ?></span>
            </div>
        </div>

        <div class="rcpt-lines">
            <div class="rcpt-line"><span class="rcpt-label">استلمت انا:</span> <?= Html::encode($clientName) ?></div>
            <div class="rcpt-line"><span class="rcpt-label">مبلغ وقـدره:</span> فقط <?= Html::encode((string) $model->value) ?> <?= Html::encode($currency) ?> لاغير</div>
            <div class="rcpt-line"><span class="rcpt-label">ودلك مقابـل:</span> <?= Html::encode((string) $model->why) ?></div>
        </div>

        <footer class="rcpt-foot">
            <div class="rcpt-sign"><span class="rcpt-label">اعتماد:</span><span class="rcpt-sign-line"></span></div>
            <div class="rcpt-balance"><?= Html::encode($balanceLabel) ?>: <strong><?= number_format((float) $balance, 3) ?></strong></div>
        </footer>
    </section>
    <?php
    return ob_get_clean();
};
?>

<!-- شريط الأزرار: لا يُطبع -->
<div class="no-print d-print-none mb-3 d-flex flex-wrap justify-content-between gap-2">
    <button type="button" class="btn btn-primary" onclick="printInvoice('receipts')">
        <i class="bi bi-printer"></i> <?= Yii::t('app', 'Print') ?>
    </button>
    <?= Html::a(
        '<i class="bi bi-arrow-right-circle"></i> ' . Yii::t('app', 'Cancel'),
        Yii::$app->request->referrer ?: ['index'],
        ['class' => 'btn btn-outline-secondary']
    ) ?>
</div>

<!-- المحتوى المطبوع (الأنماط داخله ليحملها إطار الطباعة المعزول) -->
<div id="receipts">
    <style>
        #receipts { max-width: 820px; margin: 0 auto; font-family: Tahoma, Arial, sans-serif; color: #212529; }
        #receipts .rcpt { border: 1.5px solid #adb5bd; border-radius: 8px; padding: 14px 18px; background: #fff; break-inside: avoid; page-break-inside: avoid; }
        #receipts .rcpt-in  { --c: #198754; }
        #receipts .rcpt-out { --c: #dc3545; }
        #receipts .rcpt-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; border-bottom: 3px solid var(--c); padding-bottom: 10px; }
        #receipts .rcpt-brand { display: flex; align-items: center; gap: 10px; }
        #receipts .rcpt-logo { width: 64px; height: 64px; object-fit: contain; }
        #receipts .rcpt-company { font-size: 18px; font-weight: 700; }
        #receipts .rcpt-muted { font-size: 12px; color: #6c757d; }
        #receipts .rcpt-badge { text-align: center; background: var(--c); color: #fff; border-radius: 8px; padding: 6px 16px; min-width: 130px; }
        #receipts .rcpt-badge-title { font-size: 13px; }
        #receipts .rcpt-badge-no { font-size: 20px; font-weight: 700; line-height: 1.2; }
        #receipts .rcpt-meta { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin: 12px 0; font-size: 14px; }
        #receipts .rcpt-amount { border: 2px solid var(--c); color: var(--c); border-radius: 8px; padding: 4px 14px; font-size: 16px; }
        #receipts .rcpt-amount strong { font-size: 22px; }
        #receipts .rcpt-label { font-weight: 700; color: #495057; }
        #receipts .rcpt-line { padding: 7px 0; border-bottom: 1px dotted #ced4da; font-size: 14px; }
        #receipts .rcpt-line:last-child { border-bottom: 0; }
        #receipts .rcpt-foot { display: flex; justify-content: space-between; align-items: flex-end; gap: 12px; margin-top: 14px; padding-top: 10px; border-top: 1px solid #dee2e6; }
        #receipts .rcpt-sign { display: flex; align-items: flex-end; gap: 8px; flex: 1; max-width: 300px; }
        #receipts .rcpt-sign-line { flex: 1; border-bottom: 1px solid #212529; height: 22px; }
        #receipts .rcpt-balance { font-size: 15px; background: #f1f3f5; border-radius: 6px; padding: 4px 12px; }
        #receipts .rcpt-cut { display: flex; align-items: center; gap: 8px; margin: 18px 0; color: #868e96; font-size: 12px; }
        #receipts .rcpt-cut::before, #receipts .rcpt-cut::after { content: ""; flex: 1; border-top: 2px dashed #adb5bd; }
        @media (max-width: 575.98px) {
            #receipts .rcpt-head { flex-direction: column; align-items: stretch; }
            #receipts .rcpt-badge { align-self: stretch; }
        }
    </style>

    <?= $voucher('الرصيـد') ?>
    <div class="rcpt-cut">&#9986; قص هنا</div>
    <?= $voucher('الرصيـد') ?>
</div>
