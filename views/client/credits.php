<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sales */

$this->title = Yii::t('app', 'Create Depts');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="credt-create">
<div class="row">
    <div class="col-lg-4"></div>
    <div class="col-lg-4">
    <h1><?= Html::encode($this->title) ?></h1><hr>
    <hr>

    <?= $this->render('_allCredit', [
        'model' => $model,
    ]) ?>

</div>
    <div class="col-lg-4">

    </div>
    </div>
</div>
