<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\ExpensesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Expenses');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expenses-index">

    <h3 class="mb-3"><i class="bi bi-wallet2"></i> <?= Html::encode($this->title) ?></h3>

    <?php Pjax::begin(); ?>

    <!-- البحث: مطويّ على الهاتف، ظاهر دائماً على الشاشات المتوسطة وما فوق -->
    <div class="card mb-3">
        <div class="card-header d-md-none p-0">
            <button class="btn w-100 text-start py-2" type="button" data-bs-toggle="collapse"
                    data-bs-target="#expenses-search" aria-expanded="false" aria-controls="expenses-search">
                <i class="bi bi-funnel"></i> بحث وتصفية
                <i class="bi bi-chevron-down float-end"></i>
            </button>
        </div>
        <div id="expenses-search" class="collapse d-md-block">
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

                    'expenseTo',

                    [
                        'attribute' => 'at',
                        'headerOptions' => ['class' => 'text-nowrap'],
                        'contentOptions' => ['class' => 'text-nowrap'],
                    ],

                    [
                        'label' => Yii::t('app', 'Name'),
                        'format' => 'raw',
                        'value' => function ($data) {
                            return Html::encode((string) $data->item?->name);
                        },
                    ],

                    [
                        'label' => Yii::t('app', 'Value'),
                        'attribute' => 'value',
                        'format' => 'decimal',
                        'hAlign' => 'right',
                        'pageSummary' => true,
                    ],

                    // طريقة الدفع تُخفى على الهاتف لتوفير المساحة
                    [
                        'label' => Yii::t('app', 'Payment Type'),
                        'format' => 'raw',
                        'value' => 'paymentType0.name',
                        'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                        'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                    ],

                    [
                        'attribute' => 'outBox',
                        'format' => 'raw',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'value' => function ($data) {
                            return $data->outBox
                                ? '<span class="badge text-bg-warning">نعم</span>'
                                : '<span class="badge text-bg-light border">لا</span>';
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
