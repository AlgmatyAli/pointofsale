<?php
use yii\helpers\Html;
use kartik\tabs\TabsX;
use yii\helpers\Url;
$items = [
    
                [
        'label' => '<i class="glyphicon glyphicon-book"></i> '. Html::encode(yii::t('app','Purchases')),
        'content' => $this->render('_dataPurchases', [
            'model' => $model,
            'row' => $model->purchases,
        ]),
    ],
            [
        'label' => '<i class="glyphicon glyphicon-book"></i> '. Html::encode(yii::t('app','Receipt')),
        'content' => $this->render('_dataReceipt', [
            'model' => $model,
            'row' => $model->receipts,
        ]),
    ],
            [
        'label' => '<i class="glyphicon glyphicon-book"></i> '. Html::encode(yii::t('app','Sales')),
        'content' => $this->render('_dataSales', [
            'model' => $model,
            'row' => $model->sales,
        ]),
    ],
    ];
echo TabsX::widget([
    'items' => $items,
    'position' => TabsX::POS_ABOVE,
    'encodeLabels' => false,
    'class' => 'tes',
    'pluginOptions' => [
        'bordered' => true,
        'sideways' => true,
        'enableCache' => false
    ],
]);
?>
