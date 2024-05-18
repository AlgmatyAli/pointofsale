<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TransferItems */

$this->title = Yii::t('app', 'Create Transfer Items');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Transfer Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transfer-items-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
