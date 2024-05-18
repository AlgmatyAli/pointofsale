<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reorderitems */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Reorderitems',
]) . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Reorderitems'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', ]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="reorderitems-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
