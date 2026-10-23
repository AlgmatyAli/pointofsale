<?php

use app\models\CompanyInfo;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $models */
/** @var string $min_date */
/** @var string $max_date */

// تقرير بتخطيط مستقل بلا الشريط الجانبي والعلوي، ويُطبع داخل إطار معزول (printReport في layouts/report.php)
$this->context->layout = 'report';
$this->title = $this->title ?: 'تقرير الأرباح اليومية';

// بيانات الشركة والشعار
$company = CompanyInfo::currentArray() ?: [];
$logo = (string) ($company['path'] ?? '');
if ($logo !== '' && !preg_match('~^(https?:)?//|^/~', $logo)) {
    $logo = Yii::getAlias('@web/') . ltrim($logo, '/');
}

$fmt = static fn ($value, int $d = 3): string => number_format((float) $value, $d);

// ---- المجاميع: نفس معادلات الملف الأصلي (كانت تُستخدم بلا تهيئة، فصارت تبدأ من صفر. النتيجة نفسها) ----
$quantity = 0;
$costPrice = 0;
$salePrice = 0;
$profit = 0;
$total = 0;        // إجمالي الربح
$totalPrice = 0;   // إجمالي البيع
foreach ($models as $row) {
    $quantity = $quantity + $row['quantity'];
    $costPrice = $costPrice + $row['costPrice'];
    $salePrice = $salePrice + $row['salePrice'];
    $profit = $profit + ($row['salePrice'] - $row['costPrice']);
    $total += $row['profit'] * $row['quantity'];
    $totalPrice += $row['salePrice'] * $row['quantity'];
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
            <h1 class="report-title">تقرير الأرباح اليومية</h1>
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

    <!-- ملخص الفترة -->
    <section class="report-summary" style="margin:0 0 14px">
        <div class="report-stat">
            <div class="rp-k">عدد الأصناف</div>
            <div class="rp-v"><?= count($models) ?></div>
        </div>
        <div class="report-stat">
            <div class="rp-k">إجمالي البيع</div>
            <div class="rp-v rp-bal"><?= $fmt($totalPrice) ?></div>
        </div>
        <div class="report-stat">
            <div class="rp-k">إجمالي الربح</div>
            <div class="rp-v <?= $total < 0 ? 'rp-dr' : 'rp-cr' ?>"><?= $fmt($total) ?></div>
        </div>
    </section>

    <div style="overflow-x:auto">
        <table class="report-table">
            <thead>
                <tr>
                    <th class="rp-idx">#</th>
                    <th>رقم الصنف</th>
                    <th>اسم الصنف</th>
                    <th>الكمية</th>
                    <th>سعر التكلفة</th>
                    <th>سعر البيع</th>
                    <th class="rp-cr">هامش الربح</th>
                    <th class="rp-cr">إجمالي الربح</th>
                    <th class="rp-bal">إجمالي البيع</th>
                </tr>
            </thead>

            <tbody>
                <?php $i = 1; foreach ($models as $row):
                    $margin = $row['profit'];
                    $lineProfit = $row['profit'] * $row['quantity'];
                    $lineTotal = $row['salePrice'] * $row['quantity'];
                ?>
                    <tr>
                        <td class="rp-idx"><?= $i++ ?></td>
                        <td><?= Html::encode((string) $row['category']) ?></td>
                        <td><?= Html::encode((string) $row['name']) ?></td>
                        <td class="rp-num"><?= $fmt($row['quantity'], 0) ?></td>
                        <td class="rp-num"><?= $fmt($row['costPrice']) ?></td>
                        <td class="rp-num"><?= $fmt($row['salePrice']) ?></td>
                        <td class="rp-num <?= $margin < 0 ? 'rp-dr' : 'rp-cr' ?>"><?= $fmt($margin) ?></td>
                        <td class="rp-num <?= $lineProfit < 0 ? 'rp-dr' : 'rp-cr' ?>"><?= $fmt($lineProfit) ?></td>
                        <td class="rp-num rp-bal"><?= $fmt($lineTotal) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="3">الإجمالي</td>
                    <td class="rp-num"><?= $fmt($quantity) ?></td>
                    <td class="rp-num"><?= $fmt($costPrice) ?></td>
                    <td class="rp-num"><?= $fmt($salePrice) ?></td>
                    <td class="rp-num rp-cr"><?= $fmt($profit) ?></td>
                    <td class="rp-num rp-cr"><?= $fmt($total) ?></td>
                    <td class="rp-num rp-bal"><?= $fmt($totalPrice) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>
