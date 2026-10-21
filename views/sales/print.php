<?php

//use kartik\grid\GridView;

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Sales $model */
/** @var app\models\CompanyInfo $company */
/** @var yii\data\ActiveDataProvider $providerSalesDetails */
/** @var float $totalInvoice */
/** @var float $balance */

// عرض الفاتورة بتخطيط مستقل بلا الشريط الجانبي والعلوي، فتُطبع بعرض الورقة تماماً
$this->context->layout = 'invoice';

// مسار الشعار: إن كان نسبياً نجعله نسبةً إلى مجلد web.
// إن لم تظهر الصورة فافتح F12 > Network وانظر الرابط الذي فشل تحميله لتعرف المسار الصحيح.
$logo = (string) $company->path;
if ($logo !== '' && !preg_match('~^(https?:)?//|^/~', $logo)) {
    $logo = Yii::getAlias('@web/') . ltrim($logo, '/');
}

$client = $model->c; // قد يكون null

// عنوان الفاتورة حسب النوع
$titles = [
    1 => 'Sales Invoice',
    2 => 'Back Sales Invoice',
    3 => 'Reservation Invoice',
    4 => 'Proforma Invoice',
];
$invoiceTitle = $titles[(int) $model->type] ?? null;

// طريقة الدفع (في المسترجع معنى 1 و2 يختلف عن باقي الأنواع)
$payWayText = '';
if (in_array($model->type, [1, 3, 4])) {
    $payWayText = ['0' => 'نقدا', '1' => 'آجل', '2' => 'دفعة على الحساب'][(string) $model->payWay] ?? '';
} elseif ($model->type == 2) {
    $payWayText = ['1' => 'نقدا', '2' => 'آجل'][(string) $model->payWay] ?? '';
}
?>

<!-- شريط الأزرار: لا يُطبع -->
<?= $this->render('_flash') ?>
<div class="no-print d-print-none mb-3 d-flex flex-wrap justify-content-between gap-2">
    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-primary" onclick="printInvoice()">
            <i class="bi bi-printer"></i> <?= Yii::t('app', 'Print') ?>
        </button>

        <?= Html::a(
            '<i class="bi bi-printer"></i> ' . Yii::t('app', 'Print with place'),
            ['print-with-place', 'id' => $model->id],
            ['class' => 'btn btn-danger']
        ) ?>

        <?= Html::a(
            '<i class="bi bi-printer"></i> ' . Yii::t('app', 'Print without price'),
            ['noprice', 'id' => $model->id],
            ['class' => 'btn btn-secondary']
        ) ?>

        <?= Html::a(
            '<i class="bi bi-printer"></i> ' . Yii::t('app', 'Print For Driver'),
            ['print-for-driver', 'id' => $model->id],
            ['class' => 'btn btn-success']
        ) ?>

        <?= Html::a(
            '<i class="bi bi-pencil-square"></i> ' . Yii::t('app', 'Update'),
            ['update', 'id' => $model->id],
            ['class' => 'btn btn-primary']
        ) ?>

        <?= Html::a(
            '<i class="bi bi-envelope"></i> ' . Yii::t('app', 'Send By Email'),
            ['pdf', 'id' => $model->id],
            ['class' => 'btn btn-warning', 'data' => ['method' => 'post']]
        ) ?>

        <?= Html::a(
            Yii::t('app', 'Create Client Histrans'),
            ['histrans', 'client' => $model->clinet, 'allData' => 1, 'type' => $client?->type],
            ['class' => 'btn btn-info']
        ) ?>

        <?php
        // Html::a(
        //     '<i class="bi bi-whatsapp"></i> إرسال واتساب',
        //     ['whatsapp', 'id' => $model->id],
        //     ['class' => 'btn btn-success', 'target' => '_blank', 'rel' => 'noopener']
        // ) 
        ?>

        <?= Html::a(
            '<i class="bi bi-file-earmark-pdf"></i> ' . Yii::t('app', 'Save AS PDF'),
            ['save-pdf', 'id' => $model->id],
            ['class' => 'btn btn-success']
        ) ?>

        <?php if ($model->deleviried == 0): ?>
            <?= Html::a(
                Yii::t('app', 'تغيير حالة الفاتورة'),
                ['delev', 'id' => $model->id],
                ['class' => 'btn btn-warning', 'data' => ['method' => 'post']]
            ) ?>
        <?php endif; ?>

        <?= Html::a(
            Yii::t('app', 'ايقاف الدين على الزبون'),
            ['stop-credit', 'id' => $model->clinet],
            [
                'class' => 'btn btn-primary',
                'data' => [
                    'confirm' => 'هل تريد إيقاف التعامل بالدين مع هذا العميل؟',
                    'method' => 'post',
                ],
            ]
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
        Yii::$app->request->referrer ?: ['index'],
        ['class' => 'btn btn-warning']
    ) ?>
</div>

