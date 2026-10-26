<?php

use yii\helpers\Html;

/** @var app\models\Sales $model */
/** @var array $lines */
/** @var app\models\CompanyInfo $company */
/** @var app\models\Client|null $client */
/** @var float|null $balance */

// PDF الفاتورة المرسل للزبون عبر واتساب بنفس تصميم sales/print (التنسيق في web/css/invoice-pdf.css).
// بلا أي سعر تكلفة، ومستقل عن المستخدم المسجَّل.
$titles = [1 => 'Sales Invoice', 2 => 'Back Sales Invoice', 3 => 'Reservation Invoice', 4 => 'Proforma Invoice'];
$invoiceTitle = isset($titles[(int) $model->type]) ? Yii::t('app', $titles[(int) $model->type]) : 'فاتورة';

// طريقة الدفع (في المسترجع معنى 1 و2 يختلف عن باقي الأنواع) كما في sales/print
$payWayText = '';
if (in_array((int) $model->type, [1, 3, 4], true)) {
    $payWayText = ['0' => 'نقدا', '1' => 'آجل', '2' => 'دفعة على الحساب'][(string) $model->payWay] ?? '';
} elseif ((int) $model->type === 2) {
    $payWayText = ['1' => 'نقدا', '2' => 'آجل'][(string) $model->payWay] ?? '';
}

$num = function ($v) { return number_format((float) $v, 3); };
$qty = function ($v) { return rtrim(rtrim(number_format((float) $v, 3, '.', ''), '0'), '.'); };
$total = 0.0;
foreach ($lines as $l) {
    $total += (float) $l['quantity'] * (float) $l['salePrice'];
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

<div class="inv-title"><?= Html::encode($invoiceTitle) ?></div>

<table class="layout">
    <tr>
        <td style="width:50%; text-align:right">
            <div class="client-head">تفاصيل الزبون</div>
            <div><b><?= Html::encode((string) $client?->name) ?></b></div>
            <?php if (!empty($client?->phone)): ?><div><?= Html::encode($client->phone) ?></div><?php endif; ?>
            <?php if (!empty($client?->email)): ?><div><?= Html::encode($client->email) ?></div><?php endif; ?>
        </td>
        <td style="width:50%">
            <table class="info" style="margin-right:auto">
                <tr><td class="lbl">رقـم الفاتورة:</td><td><?= Html::encode((string) $model->billId) ?></td></tr>
                <tr><td class="lbl">تاريخ الفاتورة:</td><td><?= Html::encode((string) $model->at) ?></td></tr>
                <tr><td class="lbl">توقيت الفاتورة:</td><td><?= $model->created_at ? Yii::$app->formatter->asTime($model->created_at) : '' ?></td></tr>
                <?php if (!empty($model->deleviryAt)): ?>
                    <tr><td class="lbl">تاريخ التسليم:</td><td><?= Html::encode((string) $model->deleviryAt) ?></td></tr>
                <?php endif; ?>
                <tr><td class="lbl">طريقة الدفع:</td><td><?= Html::encode($payWayText) ?></td></tr>
            </table>
        </td>
    </tr>
</table>

<table class="items">
    <thead>
        <tr>
            <th style="width:6%">#</th>
            <th style="width:49%"><?= Yii::t('app', 'Name') ?></th>
            <th style="width:15%"><?= Yii::t('app', 'Company') ?></th>
            <th style="width:10%"><?= Yii::t('app', 'quantity') ?></th>
            <th style="width:10%"><?= Yii::t('app', 'Price') ?></th>
            <th style="width:10%"><?= Yii::t('app', 'Total') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($lines as $i => $l): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= Html::encode($l['name']) ?></td>
                <td><?= Html::encode((string) $l['company']) ?></td>
                <td><?= $qty($l['quantity']) ?></td>
                <td><?= $num($l['salePrice']) ?></td>
                <td><?= $num((float) $l['quantity'] * (float) $l['salePrice']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<table class="layout">
    <tr>
        <td style="width:55%"></td>
        <td style="width:45%">
            <table class="sums" style="width:100%">
                <tr><th>الاجمـــالي</th><td><?= $num($total) ?></td></tr>
                <tr><th>المدفـوع</th><td><?= $num($model->paid) ?></td></tr>
                <tr><th><?= Yii::t('app', 'Disscount') ?></th><td><?= $num($model->disscount) ?></td></tr>
                <tr class="<?= $balance === null ? 'last' : '' ?>"><th>الصـــافي</th><td><?= $num($total - (float) $model->paid - (float) $model->disscount) ?></td></tr>
                <?php if ($balance !== null): ?>
                    <tr class="last"><th>الرصيد</th><td><?= $num($balance) ?></td></tr>
                <?php endif; ?>
            </table>
        </td>
    </tr>
</table>

<?php if (!empty($company->terms)): ?>
    <div class="terms">
        <div><b>الشروط والأحكام:</b></div>
        <?= \yii\helpers\HtmlPurifier::process((string) $company->terms) ?>
    </div>
<?php endif; ?>
