<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TransferSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Transfers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transfer-index">

    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <h3 class="mb-0 flex-grow-1"><i class="bi bi-arrow-left-right"></i> <?= Html::encode($this->title) ?></h3>
        <?= Html::a('<i class="bi bi-plus-circle"></i> ' . Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success', 'data-pjax' => '0']) ?>
    </div>

    <?php Pjax::begin(); ?>

    <!-- البحث: مطويّ على الهاتف، ظاهر دائماً على الشاشات المتوسطة وما فوق -->
    <div class="card mb-3">
        <div class="card-header d-md-none p-0">
            <button class="btn w-100 text-start py-2" type="button" data-bs-toggle="collapse"
                    data-bs-target="#transfer-search" aria-expanded="false" aria-controls="transfer-search">
                <i class="bi bi-funnel"></i> بحث وتصفية
                <i class="bi bi-chevron-down float-end"></i>
            </button>
        </div>
        <div id="transfer-search" class="collapse d-md-block">
            <div class="card-body pb-1">
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

                    [
                        'label' => Yii::t('app', 'From Br'),
                        'format' => 'raw',
                        'value' => fn($data) => Html::encode((string) $data->fromBr0?->name),
                    ],
                    [
                        'label' => Yii::t('app', 'To Br'),
                        'format' => 'raw',
                        'value' => fn($data) => Html::encode((string) $data->toBr0?->name),
                    ],

                    [
                        'label' => Yii::t('app', 'Value'),
                        'attribute' => 'value',
                        'format' => 'decimal',
                        'hAlign' => 'right',
                        'pageSummary' => true,
                    ],

                    [
                        'attribute' => 'at',
                        'headerOptions' => ['class' => 'text-nowrap'],
                        'contentOptions' => ['class' => 'text-nowrap'],
                    ],

                    [
                        'label' => Yii::t('app', 'Type'),
                        'format' => 'raw',
                        'value' => function ($row) {
                            if ($row->type == 1) {
                                return '<span class="badge text-bg-danger">صادر</span>';
                            } elseif ($row->type == 2) {
                                return '<span class="badge text-bg-success">وارد</span>';
                            }
                            return '';
                        },
                    ],

                    // البيان: يُخفى على الهاتف لتوفير المساحة
                    [
                        'attribute' => 'why',
                        'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                        'contentOptions' => ['class' => 'd-none d-md-table-cell'],
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
