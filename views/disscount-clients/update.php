<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\DisscountClients $model */

// نفس القاعدة القديمة: عميل (0 أو 2) => نموذج العملاء، غير ذلك => نموذج الموردين
$isClient = in_array((int) $model->client0?->type, [0, 2], true);

$this->title = Yii::t('app', 'Update Disscount Clients: {name}', [
    'name' => $model->client0?->name ?? $model->id,
]);
$this->params['breadcrumbs'][] = $isClient
    ? ['label' => Yii::t('app', 'Disscount Clients'), 'url' => ['index']]
    : ['label' => Yii::t('app', 'Disscount Suppliers'), 'url' => ['index_']];
$this->params['breadcrumbs'][] = ['label' => $model->client0?->name ?? $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="disscount-clients-update">

    <h4 class="mb-3"><i class="bi bi-pencil-square"></i> <?= Html::encode($this->title) ?></h4>

    <?= $this->render($isClient ? '_form_client' : '_form', ['model' => $model]) ?>

</div>
