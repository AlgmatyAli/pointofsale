<?php

use yii\helpers\Html;
use kartik\tabs\TabsX;

$items = [

    [
        'label' => '<i class="bi bi-arrow-repeat fs-5"></i>' . Html::encode(Yii::t('app', 'Sales Details')),
        'content' => $this->render('_dataSalesDetails', [
            'model' => $model,
            'row' => $model->salesDetails,
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
