<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Safe */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Saves'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="safe-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-fw fa-edit"></i>'.' '.Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-trash"></i>'.' '.Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-warning btn-lg',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        
        <?= Html::a('<i class="fa fa-fw fa-window-close"></i>'.' '.Yii::t('app', 'Back'),Yii::$app->request->referrer, ['class'=>'btn btn-danger btn-lg']) ?>

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
            [ 
                'label' => Yii::t('app', 'ٍSafe No.'),
                'format' => 'raw',
                   'value'=>function($searchModel) { 
    
                    if($searchModel->safeNo ==1){
                        return 'خزينة رقم 1';
                    }elseif($searchModel->safeNo ==2){
                        return 'خزينة رقم 2';
                    }
                }
            ],
            'value',
            'at',
            [ 
                'label' => Yii::t('app', 'Type'),
                'format' => 'raw',
                   'value'=>function($searchModel) { 
    
                    if($searchModel->type ==1){
                        return 'صادر';
                    }elseif($searchModel->type ==2){
                        return 'وارد';
                    }
                }
            ],
            'why',
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
