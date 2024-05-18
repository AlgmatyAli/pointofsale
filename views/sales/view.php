<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;


/* @var $this yii\web\View */
/* @var $model app\models\Sales */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sales-view">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <p>
    <?php 
     if($model->type == 1){
     echo Html::a('<i class="fa fa-fw fa-edit"></i>'.' '.Yii::t('app', 'Update'), ['update', 'id' => $model->id],
     ['class' => 'btn btn-primary btn-lg']);
     }else{
      echo  Html::a('<i class="fa fa-fw fa-edit"></i>'.' '.Yii::t('app', 'Update'), ['back-sale-update', 'id' => $model->id],
        ['class' => 'btn btn-primary btn-lg']) ;
     }
     
    ?>
        <?php
        //  Html::a('<i class="fa fa-fw fa-trash"></i>'.' '.Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
        //     'class' => 'btn btn-warning btn-lg',
        //     'data' => [
        //         'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
        //         'method' => 'post',
        //     ],
        // ]) 
        ?>
        
        <?= Html::a('<i class="fa fa-fw fa-print"></i>'.' '.Yii::t('app', 'Print Bill'), ['print-bill', 'id' => $model->id], ['class' => 'btn btn-success btn-lg']) ?>

        <?= Html::a('<i class="fa fa-fw fa-window-close"></i>'.' '.Yii::t('app', 'Back'),Yii::$app->request->referrer, ['class'=>'btn btn-danger btn-lg']) ?>

        <?= Html::a('<i class="fa fa-fw fa-edit"></i>'.' '.Yii::t('app', 'Order'), ['order', 'id' => $model->id],
     ['class' => 'btn btn-primary btn-lg']); ?>
    
      <?= Html::a('<i class="fa fa-fw fa-print"></i>'.' '.Yii::t('app', 'Print Bill'), ['pdf'], ['class' => 'btn btn-success btn-lg']) ?>


    </p><br>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            'billId',
            'at',
            [
                'label' => Yii::t('app', 'C ID'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->c->name;
                }
            ],
            [
                'label' => Yii::t('app', 'Phone No.'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->c->phone;
                }
            ],
            [ 
                'label' => Yii::t('app', 'Pay Way'),
                'format' => 'raw',
                   'value'=>function($searchModel) { 
    
                    if($searchModel->payWay ==0){
                        return 'نقدا';
                    }elseif($searchModel->payWay ==1){
                        return 'آجل';
                    }elseif($searchModel->payWay ==2){
                        return 'دفعة على الحساب';
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
            'label' => Yii::t('app', 'Type'),
            'format' => 'raw',
               'value'=>function($searchModel) { 

                if($searchModel->type ==1){
                    return 'مبيعات';
                }elseif($searchModel->type ==2){
                    return 'مسترجع مبيعات';
                }
            }
        ],
            'total',
            'paid',
            
            [
                'label' => Yii::t('app', 'Net'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->total - $data->paid;
                }
            ],

            'notes',
           
            // [
            //     'label' => Yii::t('app', 'User Update'),
            //     'format' => 'raw',
            //     'value' => function ($data) {
            //         return $data->userUpdate->username;
            //     }
            // ],
            // 'update_at',
        ],
    ]) ?>
<?php
if($providerSalesDetails->totalCount){
    $gridColumnSalesDetails = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
                        [
                'attribute' => 'category0.name',
                'label' => Yii::t('app', 'Category')
            ],
            'quantity',
            'costPrice',
            'salePrice',
            'box',
            'expire',
    ];
    echo GridView::widget([
        'dataProvider' => $providerSalesDetails,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-sales-details']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode(Yii::t('app', 'Sales Details')),
        ],
        'export' => false,
        'columns' => $gridColumnSalesDetails
    ]);
}
?>
</div>
