<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Safe */

$this->title = Yii::t('app', 'Create Safe');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Saves'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="safe-create">
    <div class="row">
    <div class="col-lg-3"></div>
    <div class="col-lg-5">
    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
    </div>
    </div>
</div>
