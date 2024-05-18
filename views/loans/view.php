<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Loans */

$this->title = $model->employee0->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Loans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="loans-view">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <p> 
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Change Status'), ['state', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <br>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'employee0.name',
            'loanValue',
            'kestValue',
            'at',
            'parts',
            'paid',
            'notes',
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
            'createdBy.username',
            'created_at',
            'updatedBy.username',
            'updated_at',
        ],
    ]) ?>

</div>
