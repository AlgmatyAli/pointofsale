<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $company app\models\CompanyInfo */
/* @var $model app\models\TempInvoice */

$identity = Yii::$app->user->identity;
$this->title = Yii::t('app', 'مرحبا بـك ') . $identity->username . '!';

// ---------------------------------------------------------------------------
// إعدادات تعتمد على الشركة والمستخدم
// ---------------------------------------------------------------------------

$days = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
$greeting = ((int) date('G') < 12) ? 'صباح الخير' : 'مساء الخير';
$today = $days[(int) date('w')] . '، ' . date('Y/m/d');

// ---------------------------------------------------------------------------
// الإشعارات: كما هي بلا أي تغيير (نفس البنود والقيم والروابط)
// الألوان: danger=أحمر، success=أخضر، info=أزرق فاتح، warning=أصفر، primary=أزرق، purple=بنفسجي
// ---------------------------------------------------------------------------
$infoBoxes = [
    ['color' => 'danger',  'icon' => 'bi-bell',        'text' => 'اصناف وصلت للحد الأدنى',               'value' => $reorder,                        'url' => ['/reorderitems/index'],                                    'link' => 'لعرض الاصناف اضغط هنا...'],
    ['color' => 'success', 'icon' => 'bi-folder2-open', 'text' => 'فواتير مشتريات لم يتم اعتمادها',      'value' => $tempPurchase,                   'url' => ['/temp-invoice-purchase/create'],                          'link' => 'لعرض الفواتير اضغط هنا...'],
    ['color' => 'info',    'icon' => 'bi-eye',          'text' => 'فواتير مبيعات لم يتم اعتمادها',        'value' => $tempSales,                      'url' => ['/temp-invoice/create', 'type' => 1],                      'link' => 'لعرض الفواتير اضغط هنا...'],
    ['color' => 'warning', 'icon' => 'bi-heart-fill',   'text' => 'الأصناف الأكثر رواجا، قاربت على الانتهاء', 'value' => $request,                    'url' => ['/category/more-request'],                                 'link' => 'لعرض الاصناف اضغط هنا...'],
    ['color' => 'primary', 'icon' => 'bi-heart-fill',   'text' => 'فواتير لم يتم تسليمها',               'value' => $deleviried,                     'url' => ['/sales/index', 'SalesSearch' => ['deleviried' => 0]],     'link' => 'لعرض الفواتير اضغط هنا...'],
    ['color' => 'purple',  'icon' => 'bi-bell',         'text' => 'فواتير مستحقة حان موعد تسديدها',      'value' => $amount,                         'url' => ['/sales/deserving'],                                       'link' => 'لعرض الفواتير اضغط هنا...'],
    ['color' => 'purple',  'icon' => 'bi-bell',         'text' => 'فواتير مشتريات حان موعد وصولها',      'value' => $dateOfArrival['dateOfArrival'], 'url' => ['/purchases/date-of-arrival'],                            'link' => 'لعرض الفواتير اضغط هنا...'],
    ['color' => 'info',    'icon' => 'bi-bell',         'text' => 'اصناف سعر البيع فيها أقل من سعر التكلفة', 'value' => $compare,                      'url' => ['/prices/compare'],                                        'link' => 'لعرض الاصناف اضغط هنا...'],
    ['color' => 'danger',  'icon' => 'bi-bell',         'text' => 'اصناف كمياتها أقل من الصفر',          'value' => $zeroQ,                          'url' => ['/stocks/zero-q'],                                         'link' => 'لعرض الاصناف اضغط هنا...'],
];
// عدد التنبيهات التي فيها قيمة (للعرض فقط في عنوان القسم)
$activeAlerts = 0;
foreach ($infoBoxes as $b) {
    if (is_numeric($b['value']) && $b['value'] > 0) {
        $activeAlerts++;
    }
}

// اختصارات سريعة (عدّل المسارات حسب صلاحياتك وقوائمك)
$quick = [
    ['icon' => 'bi-cart-plus',     'label' => 'فاتورة بيع',      'url' => ['/temp-invoice/create', 'type' => 1], 'color' => 'danger'],
    ['icon' => 'bi-bag-plus',      'label' => 'فاتورة شراء',     'url' => ['/temp-invoice-purchase/create'],     'color' => 'success'],
    ['icon' => 'bi-receipt',       'label' => 'فواتير المبيعات', 'url' => ['/sales/index'],                      'color' => 'primary'],
    ['icon' => 'bi-box-seam',      'label' => 'كشف حساب صنف',     'url' => ['/category/histrans'],                'color' => 'info'],
    ['icon' => 'bi-people ',       'label' => 'كشف حساب عميل',    'url' => ['/client/histrans'],                  'color' => 'warning'],
];

