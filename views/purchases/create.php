<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\PurchasesSearch $searchModel */
/** @var yii\widgets\ActiveForm $form */
/** @var app\models\Purchases $model */

$this->title = Yii::t('app', 'Create Purchases');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Purchases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="purchases-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>