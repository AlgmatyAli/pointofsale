<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sales */

$this->title = Yii::t('app', 'Create Client Histrans');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="histrans-create">
<div class="row">
    <div class="col-lg-4"></div>
    <div class="col-lg-4">
    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('_histrans', [
        'model' => $model,
    ]) ?>

    </div>
    <div class="col-lg-4">

    </div>
    </div>

</div>
