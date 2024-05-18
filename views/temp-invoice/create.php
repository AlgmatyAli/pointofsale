<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TempInvoice */

$this->title = Yii::t('app', 'Create Temp Invoice');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Temp Invoice'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="temp-invoice-create">

    <h3><?php echo Html::encode($this->title) ?></h3><hr>

    <?= $this->render('_form', [
    // <?= $this->render('_form', [
        'model' => $model,
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'company' => $company,
        //'data'=>$data
        // 'category'=>$category,
        // 'client'=>$client
    ]) ?>

</div>
