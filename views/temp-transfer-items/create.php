<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TempTransferItems */

$this->title = Yii::t('app', 'Create Temp Transfer Items');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Temp Transfer Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="temp-transfer-items-create">

    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>

</div>