<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\DisscountClients $model */

$this->title = Yii::t('app', 'Create Disscount Suppliers');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Disscount Suppliers'), 'url' => ['index_']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="disscount-Suppliers-create">

    <h4 class="mb-3"><i class="bi bi-plus-circle"></i> <?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
