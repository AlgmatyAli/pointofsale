<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TempReorder */

$this->title = Yii::t('app', 'Create Temp Reorder');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Temp Reorders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="temp-reorder-create">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>

</div>
