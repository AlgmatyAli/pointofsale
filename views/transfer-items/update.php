<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TransferItems */

$this->title = Yii::t('app', 'Update Transfer Items:').$model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Transfer Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="transfer-items-update">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('_form_update', [
        'model' => $model,
        'dataProvider' => $dataProvider,
    ]) ?>

</div>
