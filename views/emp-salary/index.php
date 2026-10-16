<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\EmpSalarySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Emp Salaries');
$this->params['breadcrumbs'][] = $this->title;

// ---- الإجماليات (نفس منطق الملف الأصلي دون تغيير) ----
$sumDisscount = 0;
$sumExtra = 0;
$sumDrawing = 0;
foreach ($dataProvider->getModels() as $value) {
    if ($value['type'] == 1) {
        $sumDrawing = $sumDrawing + $value['value'];
    } elseif ($value['type'] == 2) {
        $sumDisscount = $sumDisscount + $value['value'];
    } elseif ($value['type'] == 3) {
        $sumExtra = $sumExtra + $value['value'];
    }
}

$fmt = fn($v) => Yii::$app->formatter->asDecimal($v, 3);

// بطاقات الإجماليات: [العنوان، القيمة، لون الأيقونة، الأيقونة]
$cards = [
    [Yii::t('app', 'اجمالي الخصم'), $sumDisscount, 'text-bg-warning', 'bi-scissors'],
    [Yii::t('app', 'اجمالي الاضافي'), $sumExtra, 'text-bg-danger', 'bi-plus-circle'],
    [Yii::t('app', 'اجمالي السحوبات'), $sumDrawing, 'text-bg-success', 'bi-cash-coin'],
    [Yii::t('app', 'اجمالي السحوبات + الاضافي'), $sumDrawing + $sumExtra, 'text-bg-primary', 'bi-wallet2'],
];

// نوع الحركة: [النص، لون الشارة]
$types = [
    1 => ['سحب', 'text-bg-success'],
    2 => ['خصم', 'text-bg-warning'],
    3 => ['اضافي', 'text-bg-danger'],
];
?>

<div class="emp-salary-index">
    <?php Pjax::begin(); ?>

    <h3 class="mb-3"><i class="bi bi-people"></i> <?= Html::encode($this->title) ?></h3>

    <!-- بطاقات الإجماليات: بطاقتان في كل صف على الهاتف، أربع على الشاشات الكبيرة -->
    <div class="row g-2 g-md-3 mb-3">
        <?php foreach ($cards as [$label, $value, $color, $icon]): ?>
            <div class="col-6 col-xl-3">
                <div class="info-box mb-0 h-100">
                    <span class="info-box-icon <?= $color ?> shadow-sm"><i class="bi <?= $icon ?>"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-wrap"><?= Html::encode($label) ?></span>
                        <span class="info-box-number"><?= $fmt($value) ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- البحث: مطويّ على الهاتف، ظاهر دائماً على الشاشات المتوسطة وما فوق -->
    <div class="card mb-3">
        <div class="card-header d-md-none p-0">
            <button class="btn w-100 text-start py-2" type="button" data-bs-toggle="collapse"
                data-bs-target="#emp-salary-search" aria-expanded="false" aria-controls="emp-salary-search">
                <i class="bi bi-funnel"></i> بحث وتصفية
                <i class="bi bi-chevron-down float-end"></i>
            </button>
        </div>
        <div id="emp-salary-search" class="collapse d-md-block">
            <div class="card-body">
                <?= $this->render('_search', ['model' => $searchModel]) ?>
            </div>
        </div>
    </div>

    <!-- جدول الحركات -->
    <div class="card">
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'showPageSummary' => true,
                'summary' => '',
                'bordered' => false,
                'striped' => false,
                'condensed' => true,
                'hover' => true,
                'responsive' => true,
                'rowOptions' => function ($row) {
                    if ($row->type == 3) {
                        return ['class' => 'table-danger'];
                    }
                    if ($row->type == 2) {
                        return ['class' => 'table-warning'];
                    }
                    return [];
                },
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'emp.name',

                    [
                        'attribute' => 'at',
                        'headerOptions' => ['class' => 'text-nowrap'],
                        'contentOptions' => ['class' => 'text-nowrap'],
                    ],

                    [
                        'label' => Yii::t('app', 'Value'),
                        'format' => 'decimal',
                        'value' => function ($row) {
                            return $row->value;
                        },
                        'hAlign' => 'right',
                        'pageSummary' => false,
                    ],

                    // الشهر والسنة يُخفيان على الهاتف (التاريخ يكفي هناك)
                    [
                        'attribute' => 'month',
                        'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                        'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                    ],
                    [
                        'attribute' => 'year',
                        'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                        'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                    ],

                    [
                        'label' => Yii::t('app', 'TranType'),
                        'format' => 'raw',
                        'value' => function ($row) use ($types) {
                            if (!isset($types[(int) $row->type])) {
                                return '';
                            }
                            [$text, $class] = $types[(int) $row->type];
                            return '<span class="badge ' . $class . '">' . Html::encode($text) . '</span>';
                        },
                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '',
                        'headerOptions' => ['style' => 'width:1%'],
                        'contentOptions' => ['class' => 'text-nowrap'],
                        'template' => '<div class="btn-group btn-group-sm" role="group">{view}{update}{delete}</div>',
                        'buttons' => [
                            'view' => function ($url) {
                                return Html::a('<i class="bi bi-eye"></i>', $url, [
                                    'class' => 'btn btn-outline-secondary',
                                    'title' => Yii::t('app', 'View'),
                                    'aria-label' => Yii::t('app', 'View'),
                                    'data-pjax' => '0',
                                ]);
                            },
                            // التعديل يذهب لشاشة نوع الحركة: السحب update， الخصم u-discount， الإضافي u-extra-job
                            'update' => function ($url, $row) {
                                $routes = [1 => 'update', 2 => 'u-disscount', 3 => 'u-extra-job'];
                                $url = \yii\helpers\Url::to([$routes[(int) $row->type] ?? 'update', 'id' => $row->id]);
                                return Html::a('<i class="bi bi-pencil-square"></i>', $url, [
                                    'class' => 'btn btn-outline-primary',
                                    'title' => Yii::t('app', 'Update'),
                                    'aria-label' => Yii::t('app', 'Update'),
                                    'data-pjax' => '0',
                                ]);
                            },
                            // الحذف عبر POST مع تأكيد (الحذف بالرابط العادي GET غير آمن)
                            'delete' => function ($url) {
                                return Html::a('<i class="bi bi-trash"></i>', $url, [
                                    'class' => 'btn btn-outline-danger',
                                    'title' => Yii::t('app', 'Delete'),
                                    'aria-label' => Yii::t('app', 'Delete'),
                                    'data-pjax' => '0',
                                    'data-confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                    'data-method' => 'post',
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