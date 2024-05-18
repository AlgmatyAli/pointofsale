<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sales */

$this->title = Yii::t('app', 'Update Back Sales').' '.$model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Back Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="sales-back-update">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('backSale', [
       'model' => $model,
       'models' => $models,
       'net'=>$net,
       'data'=>$data
    ]) ?>

</div>
