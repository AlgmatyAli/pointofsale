<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sales */

$this->title = Yii::t('app', 'Create Category Histrans');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="histrans-create">
<div class="row">
    <div class="col-lg-2"></div>
    <div class="col-lg-8">
    <h1><?= Html::encode($this->title) ?></h1><hr>
    <hr>

    <?= $this->render('_histransClient', [
        'model' => $model,
        'data' => $data
    ]) ?>
        </div>
    <div class="col-lg-2">

    </div>
    </div>

</div>
