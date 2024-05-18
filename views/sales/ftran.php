<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sales */

$this->title = Yii::t('app', 'Ftran Report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-create">
<div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-4">
    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('_ftran', [
        'model' => $model,
    ]) ?>

    </div>
</div>  

</div>
