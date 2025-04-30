<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Currancy $model */

$this->title = Yii::t('app', 'Create Currancy');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Currancies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currancy-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
