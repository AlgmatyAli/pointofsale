<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model app\models\AuthItem */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Auth Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="auth-item-view">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->name], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->name], [
            'class' => 'btn btn-danger',
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
            'name',
            'type',
            // 'description:ntext',
            // 'rule_name',
            // 'data',
            // 'created_at',
            // 'updated_at',
        ],
    ]) ?>

<hr> 
    <div class="row">
  <div class="col-sm-1 col-md-1 col-lg-1" ></div>
   <div class="col-sm-8 col-md-8 col-lg-8" >
      <h3>الصلاحيات</h3><hr>
    <?= GridView::widget([
        'dataProvider' => $authItemChildProvider,
       // 'filterModel' => $searchModel,
        'summary'=>'',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'child0.description',
        ],
    ]); ?>
</div>
</div>
