<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\TransferItemsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Transfer Items');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transfer-items-index">

    <h3 class="mb-3"><i class="bi bi-arrow-left-right"></i> <?= Html::encode($this->title) ?></h3>

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
            <div class="card-body">
                <?= $this->render('_search', ['model' => $searchModel]) ?>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'summary' => '',
                'layout' => '<div class="table-responsive">{items}</div>{pager}',
                'tableOptions' => ['class' => 'table table-striped table-hover align-middle mb-0'],
                'emptyText' => 'لا توجد عمليات نقل',
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute' => 'fromBranch0.name',
                        'label' => 'من الفرع',
                        'format' => 'raw',
                        'value' => function ($data) {
                            return '<i class="bi bi-box-arrow-up text-danger"></i> ' . Html::encode((string) $data->fromBranch0?->name);
                        },
                    ],
                    [
                        'attribute' => 'toBranch0.name',
                        'label' => 'إلى الفرع',
                        'format' => 'raw',
                        'value' => function ($data) {
                            return '<i class="bi bi-box-arrow-in-down text-success"></i> ' . Html::encode((string) $data->toBranch0?->name);
                        },
                    ],
                    [
                        'attribute' => 'at',
                        'headerOptions' => ['class' => 'text-nowrap'],
                        'contentOptions' => ['class' => 'text-nowrap'],
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
