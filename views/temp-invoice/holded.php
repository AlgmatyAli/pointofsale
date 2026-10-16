<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\TempInvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="temp-invoice-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'summary' => '',
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'label' => Yii::t('app', 'Total'),
                'attribute' => 'salePrice',
                'format' => ['decimal', 3],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{unhold}',
                'buttons' => [
                    'unhold' => function ($url, $model, $key) {
                        return Html::a(
                            '<i class="bi bi-box-arrow-in-down"></i> ' . Yii::t('app', 'Asign'),
                            Url::to(['temp-invoice/unhold', 'id' => $model['invoice_number']]),
                            [
                                'title' => Yii::t('app', 'Asign'),
                                'class' => 'btn btn-outline-primary btn-sm',
                                'data-method' => 'post',
                                'data-pjax' => '0',
                            ]
                        );
                    },
                ],
            ],
        ],
    ]) ?>

</div>
