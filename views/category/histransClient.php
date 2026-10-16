<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sales */

$this->title = Yii::t('app', 'Create Category Histrans');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="histrans-create">

    <?= $this->render('_histransClient', [
        'model' => $model,
        'data' => $data
    ]) ?>

</div>