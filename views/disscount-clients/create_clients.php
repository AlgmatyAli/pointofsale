<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\DisscountClients $model */

$this->title = Yii::t('app', 'Create Disscount Clients');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Disscount Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="disscount-clients-create">

    <h4 class="mb-3"><i class="bi bi-plus-circle"></i> <?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form_client', [
        'model' => $model,
    ]) ?>

</div>
