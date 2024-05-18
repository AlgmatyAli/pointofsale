<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\alert\Alert;
use barcode\barcode\BarcodeGenerator as BarcodeGenerator;

/* @var $this yii\web\View */
/* @var $model app\models\Category */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="category-view">

    <h1><?= Html::encode($this->title) ?></h1><hr>

     <p>
        <?= Html::a('<i class="fa fa-fw fa-edit"></i>'.' '.Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-lg']) ?>
        
        <?= Html::a(Yii::t('app', 'Change Status'), ['state', 'id' => $model->id], ['class' => 'btn btn-success btn-lg']) ?>
       
        <?= Html::a('<i class="fa fa-fw fa-window-close"></i>'.' '.Yii::t('app', 'Cancel'),Yii::$app->request->referrer, ['class'=>'btn btn-danger btn-lg']) ?>
    </p><br>

    <?php
    if ($model->status == 1) {
        echo Alert::widget([
            'type' => Alert::TYPE_DANGER,
            'icon' => 'fa fa-info-circle',
            'title' => 'Note',
            'titleOptions' => ['icon' => 'fa fa-info-circle'],
            'body' => Yii::t('app', 'this item is unActive?'),
            'showSeparator' => true,
        ]);
    }
    ?>

    <div class="row">
        <div class="col-md-8">
        <html><div id="showBarcode"></div></html> <!--This element id should be passed on to options-->

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'class',
            'unit',
            'box',
            'cost',
            'price',
            'quantity',
            'minimum',
            [ 
                'label' => Yii::t('app', 'Q Show'),
                'format' => 'raw',
                   'value'=>function($searchModel) { 
    
                    if($searchModel->qShow ==1){
                        return 'نعم';
                    }elseif($searchModel->qShow ==0){
                        return 'لا';
                    }
                }
            ],
            [ 
                'label' => Yii::t('app', 'Ending'),
                'format' => 'raw',
                   'value'=>function($searchModel) { 
    
                    if($searchModel->ending ==1){
                        return 'نعم';
                    }elseif($searchModel->ending ==0){
                        return 'لا';
                    }
                }
            ],
            [ 
                'label' => Yii::t('app', 'Status'),
                'format' => 'raw',
                   'value'=>function($searchModel) { 
    
                    if($searchModel->status ==1){
                        return 'موقوف';
                    }elseif($searchModel->status ==0){
                        return 'نشط';
                    }
                }
            ],
            [
                'attribute' => 'commCode',
                'format' => 'raw',
            ],
            [
                'label' => Yii::t('app', 'User Insert'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->userInsert->username;
                }
            ],
            'created_at',

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
    </div>
    <div class="col-md-4">
     <img src=<?php echo $model->path?>  class ="img-prev" alt="Cinque Terre">
    </div>
    </div>
   <div id="showBarcode"><!--This element id should be passed on to options-->
     <?php
        $optionsArray = array(
        'elementId'=> 'showBarcode', /* div or canvas id*/
        'value'=> $model->serialNo, /* value for EAN 13 be careful to set right values for each barcode type */
        'type'=>'code39',/*supported types  ean8, ean13, upc, std25, int25, code11, code39, code93, code128, codabar, msi, datamatrix*/
        );
        echo BarcodeGenerator::widget($optionsArray);
    ?>
    </div>

</div>
