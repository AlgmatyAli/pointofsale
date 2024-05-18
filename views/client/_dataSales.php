<?php
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
    $dataProvider = new ArrayDataProvider([
        'allModels' => $model->sales,
        'key' => 'id'
    ]);
    $gridColumns = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],
        'billId',
        'at',
       
        'total',
        'paid',
        ['class' => 'yii\grid\ActionColumn', 
        'template' => '{asign}',
        'buttons' => [
            'asign' => function($url, $model, $key) {

                $url = Url::to(['sales/print', 'id' => $model['id'] ]);

                return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, [
                    'title' => Yii::t('app', 'Asign'),
                ]);

            }
        ],

        ],
       



        // [
        //     'class' => 'yii\grid\ActionColumn',
        //     'controller' => 'sales'
        // ],
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
