<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Branches */

$this->title = Yii::t('app', 'Create Branches');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Branches'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="branches-create">

    <div class="row">
    <div class="col-lg-3"></div>
    <div class="col-lg-6">
    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
   </div>
   </div>

</div>
