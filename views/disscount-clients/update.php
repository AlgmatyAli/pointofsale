<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\DisscountClients $model */

$this->title = Yii::t('app', 'Update Disscount Clients: {name}', [
    'name' => $model->id,
]);
?>
<div class="disscount-clients-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>

    <?php
    if ($model->client0->type == 0 || $model->client0->type == 2) {
        echo $this->render('_form_client', ['model' => $model,]);
    } else {
        echo $this->render('_form', ['model' => $model,]);
    }
    ?>

</div>