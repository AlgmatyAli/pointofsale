<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var  yii\web\View $this*/
/** @var  app\models\SalesSearch $searchModel*/
/** @var  yii\data\ActiveDataProvider $dataProvider */


$this->title = Yii::t('app', 'تقرير تفصيلي عن المبيعات');
?>
<div class="sales-index">
    <h3><?= Html::encode($this->title) ?></h3>
    <hr>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'showPageSummary' => true,
        'summary' => '',
        'rowOptions' => function ($searchModel) {
            if ($searchModel->deleviried == '0') {
                return ['class' => 'table-danger'];
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'width' => '50px',
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detail' => function ($model, $key, $index, $column) {
                    return Yii::$app->controller->renderPartial('_expand', ['model' => $model]);
                },
                'headerOptions' => ['class' => 'kartik-sheet-style'],
                'expandOneOnly' => true,

                // ---- إضافة أيقونات Bootstrap Icons وتنسيقها ----
                'expandIcon' => '<i class="bi bi-chevron-left"></i>',    // أو bi-plus-circle
                'collapseIcon' => '<i class="bi bi-chevron-down"></i>',  // أو bi-dash-circle
                'expandTitle' => Yii::t('app', 'Expand'),
                'collapseTitle' => Yii::t('app', 'Collapse'),
            ],

            'billId',
            'at',

            [
                'label' => Yii::t('app', 'C ID'),
                'format' => 'text',
                'value' => 'c.name',
                'contentOptions' => function ($model) {
                    return [
                        'class' => 'cell-with-tooltip',
                        'data-bs-toggle' => 'tooltip',
                        'style' => 'font-size:14px;',
                        'data-bs-placement' => 'top',
                        'data-bs-container' => 'body',
                        'title' => $model->notes,
                    ];
                }

            ],

            [
                'label' => Yii::t('app', 'Pay Way'),
                'format' => 'raw',
                'value' => function ($searchModel) {
                    if ($searchModel->type != 2) {
                        if ($searchModel->payWay == 0) {
                            return 'نقدا';
                        } elseif ($searchModel->payWay == 1) {
                            return 'آجل';
                        } elseif ($searchModel->payWay == 2) {
                            return 'دفعة على الحساب';
                        }
                    } else {
                        if ($searchModel->payWay == 1) {
                            return 'نقدا';
                        } elseif ($searchModel->payWay == 2) {
                            return 'على الحساب';
                        }
                    }
                }
            ],

            [
                'label' => Yii::t('app', 'Payment Type'),
                'format' => 'raw',
                'value' => function ($searchModel) {
                    return Html::encode($searchModel->paymentType0?->name);
                }
            ],

            [
                'label' => Yii::t('app', 'Type'),
                'format' => 'raw',
                'value' => function ($searchModel) {

                    if ($searchModel->type == 1) {
                        return 'مبيعات';
                    } elseif ($searchModel->type == 2) {
                        return 'مسترجع مبيعات';
                    } elseif ($searchModel->type == 3) {
                        return 'فاتورة حجز';
                    } elseif ($searchModel->type == 4) {
                        return 'فاتورة مبدئية';
                    }
                }
            ],

            [
                'label' => Yii::t('app', 'Total'),
                'attribute' => 'total',
                'format' => ['decimal', 3],
                'hAlign' => 'right',
                'pageSummary' => true,
            ],

            [
                'label' => Yii::t('app', 'Disscount'),
                'attribute' => 'disscount',
                'format' => ['decimal', 3],
                'hAlign' => 'right',
                'pageSummary' => true,
            ],

            [
                'label' => Yii::t('app', 'Net'),
                'format' => ['decimal', 3],
                'pageSummary' => true,
                'value' => function ($data) {
                    return $data->total - $data->disscount;
                }
            ],

            [
                'label' => Yii::t('app', 'User Insert'),
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::encode($data->userInsert?->username);
                }
            ],


            [
                'class' => 'yii\grid\ActionColumn',
                'options' => ['style' => 'width:120px;'],
                'template' => '<div class="btn-group btn-group-sm" role="group" aria-label="...">{print}</div>',
                'buttons' => [
                    'print' => function ($url, $searchModel, $key) {
                        return Html::a('<i class="bi bi-eye"></i>', $url, ['class' => 'btn btn-outline-secondary btn-sm']);
                    },
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>