<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Receipt */

$this->title = $model->id.' - '.$model->c->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Receipts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="receipt-view">

    <h1><?= Html::encode($this->title) ?></h1><hr>

     <p>
        <?= Html::a('<i class="fa fa-fw fa-edit"></i>'.' '.Yii::t('app', 'Update'), ['update', 'id' => $model->id,'type'=>$model->type], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-fw fa-trash"></i>'.' '.Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-warning',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        
        <?php
         if($model->type == 1){
           echo  Html::a('<i class="fa fa-fw fa-print"></i>'.' '.Yii::t('app', 'Print Reciept Receive'), ['print-reciept-reciver', 'id' => $model->id], ['class' => 'btn btn-success']);
         }elseif($model->type == 2){
           echo Html::a('<i class="fa fa-fw fa-print"></i>'.' '.Yii::t('app', 'Print Reciept'), ['print-reciept', 'id' => $model->id], ['class' => 'btn btn-success']);
         }
         ?>

        <?= Html::a(Yii::t('app', 'Create Client Histrans'), 
         ['sales/histrans', 'client'=> $model->clinet, 'allData'=>1, 'type'=>0], ['class' => 'btn btn-info']) ?>
         
        <?= Html::a('<i class="fa fa-fw fa-window-close"></i>'.' '.Yii::t('app', 'Back'),Yii::$app->request->referrer, ['class'=>'btn btn-danger']) ?>

        <?php echo Html::button('<i class="fa fa-fw fa-step-forward"></i>' . ' ' . Yii::t('app', 'ترحيل الى الخزينة'), ['value' => Url::to(['receipt/transfer-to-safe', 'receiptId' => $model->id]), 'class' => 'btn btn-danger popup']); ?>

    </p><br>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => Yii::t('app', 'Br ID'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->br->name;
                }
            ],
            'rId',
            [
                'label' => Yii::t('app', 'Client Name'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->c->name;
                }
            ],
            'at',
            'value',
            [
                'label' => Yii::t('app', 'Why'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->why;
                }
            ],
            'tafqet',
            'payWay',

            [
                'label' => Yii::t('app', 'Currancy'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->currancy0->name;
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
