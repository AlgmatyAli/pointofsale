<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\LoanPaid */

$this->title = Yii::t('app', 'Update Loan Paid:').$model->employee0->name;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Loan Paids'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="loan-paid-update">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
