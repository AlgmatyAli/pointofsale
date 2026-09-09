<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\Sales $model */
/** @var app\models\CompanyInfo $company */
/** @var yii\data\ActiveDataProvider $providerSalesDetails */
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <style>
        /* CSS عام وتجهيز الصفحة للطباعة */
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: #fff;
                margin: 0;
                padding: 0;
            }

            #container {
                width: 100%;
                margin: 0;
                box-shadow: none;
            }

            /* إزالة الحدود والخطوط العمودية غير المرغوبة أثناء الطباعة */
            table,
            th,
            td {
                border-right: none !important;
            }

            .grid-view table {
                border: 1px solid #dee2e6;
                border-collapse: collapse;
            }
        }

        /* إصلاح تنسيقات عناصر الفاتورة */
        .invoice-info-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .company-logo {
            max-height: 80px;
            width: auto;
        }
    </style>
</head>

<body id="div1">

    <!-- أزرار التحكم - مخفية أثناء الطباعة -->
    <div class="no-print mb-3">
        <button class="btn btn-primary" onclick="window.print()"><?= Yii::t('app', 'Print') ?></button>
        <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-warning pull-left']) ?>
    </div>

    <div id="container">
        <div class="invoice-top">

            <section id="memo" class="text-center">
                <?php if (!empty($company->path)): ?>
                    <div class="pull-left">
                        <img src="<?= Html::encode($company->path) ?>" class="logo company-logo" alt="Company Logo">
                    </div>
                <?php endif; ?>
                <br><br>

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

            <!-- عنوان الفاتورة حسب النوع -->
            <?php
            $invoiceTitles = [
                1 => Yii::t('app', 'Sales Invoice'),
                2 => Yii::t('app', 'Back Sales Invoice'),
                3 => Yii::t('app', 'Reservation Invoice'),
                4 => Yii::t('app', 'Proforma Invoice'),
            ];
            $title = $invoiceTitles[$model->type] ?? Yii::t('app', 'Invoice');
            ?>
            <h2 class="text-center white"><?= $title ?></h2>

            <section id="invoice-info">
                <div>
                    <span><?= Html::encode($model->billId) ?></span>
                    <span><?= Html::encode($model->at) ?></span>
                    <span><?= Yii::$app->formatter->asTime($model->created_at) ?></span>
                    <span><?= !empty($model->deleviryAt) ? Html::encode($model->deleviryAt) : '' ?></span>
                    <span>
                        <?php
                        if (in_array($model->type, [1, 3, 4])) {
                            $payWays = ['0' => 'نقداً', '1' => 'آجل', '2' => 'دفعة على الحساب'];
                            echo $payWays[$model->payWay] ?? '';
                        } elseif ($model->type == 2) {
                            $payWays = ['1' => 'نقداً', '2' => 'آجل'];
                            echo $payWays[$model->payWay] ?? '';
                        }
                        ?>
                    </span>
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
                <div>
                    <span class="bold"><?= Html::encode($model->c->name ?? '') ?></span>
                </div>
                <div>
                    <span><?= Html::encode($model->c->phone ?? '') ?></span>
                </div>
                <div>
                    <span><?= Html::encode($model->c->email ?? '') ?></span>
                </div>
            </section>
        </div>

        <div class="invoice-body">
            <section id="items">
                <?= GridView::widget([
                    'summary' => '',
                    'dataProvider' => $providerSalesDetails,
                    'layout' => "{items}",
                    'options' => ['style' => 'font-size:12px;'],
                    'tableOptions' => ['class' => 'table table-striped table-bordered', 'style' => 'border-right: none;'],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'label' => Yii::t('app', 'Name'),
                            'contentOptions' => ['style' => 'font-size:12px;'],
                            'headerOptions' => ['style' => 'width:50%'],
                            'value' => function ($data) {
                                return $data->cat->name ?? '';
                            }
                        ],
                        [
                            'label' => Yii::t('app', 'العدد'),
                            'contentOptions' => ['style' => 'font-size:12px;'],
                            'headerOptions' => ['style' => 'width:15%'],
                            'value' => function ($data) {
                                return $data->quantity;
                            }
                        ],
                    ],
                ]); ?>
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

            <br><br><br>
            <section id="terms">
                <div class="row">
                    <div class="col-md-12">
                        <span>ملاحظات: <?= Html::encode($model->notes) ?></span>
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
</body>

<?php $this->registerCssFile("@web/css/template.css"); ?>

</html>