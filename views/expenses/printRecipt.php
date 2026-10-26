<?php

use app\models\CompanyInfo;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Expenses $model */

// تخطيط مستقل (يعرض الشريط الجانبي والعلوي، ولا يدخلان في الطباعة). زر Print يستدعي printInvoice('receipt') (طباعة داخل إطار معزول)
$this->context->layout = 'invoice';
$this->title = 'ايصال صرف';

$company = CompanyInfo::currentArray() ?: [];

$logo = (string) ($company['path'] ?? '');
if ($logo !== '' && !preg_match('~^(https?:)?//|^/~', $logo)) {
    $logo = Yii::getAlias('@web/') . ltrim($logo, '/');
}
?>

<!-- شريط الأزرار: لا يُطبع -->
<div class="no-print d-print-none mb-3 d-flex flex-wrap justify-content-between gap-2">
    <button type="button" class="btn btn-primary" onclick="printInvoice('receipt')">
        <i class="bi bi-printer"></i> <?= Yii::t('app', 'Print') ?>
    </button>
    <?= Html::a(
        '<i class="bi bi-arrow-right-circle"></i> ' . Yii::t('app', 'Cancel'),
        Yii::$app->request->referrer ?: ['index'],
        ['class' => 'btn btn-outline-secondary']
    ) ?>
</div>

<!-- المحتوى المطبوع (الأنماط داخله ليحملها إطار الطباعة المعزول) -->
<div id="receipt">
    <style>
        #receipt { max-width: 820px; margin: 0 auto; font-family: Tahoma, Arial, sans-serif; color: #212529; }
        #receipt .rcpt { --c: #dc3545; border: 1.5px solid #adb5bd; border-radius: 8px; padding: 14px 18px; background: #fff; break-inside: avoid; page-break-inside: avoid; }
        #receipt .rcpt-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; border-bottom: 3px solid var(--c); padding-bottom: 10px; }
        #receipt .rcpt-brand { display: flex; align-items: center; gap: 10px; }
        #receipt .rcpt-logo { width: 64px; height: 64px; object-fit: contain; }
        #receipt .rcpt-company { font-size: 18px; font-weight: 700; }
        #receipt .rcpt-muted { font-size: 12px; color: #6c757d; }
        #receipt .rcpt-badge { text-align: center; background: var(--c); color: #fff; border-radius: 8px; padding: 6px 16px; min-width: 130px; }
        #receipt .rcpt-badge-title { font-size: 13px; }
        #receipt .rcpt-badge-no { font-size: 20px; font-weight: 700; line-height: 1.2; }
        #receipt .rcpt-meta { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin: 12px 0; font-size: 14px; }
        #receipt .rcpt-amount { border: 2px solid var(--c); color: var(--c); border-radius: 8px; padding: 4px 14px; font-size: 16px; }
        #receipt .rcpt-amount strong { font-size: 22px; }
        #receipt .rcpt-label { font-weight: 700; color: #495057; }
        #receipt .rcpt-line { padding: 9px 0; border-bottom: 1px dotted #ced4da; font-size: 15px; }
        #receipt .rcpt-line:last-child { border-bottom: 0; }
        #receipt .rcpt-foot { display: flex; justify-content: flex-start; margin-top: 22px; padding-top: 10px; border-top: 1px solid #dee2e6; }
        #receipt .rcpt-sign { display: flex; align-items: flex-end; gap: 8px; flex: 1; max-width: 320px; }
        #receipt .rcpt-sign-line { flex: 1; border-bottom: 1px solid #212529; height: 22px; }
        @media (max-width: 575.98px) {
            #receipt .rcpt-head { flex-direction: column; align-items: stretch; }
            #receipt .rcpt-badge { align-self: stretch; }
        }
    </style>

    <section class="rcpt">
        <header class="rcpt-head">
            <div class="rcpt-brand">
                <?php if ($logo !== ''): ?>
                    <img src="<?= Html::encode($logo) ?>" alt="<?= Html::encode($company['name'] ?? '') ?>" class="rcpt-logo">
                <?php endif; ?>
                <div>
                    <div class="rcpt-company"><?= Html::encode($company['name'] ?? '') ?></div>
                    <?php if (!empty($company['work'])): ?>
                        <div class="rcpt-muted"><?= Html::encode($company['work']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="rcpt-badge">
                <div class="rcpt-badge-title">ايصال رقـم</div>
                <div class="rcpt-badge-no"><?= Html::encode((string) $model->id) ?></div>
            </div>
        </header>

        <div class="rcpt-meta">
            <div><span class="rcpt-label">التاريخ:</span> <?= Html::encode((string) $model->at) ?></div>
            <div class="rcpt-amount">
                <span class="rcpt-label">المبلغ:</span>
                <strong><?= Html::encode((string) $model->value) ?></strong>
                <span>دينار</span>
            </div>
        </div>

        <div class="rcpt-lines">
            <div class="rcpt-line"><span class="rcpt-label">استلمت انـا:</span> <?= Html::encode((string) $model->expenseTo) ?></div>
            <div class="rcpt-line"><span class="rcpt-label">مبلغ وقـدره:</span> <?= Html::encode((string) $model->value) ?> دينار</div>
            <div class="rcpt-line"><span class="rcpt-label">ودلك مقابـل:</span> <?= Html::encode((string) $model->why) ?></div>
        </div>

        <footer class="rcpt-foot">
            <div class="rcpt-sign"><span class="rcpt-label">اعتماد:</span><span class="rcpt-sign-line"></span></div>
        </footer>
    </section>
</div>
