<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Stocks */

$this->title = Yii::t('app', 'Create Stocks');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stocks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stocks-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
