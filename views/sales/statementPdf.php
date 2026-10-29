<?php

use yii\helpers\Html;

/** @var array $models */
/** @var array $lastBalance */
/** @var int $type */
/** @var app\models\Client|null $client */
/** @var app\models\CompanyInfo $company */

// PDF كشف حساب العميل المرسل عبر واتساب (sales/statement-pdf). نفس حساب الرصيد في client/histransrep.php،
// والتنسيق من web/css/invoice-pdf.css. بلا روابط ولا أزرار ولا أي سعر تكلفة.
$fmt = static fn($v): string => number_format((float) $v, 3);

$balance = 0.0;
foreach ($lastBalance as $lb) {
    $balance += (float) ($lb['sader'] ?? 0) - (float) ($lb['wared'] ?? 0);
}

$logoFile = '';
if (!empty($company->path)) {
    $candidate = Yii::getAlias('@webroot') . '/' . ltrim((string) $company->path, '/');
    if (is_file($candidate)) {
        $logoFile = $candidate;
    }
}
?>
<table class="layout">
    <tr>
        <td style="width:70%; text-align:right">
            <div class="company-name"><?= Html::encode($company->name) ?></div>
            <?php if (!empty($company->work)): ?><div class="company-name1"><?= Html::encode($company->work) ?></div><?php endif; ?>
            <?php if (!empty($company->address)): ?><div class="company-sub"><?= Html::encode($company->address) ?></div><?php endif; ?>
            <div class="company-sub"><?= Html::encode(implode(' | ', array_filter([$company->phone1, $company->phone2]))) ?></div>
        </td>
        <td style="width:30%; text-align:left">
            <?php if ($logoFile !== ''): ?><img src="<?= Html::encode($logoFile) ?>" class="logo"><?php endif; ?>
        </td>
    </tr>
</table>

<div class="inv-title">كشف حساب</div>

<table class="layout">
    <tr>
        <td style="width:60%; text-align:right">
            <div>السيد: <b><?= Html::encode((string) $client?->name) ?></b></div>
            <div>رقم الحساب: <?= (int) $client?->id ?></div>
        </td>
        <td style="width:40%; text-align:left">حتى تاريخ: <b><?= date('Y-m-d') ?></b></td>
    </tr>
</table>

<table class="items">
    <thead>
        <tr>
            <th style="width:6%">#</th>
            <th style="width:14%">التاريخ</th>
            <th>البيان</th>
            <th style="width:14%">دائن</th>
            <th style="width:14%">مدين</th>
            <th style="width:15%">الرصيد</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="5"><b>رصيد سابق</b></td>
            <td><?= $fmt($balance) ?></td>
        </tr>
        <?php
        $sumwared = 0.0;
        $sumsader = 0.0;
        $current = $balance;
        foreach ($models as $i => $row):
            $wared = (float) ($row['wared'] ?? 0);
            $sader = (float) ($row['sader'] ?? 0);
            $sumwared += $wared;
            $sumsader += $sader;
            $current = ((int) ($row['type'] ?? 0) === 1)
                ? ($sumwared - $sumsader) + $balance
                : ($sumsader - $sumwared) + $balance;
        ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= Html::encode((string) ($row['trandate'] ?? '')) ?></td>
                <td><?= Html::encode(($row['kind'] ?? '') . ' ' . ($row['billId'] ?? '')) ?></td>
                <td><?= $fmt($wared) ?></td>
                <td><?= $fmt($sader) ?></td>
                <td><?= $fmt($current) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<table class="layout">
    <tr>
        <td style="width:55%"></td>
        <td style="width:45%">
            <table class="sums" style="width:100%">
                <tr><th>إجمالي الدائن</th><td><?= $fmt($sumwared) ?></td></tr>
                <tr><th>إجمالي المدين</th><td><?= $fmt($sumsader) ?></td></tr>
                <tr class="last"><th>الرصيد</th><td><?= $fmt($current) ?></td></tr>
            </table>
        </td>
    </tr>
</table>
