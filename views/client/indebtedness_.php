<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/** @var app\models\Client $model */

$this->title = Yii::t('app', 'Create Indebtedness');
?>
<div class="credt-create">

    <h4 class="mb-3"><i class="bi bi-cash-coin"></i> <?= Html::encode($this->title) ?></h4>

    <div class="row justify-content-center">
        <div class="col-xl-4 col-lg-6 col-md-8">
            <?= $this->render('indebtedness', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
