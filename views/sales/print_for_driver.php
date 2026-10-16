<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\Sales $model */
/** @var app\models\CompanyInfo $company */
/** @var yii\data\ActiveDataProvider $providerSalesDetails */

$this->context->layout = 'invoice';

$logo = (string) $company->path;
if ($logo !== '' && !preg_match('~^(https?:)?//|^/~', $logo)) {
    $logo = Yii::getAlias('@web/') . ltrim($logo, '/');
}

$client = $model->c;

$titles = [
    1 => 'Sales Invoice',
    2 => 'Back Sales Invoice',
    3 => 'Reservation Invoice',
    4 => 'Proforma Invoice',
];
$title = $titles[(int) $model->type] ?? 'Invoice';

$payWayText = '';
if (in_array($model->type, [1, 3, 4])) {
    $payWayText = ['0' => 'نقداً', '1' => 'آجل', '2' => 'دفعة على الحساب'][(string) $model->payWay] ?? '';
} elseif ($model->type == 2) {
    $payWayText = ['1' => 'نقداً', '2' => 'آجل'][(string) $model->payWay] ?? '';
}
?>

<!-- شريط الأزرار: لا يُطبع -->
<?= $this->render('_flash') ?>
<div class="no-print d-print-none mb-3 d-flex justify-content-between align-items-center">
    <div>
        <button type="button" class="btn btn-primary" onclick="printInvoice()">
            <i class="bi bi-printer"></i> <?= Yii::t('app', 'Print') ?>
        </button>
    </div>
    <?= Html::a(
        Yii::t('app', 'Cancel'),
        Yii::$app->request->referrer ?: ['index'],
        ['class' => 'btn btn-warning']
    ) ?>
</div>

<div id="div1">
    <div id="container">
        <div class="invoice-top">

            <section id="memo" class="text-center">
                <div style="float:left">
                    <?php if ($logo !== ''): ?>
                        <img src="<?= Html::encode($logo) ?>" class="logo" alt="<?= Html::encode($company->name) ?>">
                    <?php endif; ?>
                </div>
                <br>
                <div class="company-info">
                    <span class="company-name"><?= Html::encode($company->name) ?></span>
                    <span class="spacer"></span>
                    <span class="company-name1"><?= Html::encode($company->work) ?></span>
                    <span class="spacer"></span>
                    <div><?= Html::encode($company->address) ?></div>
                    <span class="clearfix"></span>
                    <div><?= Html::encode($company->phone1) ?> | <?= Html::encode($company->phone2) ?></div>
                </div>
            </section>

            <h2 class="text-center white"><?= Yii::t('app', $title) ?></h2>

            <section id="invoice-info">
                <div>
                    <span><?= Html::encode((string) $model->billId) ?></span>
                    <span><?= Html::encode((string) $model->at) ?></span>
                    <span><?= Yii::$app->formatter->asTime($model->created_at) ?></span>
                    <span><?= Html::encode((string) $model->deleviryAt) ?></span>
                    <span><?= Html::encode($payWayText) ?></span>
                </div>

                <div>
                    <span>رقم الفاتورة:</span>
                    <span>تاريخ الفاتورة:</span>
                    <span>توقيت الفاتورة:</span>
                    <span>تاريخ التسليم:</span>
                    <span>طريقة الدفع:</span>
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
                            'value' => fn ($data) => $data->cat?->name,
                        ],
                        [
                            'label' => Yii::t('app', 'العدد'),
                            'contentOptions' => ['style' => 'font-size:12px;'],
                            'headerOptions' => ['style' => 'width:15%'],
                            'value' => fn ($data) => $data->quantity,
                        ],
                    ],
                ]) ?>
            </section>

            <section id="sums">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <th>اجمالي الأعداد</th>
                        <td>
                            <?php
                            $totalQuantity = array_sum(array_column($providerSalesDetails->getModels(), 'quantity'));
                            echo number_format((float)$totalQuantity, 3);
                            ?>
                        </td>
                        <td></td>
                    </tr>
                </table>
            </section>

            <div class="clearfix"></div>

            <section id="terms">
                <div class="row">
                    <div class="col-md-12">
                        <span>ملاحظات: <?= Html::encode((string) $model->notes) ?></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <span>الشروط والأحكام:</span>
                        <?= $company->terms ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>