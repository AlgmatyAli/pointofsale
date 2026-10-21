<?php

use yii\helpers\Html;

/** @var app\models\Sales $model */
/** @var array $lines */
/** @var app\models\CompanyInfo $company */
/** @var app\models\Client|null $client */
/** @var float|null $balance */

// PDF الفاتورة المرسل للزبون: بلا أي سعر تكلفة، ومستقل عن المستخدم المسجَّل
$titles = [1 => 'فاتورة مبيعات', 2 => 'فاتورة مرتجع', 3 => 'فاتورة حجز', 4 => 'فاتورة مبدئية'];
$title = $titles[(int) $model->type] ?? 'فاتورة';
$payWay = ((int) $model->payWay === 1) ? 'آجل' : 'نقدا';
$net = (float) $model->total - (float) $model->disscount;
$num = function ($v) { return number_format((float) $v, 3, '.', ','); };
$qty = function ($v) { return rtrim(rtrim(number_format((float) $v, 3, '.', ''), '0'), '.'); };

$logoFile = '';
if (!empty($company->path)) {
    $candidate = Yii::getAlias('@webroot') . '/' . ltrim((string) $company->path, '/');
    if (is_file($candidate)) {
        $logoFile = $candidate;
    }
}
?>
<style>
    body { direction: rtl; text-align: right; font-family: sans-serif; font-size: 12pt; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #eeeeee; border: 1px solid #444; padding: 5px; text-align: right; }
    td { border: 1px solid #444; padding: 5px; }
    .n { text-align: left; }
    .head td { border: 0; padding: 2px 0; }
    .tot td { border: 0; padding: 3px 0; }
    .big { font-size: 14pt; font-weight: bold; }
</style>

<table class="head">
    <tr>
        <td style="width:70%">
            <div class="big"><?= Html::encode($company->name) ?></div>
            <?php if (!empty($company->work)): ?><div><?= Html::encode($company->work) ?></div><?php endif; ?>
            <?php if (!empty($company->address)): ?><div><?= Html::encode($company->address) ?></div><?php endif; ?>
            <div><?= Html::encode(trim($company->phone1 . '  ' . $company->phone2)) ?></div>
        </td>
        <td style="width:30%; text-align:left">
            <?php if ($logoFile !== ''): ?><img src="<?= Html::encode($logoFile) ?>" style="height:70px"><?php endif; ?>
        </td>
    </tr>
</table>

<h2 style="text-align:center; margin: 10px 0"><?= Html::encode($title) ?></h2>

<table class="head">
    <tr>
        <td>رقم الفاتورة: <b><?= (int) $model->billId ?></b></td>
        <td>التاريخ: <b><?= Html::encode($model->at) ?></b></td>
        <td>الدفع: <b><?= $payWay ?></b></td>
    </tr>
    <tr>
        <td colspan="3">السيد: <b><?= Html::encode($client ? $client->name : '') ?></b></td>
    </tr>
</table>

<br>
<table>
    <thead>
        <tr>
            <th style="width:6%">#</th>
            <th>الصنف</th>
            <th class="n" style="width:12%">الكمية</th>
            <th class="n" style="width:16%">السعر</th>
            <th class="n" style="width:18%">الإجمالي</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($lines as $i => $l): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= Html::encode($l['name']) ?></td>
                <td class="n"><?= $qty($l['quantity']) ?></td>
                <td class="n"><?= $num($l['salePrice']) ?></td>
                <td class="n"><?= $num($l['quantity'] * $l['salePrice']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<br>
<table class="tot" style="width:50%">
    <tr><td>الإجمالي</td><td class="n"><?= $num($model->total) ?></td></tr>
    <?php if ((float) $model->disscount > 0): ?>
        <tr><td>الخصم</td><td class="n"><?= $num($model->disscount) ?></td></tr>
    <?php endif; ?>
    <tr class="big"><td>الصافي</td><td class="n"><?= $num($net) ?></td></tr>
    <?php if ((int) $model->payWay === 1): ?>
        <tr><td>المدفوع</td><td class="n"><?= $num($model->paid) ?></td></tr>
        <tr><td>المتبقي من الفاتورة</td><td class="n"><?= $num($net - (float) $model->paid) ?></td></tr>
        <?php if ($balance !== null): ?>
            <tr><td>إجمالي رصيدكم</td><td class="n"><?= $num($balance) ?></td></tr>
        <?php endif; ?>
    <?php endif; ?>
</table>

<?php if (!empty($company->terms)): ?>
    <br><div style="font-size:10pt"><?= strip_tags((string) $company->terms, '<b><br><p><ul><li>') ?></div>
<?php endif; ?>
