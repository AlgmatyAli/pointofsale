<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

/** @var  app\models\Employee $model*/

$this->title = Yii::t('app', 'Create Employee');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Employees'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-create">
    <div class="row">
        <div class="col-lg-1"></div>
        <div class="col-lg-10">
            <h1><?php //Html::encode($this->title) 
                ?></h1>
            <hr>

            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>