<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\DisscountClients $model */

$this->title = $model->client0->name . ' - ' . $model->notes;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Disscount Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="disscount-clients-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>

    <p>
        <?= Html::a('<i class="fa fa-fw fa-edit"></i>' . ' ' . Yii::t('app', 'Update'), ['update', 'id' => $model->id, 'type' => $model->type], ['class' => 'btn btn-primary']) ?>

        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Back'), Yii::$app->request->referrer, ['class' => 'btn btn-danger']) ?>

    </p>
    <br>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => Yii::t('app', 'Client Name'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->client0->name;
                }
            ],
            'at',
            'value',
            [
                'label' => Yii::t('app', 'Currancy'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->currancy0->name;
                }
            ],
            [
                'label' => Yii::t('app', 'Branch'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->branch0->name;
                }
            ],
            [
                'label' => Yii::t('app', 'Notes'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->notes;
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