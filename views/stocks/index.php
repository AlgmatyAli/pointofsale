<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var  yii\web\View $this */
/** @var  app\models\StocksSearch $searchModel*/
/** @var  yii\data\ActiveDataProvider $dataProvider*/

$this->title = Yii::t('app', 'Inventories');
$this->params['breadcrumbs'][] = $this->title;

// التحقق من صلاحية رؤية التكلفة وأسعار البيع
$seeCostPrice = Yii::$app->user->identity->seeCostPrice ?? 0;
?>

<!-- تنسيقات سريعة لألوان وتصميم البطاقات -->
<style>
    .info-box-colored {
        border-radius: 10px;
        color: #ffffff !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: none;
        overflow: hidden;
    }

    .info-box-colored:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .info-box-colored .info-box-icon {
        background: rgba(255, 255, 255, 0.2) !important;
        color: #ffffff !important;
        border-radius: 10px 0 0 10px;
    }

    .info-box-colored .info-box-text {
        font-weight: 600;
        font-size: 15px;
        color: rgba(255, 255, 255, 0.9);
    }

    .info-box-colored .info-box-number {
        font-size: 22px;
        font-weight: bold;
        color: #ffffff;
    }

    /* الألوان التدرجية */
    .bg-gradient-blue {
        background: linear-gradient(135deg, #2193b0, #6dd5ed) !important;
    }

    .bg-gradient-red {
        background: linear-gradient(135deg, #ee0979, #ff6a00) !important;
    }

    .bg-gradient-green {
        background: linear-gradient(135deg, #11998e, #38ef7d) !important;
    }
</style>

<div class="stocks-index">

    <center>
        <h1><?= Html::encode($this->title) . ' ' . date('Y-m-d') ?></h1>
        <hr>
    </center>

    <?php Pjax::begin(); ?>
    <?php
    $quantity = 0;
    $costPrice = 0;
    $salePrice = 0;
    $i = 0;
    $data = $dataProvider->getModels();

    foreach ($data as $value) {
        $i++;
        $quantity += $value['quantity'];
        $costPrice += $value['costPrice'] * $value['quantity'];
        $salePrice += $value['maxPrice'] * $value['quantity'];
    }

    // تحديد عرض الأعمدة للبطاقات بحسب الصلاحيات
    $colClass = ($seeCostPrice == 1) ? 'col-md-4 col-sm-4 col-xs-12' : 'col-md-12 col-sm-12 col-xs-12';
    ?>

    <!-- صف يحتوي على بطاقات المعلومات الملونة -->
    <div class="row mb-3">
        <!-- بطاقة إجمالي الكمية -->
        <div class="<?= $colClass ?>">
            <div class="info-box info-box-colored bg-gradient-blue">
                <span class="info-box-icon"><i class="fa fa-fw fa-cubes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?= Yii::t('app', 'اجمالي الكمية') ?></span>
                    <span class="info-box-number"><?= number_format($quantity, 2) ?></span>
                </div>
            </div>
        </div>

        <?php if ($seeCostPrice == 1): ?>
            <!-- بطاقة إجمالي التكلفة -->
            <div class="<?= $colClass ?>">
                <div class="info-box info-box-colored bg-gradient-red">
                    <span class="info-box-icon"><i class="fa fa-fw fa-calculator"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= Yii::t('app', 'اجمالي التكلفة') ?></span>
                        <span class="info-box-number"><?= number_format($costPrice, 3) ?></span>
                    </div>
                </div>
            </div>

            <!-- بطاقة إجمالي البيع -->
            <div class="<?= $colClass ?>">
                <div class="info-box info-box-colored bg-gradient-green">
                    <span class="info-box-icon"><i class="fa fa-fw fa-money"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= Yii::t('app', 'اجمالي البيع') ?></span>
                        <span class="info-box-number"><?= number_format($salePrice, 3) ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <hr>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    $gridColumn = [
        'category',
        [
            'label' => Yii::t('app', 'Name'),
            'headerOptions' => ['style' => 'width:25%'],
            'value' => function ($data) {
                return $data->name;
            },
            'group' => true,
            'groupFooter' => function ($model, $key, $index, $widget) {
                return [
                    'content' => [
                        3 => 'مجموع الكمية ',
                        5 => GridView::F_SUM,
                    ],
                    'contentFormats' => [
                        3 => ['format' => 'number', 'decimals' => 3],
                        4 => ['format' => 'number', 'decimals' => 3],
                        5 => ['format' => 'number', 'decimals' => 0],
                        6 => ['format' => 'number', 'decimals' => 3],
                    ],
                    'contentOptions' => [
                        1 => ['style' => 'font-variant:small-caps'],
                        3 => ['style' => 'text-align:right'],
                        4 => ['style' => 'text-align:right'],
                        5 => ['style' => 'text-align:right'],
                        6 => ['style' => 'text-align:right'],
                    ],
                    'options' => ['class' => 'info table-info', 'style' => 'font-weight:bold;']
                ];
            }
        ],
        [
            'label' => Yii::t('app', 'Serial No'),
            'headerOptions' => ['style' => 'width:10%'],
            'value' => function ($data) {
                return $data->serialNo;
            }
        ],
        [
            'label' => Yii::t('app', 'Comm Code'),
            'headerOptions' => ['style' => 'width:10%'],
            'value' => function ($data) {
                return $data->commCode;
            }
        ],
        'company',
        [
            'label' => Yii::t('app', 'quantity'),
            'value' => function ($data) {
                return $data->quantity;
            }
        ],
    ];

    // إضافة أعمدة التكلفة والبيع للجدول فقط عند امتلاك الصلاحية
    if ($seeCostPrice == 1) {
        $gridColumn[] = [
            'label' => Yii::t('app', 'Cost Price'),
            'value' => function ($data) {
                return $data->costPrice;
            }
        ];
        $gridColumn[] = [
            'label' => Yii::t('app', 'Max Price'),
            'value' => function ($data) {
                return $data->maxPrice;
            }
        ];
        $gridColumn[] = [
            'label' => Yii::t('app', 'Min Price'),
            'value' => function ($data) {
                return $data->minPrice;
            }
        ];
    }

    $gridColumn[] = [
        'label' => Yii::t('app', 'Branch'),
        'headerOptions' => ['style' => 'width:10%'],
        'value' => function ($data) {
            return $data->branches0->name ?? '';
        },
    ];
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumn,
        'summary' => '',
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-inventory']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title . ' ' . date('Y-m-d')),
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>