<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

/** @var app\models\Client $model */

$this->title = Yii::t('app', 'Create Indebtedness');

?>
<div class="credt-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <hr>
    <div class="row">
        <div class="col-lg-4">

            <?= $this->render('indebtedness', [
                'model' => $model,
            ]) ?>

        </div>

    </div>
</div>