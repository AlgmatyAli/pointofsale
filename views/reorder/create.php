<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reorder */

$this->title = Yii::t('app', 'Create Reorder');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Reorders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reorder-create">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
