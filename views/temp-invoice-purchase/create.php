<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TempInvoicePurchase */

$this->title = Yii::t('app', 'Create Temp Invoice Purchase');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Temp Invoice Purchases'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="temp-invoice-purchase-create">

    <!-- <h1><?= Html::encode($this->title) ?></h1><hr> -->

    <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'totalCost' => null,
        'totalInvoice' => null,
        'rate' => null,
        'derhamRate' => null
    ]) ?>

</div>
