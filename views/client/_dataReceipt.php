<?php
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\helpers\Html;

    $dataProvider = new ArrayDataProvider([
        'allModels' => $model->receipts,
        'key' => 'id'
    ]);
    $gridColumns = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        'rId',
        'at',
        'value',
        'why',
        'payWay',
        'type',
     
        'branch',
        ['class' => 'yii\grid\ActionColumn', 
        'template' => '{view}',
        'buttons' => [
            'view' => function($url, $model, $key) {

                $url = Url::to(['receipt/view', 'id' => $model['id'] ]);

                return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, [
                    'title' => Yii::t('app', 'Asign'),
                ]);

            }
        ],

        ],
    ];
    
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
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
        'showPageSummary' => false,
        'persistResize' => false,
    ]);
