<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\TempInvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use yii\helpers\Url;

?>
<div class="temp-invoice-index">

    <h1><?= Html::encode($this->title) ?></h1>
  
   <?php 
   
  

   echo GridView::widget([
     'summary'=>'',
     'dataProvider' => $dataProvider,
     'columns' => [
        [
            'label' => Yii::t('app', 'Total'),
          //  'headerOptions' => ['style' => 'width:5%'],
            'attribute' => 'salePrice',
            'format' => 'raw'
        ],
        // 'salePrice'
        ['class' => 'yii\grid\ActionColumn', 
                        'template' => '{unhold}',
                        'buttons' => [
                            'unhold' => function($url, $model, $key) {

                                $url = Url::to(['temp-invoice/unhold', 'id' => $model['invoice_number'] ]);

                                return Html::a('<span class="glyphicon glyphicon-save"></span>', $url, [
                                    'title' => Yii::t('app', 'Asign'),
                                ]);

                            }
                        ],

        ],
     ],]);
 
   ?> 
</div>
