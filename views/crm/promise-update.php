<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\CrmPromise $model */

$this->title = 'تعديل وعد الدفع: ' . ($model->client->name ?? '');
$this->params['breadcrumbs'][] = ['label' => 'وعود الدفع', 'url' => ['promises']];
$this->params['breadcrumbs'][] = 'تعديل';
?>
<div class="crm-promise-update" style="max-width: 560px">
    <?= $this->render('_nav') ?>
    <h1 class="h4"><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'amount')->input('number', ['step' => '0.001', 'min' => '0.001']) ?>
    <?= $form->field($model, 'promiseDate')->input('date') ?>
    <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>
    <?= Html::submitButton('حفظ الوعد', ['class' => 'btn btn-primary']) ?>
    <?= Html::a('رجوع', ['promises'], ['class' => 'btn btn-link']) ?>
    <?php ActiveForm::end(); ?>
</div>
