<?php

use app\models\CompanyInfo;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $models */
/** @var int $indebtedness */

// تقرير بتخطيط مستقل بلا الشريط الجانبي والعلوي، ويُطبع داخل إطار معزول (printReport في layouts/report.php)
$this->context->layout = 'report';
$this->title = $this->title ?: 'تقرير المديونية';

// بيانات الشركة والشعار (المسار النسبي يُنسب إلى مجلد web)
$company = CompanyInfo::currentArray() ?: [];
$logo = (string) ($company['path'] ?? '');
if ($logo !== '' && !preg_match('~^(https?:)?//|^/~', $logo)) {
    $logo = Yii::getAlias('@web/') . ltrim($logo, '/');
}

$fmt = static fn ($value): string => number_format((float) $value, 3);

// نفس الحساب القديم: السالب يُجمع في $credt والباقي في $dept
// (كان $dept غير مُعرَّف قبل الحلقة فيُصدر تحذير PHP، وتمت تهيئته بصفر دون تغيير النتيجة)
$credt = 0;
$dept = 0;
foreach ($models as $row) {
    if ($row['credt'] < 0) {
        $credt = $credt + $row['credt'];
    } else {
        $dept = $dept + $row['credt'];
    }
}

if ($indebtedness == 0) {
    $reportTitle = 'تقرير بإجمالي الدائنون';
    $total = $dept;
} elseif ($indebtedness == 1) {
    $reportTitle = 'تقرير بإجمالي المدينون';
    $total = $credt * -1;
} else {
    $reportTitle = 'تقرير المديونية';
    $total = null;
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
            <h1 class="report-title"><?= Html::encode($reportTitle) ?></h1>
            <div class="report-period">حتى تاريخ: <b><?= date('Y/m/d') ?></b></div>
        </div>

        <div class="report-meta">
            تاريخ الطباعة: <?= date('Y-m-d H:i') ?><br>
            أعدّه: <?= Html::encode((string) Yii::$app->user->identity?->username) ?>
        </div>
    </header>

    <table class="report-table">
        <thead>
            <tr>
                <th class="rp-idx">#</th>
                <th>رقم الحساب</th>
                <th>اسم العميل</th>
                <th class="rp-bal">الرصيـد</th>
                <th>رقم الهاتف</th>
                <th class="rp-actions no-print"></th>
            </tr>
        </thead>

        <tbody>
            <?php $i = 1; foreach ($models as $row): ?>
                <tr>
                    <td class="rp-idx"><?= $i++ ?></td>
                    <td><?= Html::encode((string) $row['id']) ?></td>
                    <?php if ($row['post_paid'] == 0): ?>
                        <?php /* أنماط مضمّنة لأن الطباعة داخل إطار يحمّل report.css فقط */ ?>
                        <td style="color:#b02a37;text-decoration:underline"><?= Html::encode((string) $row['name']) ?></td>
                    <?php else: ?>
                        <td><?= Html::encode((string) $row['name']) ?></td>
                    <?php endif; ?>
                    <td class="rp-num rp-bal">
                        <?php
                        if ($indebtedness == 0) {
                            echo $fmt($row['credt']);
                        } elseif ($indebtedness == 1) {
                            echo $fmt($row['credt'] * -1);
                        }
                        ?>
                    </td>
                    <td class="rp-num"><?= Html::encode((string) $row['phone']) ?></td>
                    <td class="rp-actions no-print">
                        <?= Html::a(
                            '<i class="bi bi-folder2-open"></i>',
                            ['sales/histrans', 'client' => $row['id'], 'allData' => 1, 'type' => $row['type']],
                            ['title' => 'كشف الحساب', 'class' => 'btn btn-sm btn-success']
                        ) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$models): ?>
                <tr><td colspan="6" style="text-align:center;padding:16px">لا توجد بيانات</td></tr>
            <?php endif; ?>
        </tbody>

        <?php if ($total !== null): ?>
            <tfoot>
                <tr>
                    <td colspan="3">الاجمالي :</td>
                    <td class="rp-num rp-bal"><?= $fmt($total) ?></td>
                    <td></td>
                    <td class="rp-actions no-print"></td>
                </tr>
            </tfoot>
        <?php endif; ?>
    </table>

</div>
