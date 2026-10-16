<?php

use app\models\CompanyInfo;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $models */
/** @var array $lastBalance */
/** @var string $min_date */
/** @var string $max_date */

// تقرير بتخطيط مستقل بلا الشريط الجانبي والعلوي، ويُطبع داخل إطار معزول (printReport في layouts/report.php)
$this->context->layout = 'report';
$this->title = $this->title ?: 'كشف حساب صنف';

// بيانات الشركة والشعار (المسار النسبي يُنسب إلى مجلد web)
$company = CompanyInfo::find()->select(['*'])->asArray()->one() ?: [];
$logo = (string) ($company['path'] ?? '');
if ($logo !== '' && !preg_match('~^(https?:)?//|^/~', $logo)) {
    $logo = Yii::getAlias('@web/') . ltrim($logo, '/');
}

// اسم الصنف ورقمه: من آخر صف كما في الكود الأصلي
$name = '';
$id = '';
foreach ($models as $row) {
    $name = $row['name'];
    $id = $row['id'];
}

// الرصيد السابق: من آخر صف في $lastBalance كما في الكود الأصلي
$openingBalance = 0;
foreach ($lastBalance as $row) {
    $openingBalance = (float) $row['quantity'];
}
$sumQuantity = $openingBalance;   // الرصيد المتحرك أثناء المرور على الحركات

// مجاميع الحركات (كانت تُستخدم بلا تهيئة، فصارت تبدأ من صفر. النتيجة نفسها)
$sumwared = 0;
$sumsader = 0;
$sumPurchase = 0;
$sumBackPurchase = 0;
$sumSales = 0;
$sumBackSales = 0;

$fmt = static fn ($value): string => number_format((float) $value, 3);

// رابط عرض المستند حسب نوع الحركة. النصوص مطابقة حرفياً لما يرجعه الاستعلام (بما فيها المسافات الأخيرة)
$viewLinks = [
    'فاتورة مشتريات رقم' => ['purchases/view', 'btn-warning'],
    'فاتورة مسترجع مشتريات رقم ' => ['purchases/view', 'btn-warning'],
    'فاتورة مبيعات رقم ' => ['sales/print', 'btn-info'],
    'فاتورة مسترجع مبيعات رقم' => ['sales/print', 'btn-info'],
    'نقل إلى الفرع' => ['transfer-items/view', 'btn-primary'],
    'نقل من الفرع' => ['transfer-items/view', 'btn-primary'],
    'تسوية جرد' => ['arrangement/view', 'btn-danger'],
];
?>

