<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Purchases */

$this->title = Yii::t('app', 'Update Back Purchases: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Purchases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="back-purchases-update">

    <!-- <h1><?= Html::encode($this->title) ?></h1><hr> -->

    <?= $this->render('backPurchases', [
        'model' => $model,
        'models' => $models,
        'nets'=>$net,
    ]) ?>

</div>
