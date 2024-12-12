<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PurchasesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Purchases');
?>
<div class="purchases-index">
    <hr>
    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>


    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'showPageSummary' => true,
        'summary' => '',
        'rowOptions' => function ($searchModel) {
            if ($searchModel->shippingType == '1') {
                return ['class' => 'danger'];
            } elseif ($searchModel->shippingType == '2') {
                return ['class' => 'warning'];
            }
        },
        'columns' => [
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

            [
                'label' => Yii::t('app', 'C ID'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->c->name;
                }
            ],
            'clientBill',
            'at',

            [
                'label' => Yii::t('app', 'Pay Way'),
                'format' => 'raw',
                'value' => function ($searchModel) {
                    if ($searchModel->payWay == 0) {
                        return 'نقدا';
                    } elseif ($searchModel->payWay == 1) {
                        return 'آجل';
                    } elseif ($searchModel->payWay == 2) {
                        return 'دفعة على الحساب';
                    }
                }
            ],

            [
                'label' => Yii::t('app', 'Br ID'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->br->name;
                }
            ],

            [
                'label' => Yii::t('app', 'Type'),
                'format' => 'raw',
                'value' => function ($searchModel) {

                    if ($searchModel->type == 1) {
                        return 'مشتريات';
                    } elseif ($searchModel->type == 2) {
                        return 'مسترجع مشتريات';
                    } elseif ($searchModel->type == 3) {
                        return 'فاتورة مشتريات معلقة';
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
                'label' => Yii::t('app', 'Total Cost'),
                'attribute' => 'totalCost',
                'format' => ['decimal', 3],
                'hAlign' => 'right',
                'pageSummary' => true,
            ],

            [
                'class' => 'kartik\grid\FormulaColumn',
                'contentOptions' => ['style' => 'font-size:14px;'],
                'header' => Yii::t('app', 'Total'),
                'vAlign' => 'middle',
                'value' => function ($model, $key, $index, $widget) {
                    $p = compact('model', 'key', 'index');
                    return $widget->col(8, $p) + $widget->col(9, $p);
                },
                'headerOptions' => ['class' => 'kartik-sheet-style'],
                'hAlign' => 'right',
                'width' => '10%',
                'format' => ['decimal', 3],
                'mergeHeader' => true,
                'pageSummary' => true,
                'footer' => true

            ],


            [
                'class' => 'yii\grid\ActionColumn',
                'options' => ['style' => 'width:120px;'],
                'template' => '<div class="btn-group btn-group-sm" role="group" aria-label="...">{view}</div>',
                'buttons' => [
                    'view' => function ($url) {
                        return Html::a('<i class="fa fa-eye"></i>', $url, ['class' => 'btn btn-default']);
                    },
                    // 'update'=>function($url,$searchModel,$key){
                    //     return Html::a('<i class="fa fa-edit"></i>',$url,['class'=>'btn btn-default']);
                    // },
                ]
            ],
        ],
    ]);
    ?>

    <?php Pjax::end(); ?>

</div>