<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sales */

$this->title = Yii::t('app', 'Profit Report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profit-create">
<div class="row">
      <div class="col-md-4"></div>
      <div class="col-md-4">
    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('profitForm', [
        'model' => $model,
    ]) ?>

    </div>
</div>  

</div>
