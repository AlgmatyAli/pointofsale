<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Safe */

$this->title = Yii::t('app', 'Create Safe');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Saves'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="safe-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>