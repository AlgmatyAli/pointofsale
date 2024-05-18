<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\LoanPaid */

$this->title = Yii::t('app', 'تسجيل دفعة سلفة');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Loan Paids'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-paid-create">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