<!-- الفاتورة -->
<div id="div1">
    <div id="container">

        <div class="invoice-top">

            <section id="memo" class="text-center">
                <div style="float:left">
                    <?php if ($logo !== ''): ?>
                        <img src="<?= Html::encode($logo) ?>" class="logo" alt="<?= Html::encode($company->name) ?>">
                    <?php endif; ?>
                </div>
                <br><br>

                <div class="company-info">
                    <span class="company-name"><?= Html::encode($company->name) ?> </span>

                    <span class="spacer"></span>
                    <span class="company-name1"><?= Html::encode($company->work) ?> </span>

                    <span class="spacer"></span>
                    <div></div>
                    <div><?= Html::encode($company->address) ?> </div>

                    <span class="clearfix"></span>

                    <div><?= Html::encode($company->phone1) ?> |</div>
                    <div><?= Html::encode($company->phone2) ?> </div>
                </div>
            </section>

            <?php if ($invoiceTitle !== null): ?>
                <h2 class="text-center white"><?= Yii::t('app', $invoiceTitle) ?></h2>
            <?php endif; ?>

            <section id="invoice-info">
                <div>
                    <span><?= Html::encode((string) $model->billId) ?></span>
                    <span><?= Html::encode((string) $model->at) ?></span>
                    <span><?= Yii::$app->formatter->asTime($model->created_at) ?></span>
                    <span><?= Html::encode((string) $model->deleviryAt) ?></span>
                    <span><?= Html::encode($payWayText) ?></span>
                    <span></span>
                </div>

                <div>
                    <span>رقـم الفاتورة:</span>
                    <span>تاريخ الفاتورة:</span>
                    <span>توقيت الفاتورة:</span>
                    <span>تاريخ التسليم:</span>
                    <span>طريقة الدفع:</span>
                    <span></span>
                </div>
            </section>

            <section id="client-info">
                <span>تفاصيل الزبون</span>
                <div><span class="bold"><?= Html::encode((string) $client?->name) ?></span></div>
                <div><span><?= Html::encode((string) $client?->phone) ?></span></div>
                <div><span><?= Html::encode((string) $client?->email) ?></span></div>
            </section>
        </div>

        <div class="invoice-body">

            <section id="items">
                <?= GridView::widget([
                    'summary' => '',
                    'dataProvider' => $providerSalesDetails,
                    'layout' => '{items}',
                    'options' => ['style' => 'font-size:12px;'],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'label' => Yii::t('app', 'Name'),
                            'contentOptions' => ['style' => 'font-size:12px;'],
                            'headerOptions' => ['style' => 'width:50%'],
                            // 'width' => '40%',
                            'value' => fn($data) => $data->cat?->name,
                        ],
                        [
                            'label' => Yii::t('app', 'Company'),
                            'contentOptions' => ['style' => 'font-size:12px;'],
                            'headerOptions' => ['style' => 'width:15%'],
                            // 'width' => '15%',
                            'value' => fn($data) => $data->cat?->company,
                        ],
                        [
                            'label' => Yii::t('app', 'quantity'),
                            'contentOptions' => ['style' => 'font-size:12px;'],
                            'headerOptions' => ['style' => 'width:10%'],
                            // 'width' => '15%',
                            'value' => fn($data) => $data->quantity,
                        ],
                        [
                            'label' => Yii::t('app', 'Price'),
                            'contentOptions' => ['style' => 'font-size:12px;'],
                            'headerOptions' => ['style' => 'width:10%'],
                            //'width' => '15%',
                            'format' => ['decimal', 3],
                            'value' => fn($data) => $data->salePrice,
                        ],
                        [
                            'label' => Yii::t('app', 'Total'),
                            'contentOptions' => ['style' => 'font-size:12px;'],
                            'headerOptions' => ['style' => 'width:10%'],
                            // 'width' => '15%',
                            'format' => ['decimal', 3],
                            'value' => fn($data) => $data->salePrice * $data->quantity,
                        ],
                    ],
                ]) ?>
            </section>

            <section id="sums">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <th>الاجمـــالي</th>
                        <td><?= number_format((float) $totalInvoice, 3) ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>المدفـوع</th>
                        <td><?= number_format((float) $model->paid, 3) ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th><?= Yii::t('app', 'Disscount') ?></th>
                        <td><?= number_format((float) $model->disscount, 3) ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>الصـــافي</th>
                        <td><?= number_format((float) $totalInvoice - (float) $model->paid - (float) $model->disscount, 3) ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>الرصيد</th>
                        <td><?= number_format((float) $balance, 3) ?></td>
                        <td></td>
                    </tr>
                </table>
            </section>

            <div class="clearfix"></div>

            <section id="terms">
                <div class="row">
                    <div class="col-md-12">
                        <span class="noprint">ملاحظـات: <?= Html::encode((string) $model->notes) ?></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <span>الشروط والأحكام:</span>
                        <?php // الشروط تُخزَّن كنص منسّق من إعدادات الشركة (يحررها المدير فقط) فتُطبع كما هي 
                        ?>
                        <?= $company->terms ?>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>