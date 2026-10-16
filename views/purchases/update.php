<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\PurchasesSearch $searchModel */
/** @var yii\widgets\ActiveForm $form */
/** @var app\models\Purchases $model */

$this->title = Yii::t('app', 'Update Purchases:') . ' ' . $model->c->name;
?>
<div class="purchases-update">

    <?= $this->render('_form_update', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'totalCost' =>  $model->totalCost,
    ]) ?>

</div>