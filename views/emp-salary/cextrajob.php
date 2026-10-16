<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\EmpSalary */

$this->title = Yii::t('app', 'Emp Extra Job');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Emp Salaries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="emp-salary-extrajob">

    <?= $this->render('extrajob', [
        'model' => $model,
    ]) ?>

</div>