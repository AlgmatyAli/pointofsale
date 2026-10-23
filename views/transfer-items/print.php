<?php

use app\models\CompanyInfo;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

// تخطيط مستقل بلا الشريط الجانبي والعلوي. زر Print يستدعي printInvoice() (طباعة داخل إطار معزول)
$this->context->layout = 'invoice';
$this->registerCssFile('@web/css/template.css');

$company = CompanyInfo::currentArray() ?: [];

$logo = (string) ($company['path'] ?? '');
if ($logo !== '' && !preg_match('~^(https?:)?//|^/~', $logo)) {
    $logo = Yii::getAlias('@web/') . ltrim($logo, '/');
}
?>

<!-- شريط الأزرار: لا يُطبع -->
<div class="no-print d-print-none mb-3 d-flex flex-wrap justify-content-between gap-2">
    <button type="button" class="btn btn-primary" onclick="printInvoice()">
        <i class="bi bi-printer"></i> <?= Yii::t('app', 'Print') ?>
    </button>
    <?= Html::a(
        '<i class="bi bi-arrow-right-circle"></i> ' . Yii::t('app', 'Cancel'),
        Yii::$app->request->referrer ?: ['index'],
        ['class' => 'btn btn-outline-secondary']
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

            <section id="invoice-info">
                <span class="clearfix"></span>
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
                            'headerOptions' => ['style' => 'width:40%'],
                            'value' => fn($data) => $data->category0?->name,
                        ],
                        [
                            'label' => Yii::t('app', 'Serial No'),
                            'value' => fn($data) => $data->category0?->serialNo,
                        ],
                        [
                            'label' => Yii::t('app', 'Company'),
                            'value' => fn($data) => $data->category0?->company,
                        ],
                        [
                            'label' => Yii::t('app', 'Quantity'),
                            'headerOptions' => ['style' => 'width:10%'],
                            'value' => fn($data) => $data->quantity,
                        ],
                        [
                            'label' => Yii::t('app', 'من الفرع'),
                            'value' => fn($data) => $data->transfer0?->fromBranch0?->name,
                        ],
                        [
                            'label' => Yii::t('app', 'الى الفرع'),
                            'value' => fn($data) => $data->transfer0?->toBranch0?->name,
                        ],
                    ],
                ]) ?>
            </section>
        </div>

    </div>
</div>
