<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Purchases */

$this->title = Yii::t('app', 'Create Purchases');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Purchases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchases-create">

    <!-- <h1><?= Html::encode($this->title) ?></h1><hr><hr> -->
    <hr>

    <?= $this->render('_form', [
        'model' => $model,
        //'models' => $models,
    ]) ?>
   

</div>