// ---------------------------------------------------------------------------
// تنسيقات
// ---------------------------------------------------------------------------
$this->registerCss(<<<'CSS'
.dash-hero { background: linear-gradient(135deg, var(--bs-primary-bg-subtle), var(--bs-body-bg) 70%); border: 1px solid var(--bs-border-color); border-radius: .85rem; }
.dash-hero .dash-hello { font-size: 1.6rem; font-weight: 700; line-height: 1.2; }
.dash-tile { display: flex; flex-direction: column; align-items: center; gap: .35rem; padding: .85rem .5rem; text-align: center; text-decoration: none; color: var(--bs-body-color); border: 1px solid var(--bs-border-color); border-radius: .75rem; background: var(--bs-body-bg); height: 100%; }
.dash-tile i { font-size: 1.6rem; }
.dash-tile span { font-size: .85rem; font-weight: 600; }
.dash-tile:hover, .dash-tile:focus-visible { border-color: var(--bs-primary); color: var(--bs-body-color); box-shadow: 0 .25rem .75rem rgba(var(--bs-emphasis-color-rgb), .08); }
@media (prefers-reduced-motion: no-preference) { .dash-tile { transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease; } .dash-tile:hover { transform: translateY(-2px); } }
.dash-lookup .select2-container .select2-selection--single { min-height: 44px; }
.dash-result .dash-price { font-size: 2rem; font-weight: 800; line-height: 1; }
.dash-mini { padding: .5rem .75rem; border: 1px solid var(--bs-border-color); border-radius: .6rem; height: 100%; }
.dash-mini .k { font-size: .72rem; color: var(--bs-secondary-color); }
.dash-mini .v { font-weight: 700; }
.dash-section-title { display: flex; align-items: center; gap: .5rem; margin: 1.25rem 0 .75rem; font-weight: 700; }
kbd.dash-key { font-size: .7rem; }
CSS);
?>

<div class="site-index">

    <!-- ترحيب -->
    <div class="dash-hero p-3 p-md-4 mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <div class="text-body-secondary small"><i class="bi bi-calendar3"></i> <?= Html::encode($today) ?></div>
            <div class="dash-hello"><?= Html::encode($greeting) ?>، <?= Html::encode($identity->username) ?></div>
            <div class="text-body-secondary">إليك ملخص ما يحتاج انتباهك اليوم.</div>
        </div>
        <span class="badge text-bg-primary fs-6 fw-semibold align-self-start"><i class="bi bi-calendar-event"></i> السنة <?= date('Y') ?></span>
    </div>

    <!-- اختصارات سريعة -->
    <div class="row g-2 mb-3">
        <?php foreach ($quick as $q): ?>
            <div class="col-6 col-md">
                <?= Html::a(
                    '<i class="bi ' . $q['icon'] . ' text-' . $q['color'] . '"></i><span>' . Html::encode($q['label']) . '</span>',
                    $q['url'],
                    ['class' => 'dash-tile']
                ) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- الإشعارات: بلا تغيير في البنود أو الروابط أو القيم -->
    <div class="dash-section-title">
        <i class="bi bi-bell"></i> التنبيهات
        <?php if ($activeAlerts > 0): ?>
            <span class="badge text-bg-danger"><?= (int) $activeAlerts ?> تحتاج انتباه</span>
        <?php else: ?>
            <span class="badge text-bg-success">لا توجد تنبيهات</span>
        <?php endif; ?>
    </div>

    <div class="row">
        <?php foreach ($infoBoxes as $b): ?>
            <div class="col-md-4">
                <div class="info-box text-bg-<?= \yii\helpers\Html::encode($b['color']) ?>">
                    <span class="info-box-icon"><i class="bi <?= \yii\helpers\Html::encode($b['icon']) ?>"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= Html::encode($b['text']) ?></span>
                        <span class="info-box-number"><?= Html::encode((string) $b['value']) ?></span>
                        <div class="progress">
                            <div class="progress-bar" style="width: 70%"></div>
                        </div>
                        <span class="progress-description">
                            <?= Html::a(Html::encode($b['link']), $b['url'], ['class' => 'text-reset fw-semibold']) ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>