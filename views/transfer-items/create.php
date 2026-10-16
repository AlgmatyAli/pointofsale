<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TransferItems */

$this->title = Yii::t('app', 'Create Transfer Items');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Transfer Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transfer-items-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>