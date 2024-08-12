<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\TempInvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\ActiveForm;

?>
<div class="temp-invoice-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

   <?php 
   echo GridView::widget([
     'summary'=>'',
     'dataProvider' => $dataProvider,
     'columns' => [
        ['class' => 'yii\grid\CheckboxColumn'],
            ['class' => 'yii\grid\SerialColumn'],
            'category0.name',
            'category0.serialNo',
            'category0.commCode',
            'category0.company',
            'quantity',
      ],]);
   ?> 

<div class="form-group">
      
<?= Html::submitButton('<i class="fa fa-fw fa-save"></i>'.' '.Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-lg']) ?>


<?= Html::a('<i class="fa fa-fw fa-window-close"></i>'.' '.Yii::t('app', 'Cancel'),Yii::$app->request->referrer, ['class'=>'btn btn-danger btn-lg']) ?>

    <?php ActiveForm::end(); ?>
</div>
