<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Arrangement */

$this->title = Yii::t('app', 'Update Arrangement: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Arrangements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="arrangement-update">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('_form_update', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>

</div>
