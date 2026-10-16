<?php

use yii\helpers\Html;
use kartik\tabs\TabsX;

/** @var app\models\Purchases $model */

$items = [

    [
        'label' => '<i class="glyphicon glyphicon-book"></i> ' . Html::encode(Yii::t('app', 'Purchases Details')),
        'content' => $this->render('_dataPurchasesDetails', [
            'model' => $model,
            'row' => $model->purchasesDetails,
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
