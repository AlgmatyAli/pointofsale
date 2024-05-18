<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Expenses */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Expenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="expenses-view">

    <br><h1><?= Html::encode($this->title) ?></h1><br>

    <p>
        <?= Html::a('<i class="fa fa-fw fa-edit"></i>'.' '.Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-trash"></i>'.' '.Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-warning btn-lg',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        
        <?= Html::a('<i class="fa fa-fw fa-print"></i>'.' '.Yii::t('app', 'Print Reciept'), ['print-recipt', 'id' => $model->id], ['class' => 'btn btn-success btn-lg']) ?>

        <?= Html::a('<i class="fa fa-fw fa-window-close"></i>'.' '.Yii::t('app', 'Back'),Yii::$app->request->referrer, ['class'=>'btn btn-danger btn-lg']) ?>

    </p><br>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'expenseTo',
            'at',
            [
                'label' => Yii::t('app', 'Name'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->item->name;
                }
            ],
            'value',
            'outBox',
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
