<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Purchases */

$this->title = Yii::t('app', 'Update Purchases:').' ' .$model->c->name ;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Purchases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="purchases-update">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('_form_update', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'totalCost' =>  $model->totalCost,
    ]) ?>

</div>