<!-- شريط الأزرار: لا يُطبع -->
<div class="no-print d-print-none mb-3 d-flex flex-wrap justify-content-between gap-2">
    <button type="button" class="btn btn-primary" onclick="printReport()">
        <i class="bi bi-printer"></i> <?= Yii::t('app', 'Print') ?>
    </button>

    <?= Html::a(
        Yii::t('app', 'Cancel'),
        Yii::$app->request->referrer ?: ['index'],
        ['class' => 'btn btn-danger']
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
            <h1 class="report-title">كشف حساب صنف</h1>
            <div class="report-period">
                من الفترة: <b><?= Html::encode((string) $min_date) ?></b>
                حتى تاريخ: <b><?= Html::encode((string) $max_date) ?></b>
            </div>
        </div>

        <div class="report-meta">
            تاريخ الطباعة: <?= date('Y-m-d H:i') ?><br>
            أعدّه: <?= Html::encode((string) Yii::$app->user->identity?->username) ?>
        </div>
    </header>

    <section class="report-item">
        <span class="rp-label">اسـم الصنف:</span>
        <b><?= Html::encode((string) $id) ?></b> - <b><?= Html::encode((string) $name) ?></b>
    </section>

    <table class="report-table">
        <thead>
            <tr>
                <th class="rp-idx">#</th>
                <th>تاريخ الحركة</th>
                <th>الفــرع</th>
                <th>البيـــان</th>
                <th class="rp-cr">دائــن</th>
                <th class="rp-dr">مديــن</th>
                <th class="rp-bal">الرصيــد</th>
                <th class="rp-actions no-print"></th>
            </tr>
        </thead>

        <tbody>
            <!-- الرصيد السابق -->
            <tr class="rp-open">
                <td colspan="6">رصيد سابق</td>
                <td class="rp-num rp-bal"><?= $fmt($openingBalance) ?></td>
                <td class="rp-actions no-print"></td>
            </tr>

            <?php $i = 1; foreach ($models as $row):
                $q = $row['quantity'];

                if ($q > 0) {
                    $sumwared = $sumwared + $q;
                }
                if ($q < 0) {
                    $sumsader = $sumsader + ($q * -1);
                }
                if ($row['kind_id'] == 2) {
                    $sumPurchase = $sumPurchase + $q;
                }
                if ($row['kind_id'] == 3) {
                    $sumBackPurchase = $sumBackPurchase + $q;
                }
                if ($row['kind_id'] == 4) {
                    $sumSales = $sumSales + $q;
                }
                if ($row['kind_id'] == 5) {
                    $sumBackSales = $sumBackSales + $q;
                }

                $sumQuantity = $sumQuantity + $q;                 // الرصيد بعد هذه الحركة
                $credit = $q >= 0 ? $q : 0;                       // دائن
                $debit = $q <= 0 ? $q * -1 : 0;                   // مدين
                $link = $viewLinks[$row['kind']] ?? null;
            ?>
                <tr>
                    <td class="rp-idx"><?= $i++ ?></td>
                    <td><?= Html::encode((string) $row['trandate']) ?></td>
                    <td><?= Html::encode((string) $row['branch']) ?></td>
                    <td>
                        <?= Html::encode($row['kind'] . ' - ' . $row['billId'] . ' - ' . $row['client']) ?>
                        <?php if ($row['deleviried'] == 0): ?>
                            <span class="rp-undelivered">غير مسلّمة</span>
                        <?php endif; ?>
                    </td>
                    <td class="rp-num rp-cr<?= $credit == 0 ? ' rp-zero' : '' ?>"><?= $fmt($credit) ?></td>
                    <td class="rp-num rp-dr<?= $debit == 0 ? ' rp-zero' : '' ?>"><?= $fmt($debit) ?></td>
                    <td class="rp-num rp-bal"><?= $fmt($sumQuantity) ?></td>
                    <td class="rp-actions no-print">
                        <?php if ($link !== null): ?>
                            <?= Html::a(
                                '<i class="bi bi-folder2-open"></i>',
                                [$link[0], 'id' => $row['printId']],
                                ['title' => Yii::t('yii', 'View'), 'class' => 'btn btn-sm ' . $link[1]]
                            ) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="4">الإجمالي</td>
                <td class="rp-num rp-cr"><?= $fmt($sumwared) ?></td>
                <td class="rp-num rp-dr"><?= $fmt($sumsader) ?></td>
                <td class="rp-num rp-bal">الرصيـد : <?= $fmt($sumQuantity) ?></td>
                <td class="rp-actions no-print"></td>
            </tr>
        </tfoot>
    </table>

    <!-- ملخص الحركات -->
    <section class="report-summary">
        <div class="report-stat">
            <div class="rp-k">اجمالي المبيعات</div>
            <div class="rp-v"><?= $fmt($sumSales * -1) ?></div>
        </div>
        <div class="report-stat">
            <div class="rp-k">اجمالي مسترجع المبيعات</div>
            <div class="rp-v"><?= $fmt($sumBackSales) ?></div>
        </div>
        <div class="report-stat">
            <div class="rp-k">اجمالي المشتريات</div>
            <div class="rp-v"><?= $fmt($sumPurchase) ?></div>
        </div>
        <div class="report-stat">
            <div class="rp-k">اجمالي مسترجع المشتريات</div>
            <div class="rp-v"><?= $fmt($sumBackPurchase) ?></div>
        </div>
        <div class="report-stat">
            <div class="rp-k">اجمالي تسوية رصيد الجرد اضافة</div>
            <div class="rp-v"><?= $fmt(0) ?></div>
        </div>
        <div class="report-stat">
            <div class="rp-k">اجمالي تسوية رصيد الجرد انقاص</div>
            <div class="rp-v"><?= $fmt(0) ?></div>
        </div>
    </section>

</div>
