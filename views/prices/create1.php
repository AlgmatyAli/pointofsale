<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Prices */

$this->title = Yii::t('app', 'Create Prices Percentage of increase');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Prices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prices-create">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('_form1', [
        'model' => $model,
    ]) ?>

</div>
