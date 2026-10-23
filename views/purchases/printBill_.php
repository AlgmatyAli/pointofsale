<?php

use yii\helpers\Html;
use app\models\CompanyInfo;

/* @var $this yii\web\View */
/* @var $models array */
/* @var $infos array */

// تخطيط مستقل بلا الشريط الجانبي والعلوي، وزر Print يستدعي printInvoice() (طباعة داخل إطار معزول)
$this->context->layout = 'invoice';
$this->registerCssFile('@web/css/template.css');

// نفس منطق الملف الأصلي: آخر عنصر في $models
$model = [];
foreach ($models as $value) {
    $model = $value;
}

$company = CompanyInfo::find()->asArray()->one() ?: [];

$logo = (string) ($company['path'] ?? '');
if ($logo !== '' && !preg_match('~^(https?:)?//|^/~', $logo)) {
    $logo = Yii::getAlias('@web/') . ltrim($logo, '/');
}

$invoiceTitle = '';
if (($model['type'] ?? null) == '1') {
    $invoiceTitle = 'فاتورة مشتريات';
} elseif (($model['type'] ?? null) == '2') {
    $invoiceTitle = 'فاتورة مسترجع مشتريات';
}

$payWayText = ['0' => 'نقدا', '1' => 'آجل', '2' => 'دفعة على الحساب'][(string) ($model['payWay'] ?? '')] ?? '';
?>

<!-- شريط الأزرار: لا يُطبع -->
<div class="no-print d-print-none mb-3 d-flex flex-wrap justify-content-between gap-2">
    <button type="button" class="btn btn-primary" onclick="printInvoice()">
        <i class="bi bi-printer"></i> <?= Yii::t('app', 'Print') ?>
    </button>
    <?= Html::a(
        Yii::t('app', 'Cancel'),
        ['/site/index'], // الخروج من الطباعة يرجع للرئيسية دائماً
        ['class' => 'btn btn-warning']
    ) ?>
</div>

<div id="div1">
    <div id="container">

        <div class="invoice-top">

            <section id="memo" class="text-center">
                <div style="float:left">
                    <?php if ($logo !== ''): ?>
                        <img src="<?= Html::encode($logo) ?>" class="logo" alt="<?= Html::encode($company['name'] ?? '') ?>">
                    <?php endif; ?>
                </div>
                <br><br>
                <div class="company-info">
                    <span class="company-name"><?= Html::encode($company['name'] ?? '') ?> </span>
                    <span class="spacer"></span>
                    <span class="company-name1"><?= Html::encode($company['work'] ?? '') ?> </span>
                    <span class="spacer"></span>
                    <div></div>
                    <div><?= Html::encode($company['address'] ?? '') ?> </div>
                    <span class="clearfix"></span>
                    <div><?= Html::encode($company['phone1'] ?? '') ?> |</div>
                    <div><?= Html::encode($company['phone2'] ?? '') ?> </div>
                </div>
            </section>

            <h2 class="text-center white"><?= Html::encode($invoiceTitle) ?></h2>

            <section id="invoice-info">
                <div>
                    <span><?= Html::encode((string) ($model['billId'] ?? '')) ?></span>
                    <span><?= Html::encode((string) ($model['at'] ?? '')) ?></span>
                    <span><?= Html::encode($payWayText) ?></span>
                    <span></span>
                </div>
                <div>
                    <span>رقـم الفاتورة:</span>
                    <span>تاريخ الفاتورة:</span>
                    <span>طريقة الدفع:</span>
                    <span></span>
                </div>
            </section>

            <section id="client-info">
                <span>اسم المورد</span>
                <div><span class="bold"><?= Html::encode((string) ($model['client'] ?? '')) ?></span></div>
            </section>
        </div>

        <div class="invoice-body">

            <section id="items">
                <table class="table table-bordered" style="font-size:12px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th style="width:35%">البيـــــان</th>
                            <th>رقم القطعة</th>
                            <th>الكمية</th>
                            <th>الشركة المصنعة</th>
                            <th>الكمية المتبقية</th>
                            <th>الكمية المحجوزة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $coun = 1; foreach ($infos as $data): ?>
                            <tr>
                                <td><?= $coun++ ?></td>
                                <td><?= Html::encode((string) $data['category']) ?></td>
                                <td><?= Html::encode((string) $data['serialNo']) ?></td>
                                <td><?= Html::encode((string) $data['quantity']) ?></td>
                                <td><?= Html::encode((string) $data['company']) ?></td>
                                <td><?= Html::encode((string) $data['Qtotalinventory']) ?></td>
                                <td><?= Html::encode((string) $data['reservation']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <div class="clearfix"></div>

            <section id="terms">
                <div class="row">
                    <div class="col-md-12">
                        <span>ملاحظـــات:</span>
                        <?= Html::encode((string) ($model['notes'] ?? '')) ?>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>
