<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\DisscountClients $model */

$this->title = Yii::t('app', 'Create Disscount Suppliers');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Disscount Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="disscount-Suppliers-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>