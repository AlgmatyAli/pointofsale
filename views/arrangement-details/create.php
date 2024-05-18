<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ArrangementDetails */

$this->title = Yii::t('app', 'Create Arrangement Details');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Arrangement Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="arrangement-details-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
