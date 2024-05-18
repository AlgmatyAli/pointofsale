<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sales */

$this->title = Yii::t('app', 'Create Back Sales');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-back-create">

    <!-- <h1><?= Html::encode($this->title) ?></h1><hr> -->
    <hr>

    <?= $this->render('backSale', [
        'model' => $model,
        'models' => $models,
        'data' =>$data
    ]) ?>

</div>
