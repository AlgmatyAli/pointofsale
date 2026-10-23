<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Purchases $model */
/** @var app\models\CompanyInfo $company */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var float $balance */

// عرض الفاتورة بتخطيط مستقل بلا الشريط الجانبي والعلوي. زر Print يستدعي printInvoice() من تخطيط invoice (طباعة داخل إطار معزول)
$this->context->layout = 'invoice';

// مسار الشعار
$logo = (string) ($company->path ?? '');
if ($logo !== '' && !preg_match('~^(https?:)?//|^/~', $logo)) {
    $logo = Yii::getAlias('@web/') . ltrim($logo, '/');
}

$client = $model->c; // بيانات المورد / العميل

// طريقة الدفع
$payWayText = ['0' => 'نقدا', '1' => 'آجل', '2' => 'دفعة على الحساب'][(string) $model->payWay] ?? '';
?>

<!-- شريط الأزرار العلوي: لا يُطبع -->
<div class="no-print d-print-none mb-3 d-flex flex-wrap justify-content-between gap-2">
    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-primary" onclick="printInvoice()">
            <i class="bi bi-printer"></i> <?= Yii::t('app', 'Print') ?>
        </button>

        <?= Html::a(
            '<i class="bi bi-pencil-square"></i> ' . Yii::t('app', 'Update'),
            ['update', 'id' => $model->id],
            ['class' => 'btn btn-primary']
        ) ?>

        <?= Html::a(
            '<i class="bi bi-trash"></i> ' . Yii::t('app', 'Delete'),
            ['delete', 'id' => $model->id],
            [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]
        ) ?>
    </div>

    <?= Html::a(
        Yii::t('app', 'Cancel'),
        ['/site/index'], // الخروج من الطباعة يرجع للرئيسية دائماً
        ['class' => 'btn btn-warning']
    ) ?>
</div>

<!-- الفاتورة (نفس الهيكل والتصميم المنسق) -->
<div id="div1">
    <div id="container">

        <div class="invoice-top">

            <section id="memo" class="text-center">
                <div style="float:left">
                    <?php if ($logo !== ''): ?>
                        <img src="<?= Html::encode($logo) ?>" class="logo" alt="<?= Html::encode($company->name ?? '') ?>">
                    <?php endif; ?>
                </div>
                <br><br>

                <div class="company-info">
                    <span class="company-name"><?= Html::encode($company->name ?? '') ?> </span>

                    <span class="spacer"></span>
                    <span class="company-name1"><?= Html::encode($company->work ?? '') ?> </span>

                    <span class="spacer"></span>
                    <div></div>
                    <div><?= Html::encode($company->address ?? '') ?> </div>

                    <span class="clearfix"></span>

                    <div><?= Html::encode($company->phone1 ?? '') ?> |</div>
                    <div><?= Html::encode($company->phone2 ?? '') ?> </div>
                </div>
            </section>

            <h2 class="text-center white"><?= Yii::t('app', 'Purchases Invoice'); ?></h2>

            <section id="invoice-info">
                <div>
                    <span><?= Html::encode((string) $model->billId) ?></span>
                    <span><?= Html::encode((string) $model->clientBill) ?></span>
                    <span><?= Html::encode((string) $model->at) ?></span>
                    <span><?= Html::encode($payWayText) ?></span>
                    <span></span>
                </div>

                <div>
                    <span>رقـم الفاتورة:</span>
                    <span>رقـم فاتورة المورد:</span>
                    <span>تاريخ الفاتورة:</span>
                    <span>طريقة الدفع:</span>
                    <span></span>
                </div>
            </section>

            <section id="client-info">
                <span>تفاصيل العميل / المورد</span>
                <div><span class="bold"><?= Html::encode((string) $client?->name) ?></span></div>
                <div><span><?= Html::encode((string) $client?->phone) ?></span></div>
                <div><span><?= Html::encode((string) $client?->email) ?></span></div>
            </section>
        </div>

        <div class="invoice-body">
            <section id="items">
                <?= GridView::widget([
                    'summary' => '',
                    'dataProvider' => $dataProvider,
                    'layout' => '{items}',
                    'options' => ['style' => 'font-size:12px;'],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'label' => Yii::t('app', 'Name'),
                            'contentOptions' => ['style' => 'font-size:12px;'],
                            'headerOptions' => ['style' => 'width:40%'],
                            'value' => fn($data) => $data->category0?->name,
                        ],
                        [
                            'label' => Yii::t('app', 'Serial No'),
                            'contentOptions' => ['style' => 'font-size:12px;'],
                            'headerOptions' => ['style' => 'width:20%'],
                            'value' => fn($data) => $data->category0?->serialNo,
                        ],
                        [
                            'label' => Yii::t('app', 'quantity'),
                            'contentOptions' => ['style' => 'font-size:12px;'],
                            'headerOptions' => ['style' => 'width:10%'],
                            'value' => fn($data) => $data->quantity,
                        ],
                        [
                            'label' => Yii::t('app', 'Sale Price'),
                            'contentOptions' => ['style' => 'font-size:12px;'],
                            'headerOptions' => ['style' => 'width:15%'],
                            'format' => ['decimal', 3],
                            'value' => fn($data) => $data->salePrice,
                        ],
                    ],
                ]) ?>
            </section>
        </div>

    </div>
</div>