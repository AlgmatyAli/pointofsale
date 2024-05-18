<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TempBackSales */

$this->title = Yii::t('app', 'Create Temp Back Sales');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Temp Back Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="temp-back-sales-create">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>

</div>
