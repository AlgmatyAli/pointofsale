<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Reorder */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Reorders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="reorder-view">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-lg']) ?>

        <?= Html::a('<i class="fa fa-fw fa-print"></i>'.' '.Yii::t('app', 'Print'), ['print', 'id' => $model->id], ['class' => 'btn btn-success btn-lg']) ?>


        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-lg',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <br><br>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'at',
            [
                'label' => Yii::t('app', 'Branch'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->branch0->name;
                }
            ],
            [
                'label' => Yii::t('app', 'User Insert'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->createdBy->username;
                }
            ],
            'created_at',
        ],
    ]) ?>

</div>
