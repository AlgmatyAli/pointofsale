<?php

use yii\helpers\Html;

/** @var app\models\ReceiptSearch $model */
/** @var yii\widgets\ActiveForm $form */

$this->title = Yii::t('app', 'Create Receipt');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Receipts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="receipt-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>