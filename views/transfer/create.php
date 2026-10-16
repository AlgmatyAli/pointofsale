<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Transfer */

$this->title = Yii::t('app', 'Create Transfer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Transfers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transfer-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>