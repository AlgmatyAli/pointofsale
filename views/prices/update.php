<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Prices */

$this->title = Yii::t('app', 'تعديل سعر صنف').' -' .$model->category0->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Prices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="prices-update">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
