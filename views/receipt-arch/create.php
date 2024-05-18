<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ReceiptArch */

$this->title = Yii::t('app', 'Create Receipt Arch');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Receipt Arches'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="receipt-arch-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
