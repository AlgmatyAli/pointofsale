<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\EmpSalary */

$this->title = Yii::t('app', 'Discount Emp Salary');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Emp Salaries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="emp-salary-cdiscount">

    <h1><?= Html::encode($this->title) ?></h1><hr>

    <?= $this->render('discount', [
        'model' => $model,
    ]) ?>

</div>
