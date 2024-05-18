<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TempArrangement */

$this->title = Yii::t('app', 'Create Temp Arrangement');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Temp Arrangement'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="temp-arrangement-create">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>

</div>
