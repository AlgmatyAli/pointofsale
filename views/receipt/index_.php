<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ReceiptSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

/** @var yii\web\View $this */
/** @var app\models\ReceiptSearch $searchModel */
/** @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Receipts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="receipt-index">

    <!-- <h1><?= Html::encode($this->title) ?></h1><hr> -->
    <hr>

    <?php echo $this->render('_search_', ['model' => $searchModel]); ?>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'showPageSummary' => true,
        'summary' => '',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'rId',
            [
                'label' => Yii::t('app', 'Client Name'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->c->name;
                }
            ],

            [
                'label' => Yii::t('app', 'At'),
                'format' => 'raw',
                'value' => 'at',

            ],

            [
                'label' => Yii::t('app', 'Value'),
                'attribute' => 'value',
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary' => true,
            ],
            [
                'label' => Yii::t('app', 'Payment Type'),
                'format' => 'raw',
                'value' => 'paymentType0.name',

            ],
            [
                'label' => Yii::t('app', 'Why'),
                'format' => 'html',
                'value' => 'why',

            ],
            [
                'label' => Yii::t('app', 'Type'),
                'format' => 'raw',
                'value' => function ($searchModel) {

                    if ($searchModel->type == 1) {
                        return 'قبض';
                    } elseif ($searchModel->type == 2) {
                        return 'صرف';
                    }
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'options' => ['style' => 'width:120px;'],
                'template' => '<div class="btn-group btn-group-sm" role="group" aria-label="...">{view}</div>',
                'buttons' => [
                    'view' => function ($url, $searchModel, $key) {
                        return Html::a('<i class="fa fa-eye"></i>', $url, ['class' => 'btn btn-default']);
                    },


                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>