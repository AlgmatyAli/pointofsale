<?php

use yii\helpers\Html;

/** @var app\models\Sales $sale */
/** @var array $lines */
/** @var app\models\CompanyInfo $company */
/** @var app\models\Client|null $client */
/** @var bool $isDefaultClient */
/** @var float|null $balance */
/** @var bool $print */
/** @var string|null $user */

// إيصال حراري 80mm: صفحة مستقلة بلا layout، تنسيقها داخلي، وتُطبع تلقائياً عند print=1
$net = (float) $sale->total - (float) $sale->disscount;
$num = function ($v) { return number_format((float) $v, 3, '.', ''); };
$qty = function ($v) { return rtrim(rtrim(number_format((float) $v, 3, '.', ''), '0'), '.'); };
$payWay = ((int) $sale->payWay === 1) ? 'آجل' : 'نقدا';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>إيصال <?= (int) $sale->billId ?></title>
    <style>
        @page { size: 80mm auto; margin: 0; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 3mm 3mm 6mm; width: 80mm; font-family: Tahoma, Arial, sans-serif; font-size: 12px; color: #000; background: #fff; }
        .c { text-align: center; }
        .b { font-weight: bold; }
        .big { font-size: 15px; }
        hr { border: 0; border-top: 1px dashed #000; margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: right; border-bottom: 1px solid #000; font-size: 11px; padding: 2px 0; }
        td { padding: 2px 0; vertical-align: top; }
        .n { text-align: left; white-space: nowrap; }
        .row { display: flex; justify-content: space-between; margin: 1px 0; }
        .tot { font-size: 15px; font-weight: bold; }
        .screen-only { margin: 8px 0; text-align: center; }
        @media print { .screen-only { display: none; } }
    </style>
</head>
<body>
    <div class="c b big"><?= Html::encode($company->name) ?></div>
    <?php if (!empty($company->work)): ?><div class="c"><?= Html::encode($company->work) ?></div><?php endif; ?>
    <?php if (!empty($company->address)): ?><div class="c"><?= Html::encode($company->address) ?></div><?php endif; ?>
    <?php if (!empty($company->phone1)): ?><div class="c"><?= Html::encode($company->phone1) ?></div><?php endif; ?>
    <hr>
    <div class="row"><span>فاتورة رقم</span><span class="b"><?= (int) $sale->billId ?></span></div>
    <div class="row"><span>التاريخ</span><span><?= Html::encode(substr((string) $sale->created_at, 0, 16)) ?></span></div>
    <?php if ($user): ?><div class="row"><span>الكاشير</span><span><?= Html::encode($user) ?></span></div><?php endif; ?>
    <?php if (!$isDefaultClient && $client): ?><div class="row"><span>العميل</span><span><?= Html::encode($client->name) ?></span></div><?php endif; ?>
    <div class="row"><span>الدفع</span><span><?= $payWay ?></span></div>
    <hr>
    <table>
        <thead>
            <tr><th>الصنف</th><th class="n">الكمية</th><th class="n">السعر</th><th class="n">الإجمالي</th></tr>
        </thead>
        <tbody>
            <?php foreach ($lines as $l): ?>
                <tr>
                    <td><?= Html::encode($l['name']) ?></td>
                    <td class="n"><?= $qty($l['quantity']) ?></td>
                    <td class="n"><?= $num($l['salePrice']) ?></td>
                    <td class="n"><?= $num($l['quantity'] * $l['salePrice']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <hr>
    <div class="row"><span>الإجمالي</span><span><?= $num($sale->total) ?></span></div>
    <?php if ((float) $sale->disscount > 0): ?>
        <div class="row"><span>الخصم</span><span><?= $num($sale->disscount) ?></span></div>
    <?php endif; ?>
    <div class="row tot"><span>الصافي</span><span><?= $num($net) ?></span></div>
    <?php if ((int) $sale->payWay === 1): ?>
        <div class="row"><span>المدفوع</span><span><?= $num($sale->paid) ?></span></div>
        <div class="row"><span>المتبقي</span><span><?= $num($net - (float) $sale->paid) ?></span></div>
        <?php if ($balance !== null): ?>
            <div class="row"><span>رصيد العميل</span><span><?= $num($balance) ?></span></div>
        <?php endif; ?>
    <?php endif; ?>
    <hr>
    <div class="c">شكراً لزيارتكم</div>
    <?php if (!empty($company->terms)): ?><div class="c" style="font-size:10px"><?= strip_tags((string) $company->terms) ?></div><?php endif; ?>

    <div class="screen-only"><button onclick="window.print()">طباعة</button></div>
    <?php if ($print): ?>
        <script>window.addEventListener('load', function () { setTimeout(function () { window.focus(); window.print(); }, 200); });</script>
    <?php endif; ?>
</body>
</html>
