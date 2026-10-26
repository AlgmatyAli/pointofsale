<?php

use app\models\CompanyInfo;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $models */
/** @var array $lastBalance */
/** @var array $sumSql */
/** @var string $min_date */
/** @var string $max_date */

// تقرير بتخطيط مستقل (يعرض الشريط الجانبي والعلوي، ولا يدخلان في الطباعة)، ويُطبع داخل إطار معزول (printReport في layouts/report.php)
$this->context->layout = 'report';
$this->title = $this->title ?: 'تقرير الحركة اليومية';

// بيانات الشركة والشعار
$company = CompanyInfo::currentArray() ?: [];
$logo = (string) ($company['path'] ?? '');
if ($logo !== '' && !preg_match('~^(https?:)?//|^/~', $logo)) {
    $logo = Yii::getAlias('@web/') . ltrim($logo, '/');
}

$fmt = static fn ($value): string => number_format((float) $value, 3);

// ---- الرصيد السابق (نفس منطق الملف الأصلي) ----
$sumwared = 0;
$sumsader = 0;
$balance = 0;
if (!empty($lastBalance) && is_array($lastBalance)) {
    foreach ($lastBalance as $row) {
        $sumwared += (float) $row['wared'];
        $sumsader += (float) $row['sader'];
        $balance = $sumwared - $sumsader;
    }
}

// ---- مجاميع الحركات (كانت $disscount تُستخدم بلا تهيئة، فصارت تبدأ من صفر. النتيجة نفسها) ----
$sader = 0;
$wared = 0;
$disscount = 0;
foreach ($models as $row) {
    $sader = $sader + $row['sader'];
    $wared = $wared + $row['wared'];
    $disscount = $disscount + $row['disscount'];
}
?>

<!-- شريط الأزرار: لا يُطبع -->
<div class="no-print d-print-none mb-3 d-flex flex-wrap justify-content-between gap-2">
    <button type="button" class="btn btn-primary" onclick="printReport()">
        <i class="bi bi-printer"></i> <?= Yii::t('app', 'Print') ?>
    </button>

    <?= Html::a(
        '<i class="bi bi-arrow-right-circle"></i> ' . Yii::t('app', 'Cancel'),
        Yii::$app->request->referrer ?: ['index'],
        ['class' => 'btn btn-outline-secondary']
    ) ?>
</div>

<div id="report" class="report">

    <header class="report-header">
        <div class="report-brand">
            <?php if ($logo !== ''): ?>
                <img src="<?= Html::encode($logo) ?>" class="report-logo" alt="<?= Html::encode($company['name'] ?? '') ?>">
            <?php endif; ?>
            <div class="report-company"><?= Html::encode($company['name'] ?? '') ?></div>
        </div>

        <div class="report-heading">
            <h1 class="report-title">تقرير الحركة اليومية</h1>
            <div class="report-period">
                من تاريخ: <b><?= Html::encode((string) $min_date) ?></b>
                حتى تاريخ: <b><?= Html::encode((string) $max_date) ?></b>
            </div>
        </div>

        <div class="report-meta">
            تاريخ الطباعة: <?= date('Y-m-d H:i') ?><br>
            أعدّه: <?= Html::encode((string) Yii::$app->user->identity?->username) ?>
        </div>
    </header>

    <!-- حركات الفترة -->
    <div style="overflow-x:auto">
        <table class="report-table">
            <thead>
                <tr>
                    <th class="rp-idx">#</th>
                    <th>بيان الحركة</th>
                    <th>التاريخ</th>
                    <th>كيفية السداد</th>
                    <th class="rp-dr">الصادر</th>
                    <th class="rp-cr">الوارد</th>
                </tr>
            </thead>

            <tbody>
                <!-- الرصيد قبل بداية الفترة -->
                <tr class="rp-open">
                    <td colspan="5">رصيد قبل <?= Html::encode((string) $min_date) ?></td>
                    <td class="rp-num rp-bal"><?= $fmt($balance) ?></td>
                </tr>

                <?php $i = 1; foreach ($models as $row): ?>
                    <tr>
                        <td class="rp-idx"><?= $i++ ?></td>
                        <td><?= Html::encode((string) $row['description']) ?></td>
                        <td class="text-nowrap" style="white-space:nowrap"><?= Html::encode((string) $row['date_']) ?></td>
                        <td><?= Html::encode((string) $row['paymentType']) ?></td>
                        <td class="rp-num rp-dr<?= $row['sader'] == 0 ? ' rp-zero' : '' ?>"><?= $fmt($row['sader']) ?></td>
                        <td class="rp-num rp-cr<?= $row['wared'] == 0 ? ' rp-zero' : '' ?>"><?= $fmt($row['wared']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="4">الإجمالي</td>
                    <td class="rp-num rp-dr"><?= $fmt($sader) ?></td>
                    <td class="rp-num rp-cr"><?= $fmt($wared) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- ملخص الفترة -->
    <section class="report-summary">
        <div class="report-stat">
            <div class="rp-k">اجمالي التخفيض</div>
            <div class="rp-v"><?= $fmt($disscount) ?></div>
        </div>
        <div class="report-stat">
            <div class="rp-k">رصيد قبل <?= Html::encode((string) $min_date) ?></div>
            <div class="rp-v"><?= $fmt($balance) ?></div>
        </div>
        <div class="report-stat">
            <div class="rp-k">الرصيـد</div>
            <div class="rp-v rp-bal"><?= $fmt($wared - $sader) ?></div>
        </div>
    </section>

    <!-- ملخص حسب نوع السداد -->
    <h2 style="font-size:16px;font-weight:800;margin:22px 0 8px">ملخص حسب نوع السداد</h2>
    <div style="overflow-x:auto">
        <table class="report-table" style="max-width:640px">
            <thead>
                <tr>
                    <th>نوع السداد</th>
                    <th class="rp-dr">الصادر</th>
                    <th class="rp-cr">الوارد</th>
                    <th class="rp-bal">الصافي</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sumSql as $sumRow):
                    $ptype = $sumRow['max(payment_types.name)'] ?? ($sumRow['name'] ?? '');
                    $s = $sumRow['sum(sader)'] ?? 0;
                    $w = $sumRow['sum(wared)'] ?? 0;
                ?>
                    <tr>
                        <td><b><?= Html::encode((string) $ptype) ?></b></td>
                        <td class="rp-num rp-dr"><?= $fmt($s) ?></td>
                        <td class="rp-num rp-cr"><?= $fmt($w) ?></td>
                        <td class="rp-num rp-bal"><?= $fmt($w - $s) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
