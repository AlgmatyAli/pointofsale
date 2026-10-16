<?php

use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\helpers\Html;

$dataProvider = new ArrayDataProvider([
    'allModels' => $model->salesDetails,
    'key' => 'id'
]);
$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    ['attribute' => 'id', 'visible' => false],
    [
        'attribute' => 'cat.name',
        'label' => Yii::t('app', 'Category')
    ],
    [
        'attribute' => 'cat.company',
        'label' => Yii::t('app', 'Company')
    ],
    [
        'label' => Yii::t('app', 'Quantity'),
        'attribute' => 'quantity',
        'format' => ['decimal', 0],
        'pageSummary' => true,
    ],
    'waitQnty',

    [
        'label' => Yii::t('app', 'Sale Price'),
        'attribute' => 'salePrice',
        'format' => ['decimal', 3],
    ],
    [
        'class' => 'kartik\grid\FormulaColumn',
        'header' => Yii::t('app', 'Total'),
        'vAlign' => 'middle',
        'value' => function ($model, $key, $index, $widget) {
            $p = compact('model', 'key', 'index');
            return $widget->col(4, $p) * $widget->col(6, $p);
        },
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'hAlign' => 'right',
        'width' => '7%',
        'format' => ['decimal', 3],
        'mergeHeader' => true,
        'pageSummary' => true,
        'footer' => true
    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{back}',
        // إضافة السطر لقائمة المرتجع المؤقتة: لمن يملك createBackSales فقط، ولا معنى لها في المسترجع أو المبدئية
        'visible' => Yii::$app->user->can('createBackSales') && !in_array((int) $model->type, [2, 4], true),
        'buttons' => [
            'back' => function ($url, $model, $key) {
                $url = Url::to(['sales/back-items', 'id' => $model['id']]);
                // تغيّر حالة (تُضيف للمرتجع المؤقت) فتكون عبر POST
                return Html::a('<i class="bi bi-arrow-repeat fs-5"></i>', $url, [
                    'title' => Yii::t('app', 'Return the item'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]);
            }
        ],

    ],
];

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'showPageSummary' => true,
    'summary' => '',
    'containerOptions' => ['style' => 'overflow: auto'],
    'pjax' => true,
    'beforeHeader' => [
        [
            'options' => ['class' => 'skip-export']
        ]
    ],
    'export' => [
        'fontAwesome' => true
    ],
    'bordered' => true,
    'striped' => true,
    'condensed' => true,
    'responsive' => true,
    'hover' => true,
    'persistResize' => false,
]);
