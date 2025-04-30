<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>


    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php Pjax::begin(); ?>
    <?php
    yii\bootstrap\Modal::begin(['id' => 'modal']);
    yii\bootstrap\Modal::end();
    ?>

    <?php
    $gridColumn = [
        // ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => Yii::t('app', 'ID'),
            'attribute' => 'id',
            'headerOptions' => ['style' => 'width:5%'],
            'value' => function ($data) {
                return $data->category;
            }

        ],
        [
            'label' => Yii::t('app', 'Name'),
            'headerOptions' => ['style' => 'width:25%'],
            'value' => function ($data) {
                return $data->name;
            },
            'group' => true,
            'groupFooter' => function ($model, $key, $index, $widget) { // Closure method
                return [
                    //'mergeColumns' => [[5, 6]], // columns to merge in summary
                    'content' => [             // content to show in each summary cell
                        5 => 'المجموع',
                        6 => GridView::F_SUM,
                    ],

                    'contentFormats' => [      // content reformatting for each summary cell
                        3 => ['format' => 'number', 'decimals' => 3],
                        4 => ['format' => 'number', 'decimals' => 3],
                        6 => ['format' => 'number', 'decimals' => 0],
                        7 => ['format' => 'number', 'decimals' => 3],
                    ],
                    'contentOptions' => [      // content html attributes for each summary cell
                        1 => ['style' => 'font-variant:small-caps'],
                        3 => ['style' => 'text-align:right'],
                        4 => ['style' => 'text-align:right'],
                        5 => ['style' => 'text-align:left'],
                        6 => ['style' => 'text-align:right'],
                    ],
                    // html attributes for group summary row
                    'options' => ['class' => 'info table-info', 'style' => 'font-weight:bold;']
                ];
            }
        ],

        [
            'label' => Yii::t('app', 'Class'),
            'attribute' => 'id',
            'headerOptions' => ['style' => 'width:8%'],
            'value' => function ($data) {
                return $data->class;
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

        [
            'attribute' => 'company',
            'headerOptions' => ['style' => 'width:8%'],
            'value' => function ($data) {
                return $data->company;
            }

        ],

        [
            'label' => Yii::t('app', 'quantity'),
            'headerOptions' => ['style' => 'width:5%'],
            'value' => function ($data) {
                return $data->quantity;
            }

        ],

        [
            'label' => Yii::t('app', 'Branch'),
            'headerOptions' => ['style' => 'width:10%'],
            'value' => function ($data) {
                return $data->branches0->name;
            },
        ],

        [
            'label' => Yii::t('app', 'Status'),
            'attribute' => 'status',
            'headerOptions' => ['style' => 'width:10%'],
            'value' => function ($model) {
                return $model->status == 0 ? 'مفعل' : 'غير مفعل';
            },
        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'options' => ['style' => 'width:120px;'],
            'template' => '<div class="btn-group btn-group-sm" role="group" aria-label="...">{view}{update}</div>',
            'buttons' => [
                'view' => function ($url, $searchModel, $key) {
                    return Html::a('<i class="fa fa-eye"></i>', ['category/view', 'id' => $searchModel->category], ['class' => 'btn btn-default']);
                },
                'update' => function ($url, $searchModel, $key) {
                    return Html::a('<i class="fa fa-edit"></i>', ['category/update', 'id' => $searchModel->category], ['class' => 'btn btn-default']);
                },
            ]
        ]
    ]
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumn,
        'summary' => '',
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-inventory']],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title . ' ' . date('Y-m-d')),
        ],
        // your toolbar can include the additional full export menu

    ]);
    ?>

    <?php
    // echo GridView::widget([
    //     'dataProvider' => $dataProvider,
    //     'id' => 'grid-id',
    //     //'filterModel' => $searchModel,
    //     'summary' => '',
    //     // 'rowOptions' => function ($searchModel) {
    //     //     if ($searchModel->status == '1') {
    //     //         return ['class' => 'danger'];
    //     //     }
    //     // },
    //     'columns' => [
    //         ['class' => 'yii\grid\SerialColumn'],

    //         'name',
    //         'serialNo',
    //         'commCode',
    //         'class',
    //         'company',
    //         'place',
    //         'quantity',
    //         'branch',
    //         [
    //             'class' => 'yii\grid\ActionColumn',
    //             'options' => ['style' => 'width:120px;'],
    //             'template' => '<div class="btn-group btn-group-sm" role="group" aria-label="...">{view}{update}</div>',
    //             'buttons' => [
    //                 'view' => function ($url, $searchModel, $key) {
    //                     return Html::a('<i class="fa fa-eye"></i>', $url, ['class' => 'btn btn-default']);
    //                 },
    //                 'update' => function ($url, $searchModel, $key) {
    //                     return Html::a('<i class="fa fa-edit"></i>', $url, ['class' => 'btn btn-default']);
    //                 },


    //             ]
    //         ],
    //         //     [
    //         //         'attribute' => 'category',
    //         //         'headerOptions' => ['style' => 'width:40%'],
    //         //         'value' => function ($model) {
    //         //             return Html::a(Yii::t('app', ' {modelClass}', [
    //         //                 'modelClass' => 'open',
    //         //             ]), ['category/update', 'id' => $model->id], ['class' => 'btn btn-default popupModal']);
    //         //         },
    //         //         'format' => 'raw',
    //         //     ],
    //     ],
    // ]);
    ?>

    <?php Pjax::end(); ?>

</div>

<?php
$this->registerJs("$(function() {
     $('.popupModal').click(function(e) {
     e.preventDefault();
     $('#modal').modal('show').find('.modal-content')
     .load($(this).attr('href'));
     });
});");

?>