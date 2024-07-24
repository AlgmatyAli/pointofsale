<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\CompanyInfo;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Purchases */

$this->title = $model->billId;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Purchases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="purchases-view">
    <br>
    <h1><?= Html::encode($this->title) ?></h1><hr><br>
    <p>
    <div class="btn-group">
        <?php
        if ($model->type == 1 || $model->type == 3) {
            echo Html::a(
                '<i class="fa fa-fw fa-edit"></i>' . ' ' . Yii::t('app', 'Update'),
                ['update', 'id' => $model->id],
                ['class' => 'btn btn-primary btn-sm']
            );
        } else {
            echo  Html::a(
                '<i class="fa fa-fw fa-edit"></i>' . ' ' . Yii::t('app', 'Update'),
                ['update', 'id' => $model->id],
                ['class' => 'btn btn-primary btn-sm']
            );
        }

        ?>

        <?php
        if (Yii::$app->user->identity->printPurtchaseInvoice == 1) {
            echo Html::a('<i class="fa fa-fw fa-print"></i>' . ' ' . Yii::t('app', 'Print Bill'), ['print-bill', 'id' => $model->id], ['class' => 'btn btn-success btn-sm']);
        }
        ?>

        <?= Html::a('<i class="fa fa-fw fa-print"></i>' . ' ' . Yii::t('app', 'Print With Hang OUt Q'), ['noprice', 'id' => $model->id], ['class' => 'btn btn-secondary']) ?>


        <?= Html::a('<i class="fa fa-fw fa-print"></i>' . ' ' . Yii::t('app', 'Print Bill With Prices'), ['print-bill-with-out-price', 'id' => $model->id], ['class' => 'btn btn-info btn-sm']) ?>

        <?= Html::a('<i class="fa fa-fw fa-print"></i>' . ' ' . Yii::t('app', 'Print Bill With Place'), ['print-bill-with-place', 'id' => $model->id], ['class' => 'btn btn-defualt btn-sm']) ?>

        <?= Html::button('<i class="fa fa-fw fa-copy"></i>' . ' ' . Yii::t('app', 'SaveAsNew'), ['value' => Url::to(['purchases/save-as-new', 'oldId' => $model->id]), 'class' => 'btn btn-success btn-sm popup']); ?>

        <?=  Html::a(
                '<i class="fa fa-fw fa-edit"></i>' . ' ' . Yii::t('app', 'Update Sales Prices'),
                ['purchases-details', 'id' => $model->id],
                ['class' => 'btn btn-primary btn-sm']
            );
            ?>
        <?=
        Html::a('<i class="fa fa-fw fa-trash"></i>' . ' ' . Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-warning btn-sm',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ])
        ?>

        <?= Html::a('<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Back'), Yii::$app->request->referrer, ['class' => 'btn btn-danger btn-sm']) ?>
    </div>
    </p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'billId',
            [
                'label' => Yii::t('app', 'Client Name'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->c->name;
                }
            ],
            'clientBill',
            'at',
            [
                'label' => Yii::t('app', 'Total'),
                'attribute' => 'total',
                'format' => ['decimal', 3],
                'hAlign' => 'right',
                'pageSummary' => true,
            ],

            [
                'label' => Yii::t('app', 'Total Cost'),
                'attribute' => 'totalCost',
                'format' => ['decimal', 3],
                'hAlign' => 'right',
                'pageSummary' => true,
            ],
            [
                'label' => Yii::t('app', 'Paid'),
                'attribute' => 'paid',
                'format' => ['decimal', 3],
                'hAlign' => 'right',
                'pageSummary' => true,
            ],

            [
                'label' => Yii::t('app', 'Net'),
                'format' => ['decimal', 3],
                'value' => function ($data) {
                    return $data->total - $data->paid;
                }
            ],

            [
                'label' => Yii::t('app', 'Pay Way'),
                'format' => 'raw',
                'value' => function ($model) {

                    if ($model->payWay == '0') {
                        return 'نقدا';
                    } elseif ($model->payWay == '1') {
                        return 'آجل';
                    } elseif ($model->payWay == '2') {
                        return 'دفعة على الحساب';
                    }
                }
            ],
            [
                'label' => Yii::t('app', 'Currancy'),
                'format' => ['raw'],
                'value' => function ($data) {
                    return $data->currancy0->name;
                }
            ],
            'total_currancy',

            [
                'label' => Yii::t('app', 'Type'),
                'format' => 'raw',
                'value' => function ($model) {

                    if ($model->type == 1) {
                        return 'مشتريات';
                    } elseif ($model->type == 2) {
                        return 'مسترجع مشتريات';
                    }
                }
            ],

            [
                'label' => Yii::t('app', 'Br ID'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->br->name;
                }
            ],

            [
                'label' => Yii::t('app', 'User Insert'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->userInsert->username;
                }
            ],
            'created_at',
        ],
    ]) ?>

</div>