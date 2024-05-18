<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\TransferItems */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Transfer Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="transfer-items-view">

    <h1><?= Html::encode($this->title) ?></h1><br>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php
        echo Html::a('<i class="fa fa-fw fa-trash"></i>'.' '.Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-warning btn-sm',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) 
        ?>

        <?= Html::a('<i class="fa fa-fw fa-print"></i>'.' '.Yii::t('app', 'Print'), ['print', 'id' => $model->id], ['class' => 'btn btn-info btn-sm']) ?>

    </p><hr>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => Yii::t('app', 'من الفرع'),
                'value' => function ($data) {
                    return $data->fromBranch0->name;
                }
    
            ],
            [
                'label' => Yii::t('app', 'الى الفرع'),
                'value' => function ($data) {
                    return $data->toBranch0->name;
                }
    
            ],
            'at',
            'created_by',
            'userInsert.username',
            'created_at',
            'userUpdate.username',
            'update_at',
        ],
    ]) ?>

</div>
