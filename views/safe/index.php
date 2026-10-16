<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\SafeSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Saves');
$this->params['breadcrumbs'][] = $this->title;

// ---- الإجماليات (نفس منطق الملف الأصلي دون تغيير) ----
// type = 1: صادر ($sumDrawing)، type = 2: وارد ($sumDisscount)، والصافي = الوارد - الصادر
$sumDisscount = 0;
$sumExtra = 0;
$sumDrawing = 0;
$sumDiff = 0;
$dataProvider->pagination->pageSize = 10000;
foreach ($dataProvider->getModels() as $value) {
    if ($value['type'] == 1) {
        $sumDrawing = $sumDrawing + $value['value'];
    } elseif ($value['type'] == 2) {
        $sumDisscount = $sumDisscount + $value['value'];
    }
    $sumDiff = $sumDisscount - $sumDrawing;
}

$fmt = static fn ($v): string => number_format((float) $v, 3);

// بطاقات الإجماليات: [العنوان، القيمة، لون الأيقونة، الأيقونة، لون القيمة]
$cards = [
    [Yii::t('app', 'اجمالي الوارد'), $sumDisscount, 'text-bg-success', 'bi-box-arrow-in-down', ''],
    [Yii::t('app', 'اجمالي الصادر'), $sumDrawing, 'text-bg-danger', 'bi-box-arrow-up', ''],
    [Yii::t('app', 'صافي الخزينة'), $sumDiff, 'text-bg-primary', 'bi-safe2', $sumDiff < 0 ? 'text-danger' : 'text-success'],
];
?>
<div class="safe-index">

    <h3 class="mb-3"><i class="bi bi-safe2"></i> <?= Html::encode($this->title) ?></h3>

    <?php Pjax::begin(); ?>

    <!-- بطاقات الإجماليات -->
    <div class="row g-2 g-md-3 mb-3">
        <?php foreach ($cards as [$label, $value, $color, $icon, $valueClass]): ?>
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="info-box mb-0 h-100">
                    <span class="info-box-icon <?= $color ?> shadow-sm"><i class="bi <?= $icon ?>"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= Html::encode($label) ?></span>
                        <span class="info-box-number <?= $valueClass ?>"><?= $fmt($value) ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- البحث: مطويّ على الهاتف، ظاهر دائماً على الشاشات المتوسطة وما فوق -->
    <div class="card mb-3">
        <div class="card-header d-md-none p-0">
            <button class="btn w-100 text-start py-2" type="button" data-bs-toggle="collapse"
                    data-bs-target="#safe-search" aria-expanded="false" aria-controls="safe-search">
                <i class="bi bi-funnel"></i> بحث وتصفية
                <i class="bi bi-chevron-down float-end"></i>
            </button>
        </div>
        <div id="safe-search" class="collapse d-md-block">
            <div class="card-body">
                <?= $this->render('_search', ['model' => $searchModel]) ?>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'showPageSummary' => true,
                'summary' => '',
                'bordered' => false,
                'striped' => true,
                'condensed' => true,
                'hover' => true,
                'responsive' => true,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    // الفرع: يُخفى على الهاتف لتوفير المساحة
                    [
                        'label' => Yii::t('app', 'Br ID'),
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                        'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                        'value' => function ($data) {
                            return Html::encode((string) $data->br?->name);
                        },
                    ],

                    [
                        'attribute' => 'at',
                        'headerOptions' => ['class' => 'text-nowrap'],
                        'contentOptions' => ['class' => 'text-nowrap'],
                    ],

                    [
                        'label' => Yii::t('app', 'Type'),
                        'format' => 'raw',
                        'value' => function ($searchModel) {
                            if ($searchModel->type == 1) {
                                return '<span class="badge text-bg-danger">صادر</span>';
                            } elseif ($searchModel->type == 2) {
                                return '<span class="badge text-bg-success">وارد</span>';
                            }
                            return '';
                        },
                    ],

                    [
                        'label' => Yii::t('app', 'Safe No.'),
                        'format' => 'raw',
                        'value' => function ($searchModel) {
                            if ($searchModel->safeNo == 1) {
                                return 'خزينة رقم 1';
                            } elseif ($searchModel->safeNo == 2) {
                                return 'خزينة رقم 2';
                            }
                            return '';
                        },
                    ],

                    'why',

                    [
                        'label' => Yii::t('app', 'Value'),
                        'value' => function ($searchModel) {
                            if ($searchModel->type == 1) {
                                return $searchModel->value * -1;
                            } elseif ($searchModel->type == 2) {
                                return $searchModel->value;
                            }
                        },
                        'format' => 'decimal',
                        'hAlign' => 'right',
                        'pageSummary' => true,
                        'contentOptions' => function ($searchModel) {
                            return ['class' => $searchModel->type == 1 ? 'text-danger fw-semibold' : 'text-success fw-semibold'];
                        },
                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'headerOptions' => ['style' => 'width:1%'],
                        'contentOptions' => ['class' => 'text-nowrap'],
                        'template' => '<div class="btn-group btn-group-sm" role="group">{view}</div>',
                        'buttons' => [
                            'view' => function ($url) {
                                return Html::a('<i class="bi bi-eye"></i>', $url, [
                                    'class' => 'btn btn-outline-secondary',
                                    'title' => Yii::t('app', 'View'),
                                    'aria-label' => Yii::t('app', 'View'),
                                    'data-pjax' => '0',
                                ]);
                            },
                        ],
                    ],
                ],
            ]) ?>
        </div>
    </div>

    <?php Pjax::end(); ?>

</div>
