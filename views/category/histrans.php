<?php

use yii\helpers\Html;

/** @var  app\models\ReceiptSearch $model*/

$this->title = Yii::t('app', 'Create Category Histrans');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="histrans-create">

    <?= $this->render('_histrans', [
        'model' => $model,
        'data' => $data
    ]) ?>
</div>