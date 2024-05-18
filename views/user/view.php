<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

 $this->title = $model->username;
// $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <br>
    <h1><?= Html::encode($this->title) ?></h1><hr><br>
    <br>
    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-lg']) ?>
        <!-- <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?> -->
    </p>
    <hr><br>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
           // 'password',
            'phone',
            'email',
            'createedDate',
            'isActive',
            //'permission'
        ],
    ]) ?>

</div>
