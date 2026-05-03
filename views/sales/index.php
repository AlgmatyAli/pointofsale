<?php

use kartik\grid\GridView;
use yii\helpers\Html;

use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$search = "$('.search-button').click(function(){ 
    $('.search-form').toggle(1000); 
    return false; 
 });";
$this->registerJs($search);

$this->title = Yii::t('app', 'Sales');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-index">

    <hr>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'showPageSummary' => true,
        'summary' => '',
        'rowOptions' => function ($searchModel) {
            if ($searchModel->deleviried == '0') {
                return ['class' => 'danger'];
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
                'expandOneOnly' => true
            ],

            'billId',
            'at',

            [
                'label' => Yii::t('app', 'C ID'),
                'format' => 'raw',
                'value' => 'c.name',
                // function ($data) {
                //     return $data->c->name;
                // },
                'contentOptions' => function ($model) {
                    return [
                        'class' => 'cell-with-tooltip',
                        'data-toggle' => 'tooltip',
                        'style' => 'font-size:14px;',
                        'data-placement' => 'top', // top, bottom, left, right
                        'data-container' => 'body', // to prevent breaking table on hover
                        'title' =>  $model->notes,
                        'value' => 'notes',
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
                    return $searchModel->paymentType0->name;
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
                // 'format' => 'currency',
                'format' => ['decimal', 3],
                'hAlign' => 'right',
                'pageSummary' => true,
            ],

            [
                'label' => Yii::t('app', 'Disscount'),
                'attribute' => 'disscount',
                // 'format' => 'currency',
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
                    return $data->userInsert->username;
                }
            ],


            [
                'class' => 'yii\grid\ActionColumn',
                'options' => ['style' => 'width:120px;'],
                'template' => '<div class="btn-group btn-group-sm" role="group" aria-label="...">{print}</div>',
                'buttons' => [
                    'print' => function ($url, $searchModel, $key) {
                        return Html::a('<i class="fa fa-eye"></i>', $url, ['class' => 'btn btn-default']);
                    },
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>