<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Arrangement */

$this->title = Yii::t('app', 'Create Arrangement');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Arrangements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="arrangement-create">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
