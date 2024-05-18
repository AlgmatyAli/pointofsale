<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Client */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Client', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= yii::t('app','Client').' '. Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-3" style="margin-top: 15px">
            
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ])
            ?>
        </div>
    </div>

    <div class="row">
    <div class="col-md-12">
            <?php 
                $gridColumn = [
                    ['attribute' => 'id', 'visible' => false],
                    'name',
                    'phone',
                    'mobile',
                    'address',
                    'email:email',
                    'balance',
                    'debt',
                    'type',
                   
                   
                ];
                echo DetailView::widget([
                    'model' => $model,
                    'attributes' => $gridColumn
                ]);
            ?>
    
            <?php
            if($providerPurchases->totalCount){
                $gridColumnPurchases = [
                    ['class' => 'yii\grid\SerialColumn'],
                        ['attribute' => 'id', 'visible' => false],
                        'billId',
                                
                        
                        'clientBill',
                       
                        ['attribute' => 'total',
                        'pageSummary' => true,
                    ],
                       
                        'notes',
                       
                        'at',

                        ['class' => 'yii\grid\ActionColumn', 
                            'template' => '{view}',
                            'buttons' => [
                                'view' => function($url, $model, $key) {

                                    $url = Url::to(['purchases/print-bill', 'id' => $model['id'] ]);

                                    return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, [
                                        'title' => Yii::t('app', 'Asign'),
                                    ]);

                                }
                            ],

        ],
                ];
                echo Gridview::widget([
                    'dataProvider' => $providerPurchases,
                    'pjax' => true,
                    'summary'=>true,
                    'showPageSummary' => true,
                    'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-purchases']],
                    'panel' => [
                        'type' => GridView::TYPE_PRIMARY,
                        'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode(yii::t('app','Purchases')),
                    ],
                    'export' => false,
                    'columns' => $gridColumnPurchases
                ]);
            }
            ?>

                
            <?php
            if($providerDebit->totalCount){
                $gridColumnReceipt = [
                    ['class' => 'yii\grid\SerialColumn'],
                        ['attribute' => 'id', 'visible' => false],
                        'rId',
                        ['attribute' => 'value',
                        'pageSummary' => true,
                    ],      
                        
                        'why',
                        'at',
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
                echo Gridview::widget([
                    'dataProvider' => $providerDebit,
                    'pjax' => true,
                    'summary'=>true,
                    'showPageSummary' => true,
                    'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-receipt']],
                    'panel' => [
                        'type' => GridView::TYPE_PRIMARY,
                        'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode(yii::t('app','Receipt')),
                    ],
                    'export' => false,
                    'columns' => $gridColumnReceipt
                ]);
            }
            ?>

            <?php
            if($providerSales->totalCount){
                $gridColumnSales = [
                    ['class' => 'yii\grid\SerialColumn'],
                        ['attribute' => 'id', 'visible' => false],
                        'billId',

                         
                        ['attribute' => 'total',
                        'pageSummary' => true,
                    ],
                    ['attribute' => 'paid',
                    'pageSummary' => true,
                ],
                        'notes',
                       
                        'at',
                        ['class' => 'yii\grid\ActionColumn', 
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function($url, $model, $key) {

                                $url = Url::to(['sales/print', 'id' => $model['id'] ]);

                                return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, [
                                    'title' => Yii::t('app', 'Asign'),
                                ]);

                            }
                        ],

        ],
                ];
                echo Gridview::widget([
                    'dataProvider' => $providerSales,
                    'pjax' => true,
                    'summary'=>true,
                    'showPageSummary' => true,
                    'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-sales']],
                    'panel' => [
                        'type' => GridView::TYPE_PRIMARY,
                        'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode(yii::t('app','Sales')),
                    ],
                    'export' => false,
                    'columns' => $gridColumnSales
                ]);
            }
            ?>

            
<?php
            if($providerInitsales->totalCount){
                $gridColumnSales = [
                    ['class' => 'yii\grid\SerialColumn'],
                        ['attribute' => 'id', 'visible' => false],
                        'billId',

                         
                        ['attribute' => 'total',
                        'pageSummary' => true,
                    ],
                   
                        'notes',
                       
                        'at',
                        ['class' => 'yii\grid\ActionColumn', 
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function($url, $model, $key) {

                                $url = Url::to(['sales/print', 'id' => $model['id'] ]);

                                return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, [
                                    'title' => Yii::t('app', 'Asign'),
                                ]);

                            }
                        ],

        ],
                ];
                echo Gridview::widget([
                    'dataProvider' => $providerInitsales,
                    'pjax' => true,
                    'summary'=>true,
                    'showPageSummary' => true,
                    'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-sales']],
                    'panel' => [
                        'type' => GridView::TYPE_PRIMARY,
                        'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode(yii::t('app','Proforma Invoice')),
                    ],
                    'export' => false,
                    'columns' => $gridColumnSales
                ]);
            }
            ?>
            
           <?php
            if($providerCredit->totalCount){
                $gridColumnReceipt = [
                    ['class' => 'yii\grid\SerialColumn'],
                        ['attribute' => 'id', 'visible' => false],
                        'rId',
                        ['attribute' => 'value',
                        'pageSummary' => true,
                    ],      
                        
                        'why',
                        'at',
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
                echo Gridview::widget([
                    'dataProvider' => $providerCredit,
                    'pjax' => true,
                    'summary'=>true,
                    'showPageSummary' => true,
                    'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-receipt']],
                    'panel' => [
                        'type' => GridView::TYPE_PRIMARY,
                        'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode(yii::t('app','Receipt')),
                    ],
                    'export' => false,
                    'columns' => $gridColumnReceipt
                ]);
            }
            ?>

    
 
    </div>
</div>
