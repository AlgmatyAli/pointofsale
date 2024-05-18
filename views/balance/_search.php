<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\base\Currancy;
use app\models\Client;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\BalanceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-balance-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        

        <div class="col-md-4">

            <?= $form->field($model, 'clinet')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Client::find()
                    ->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => ['placeholder' => 'الرجاء اختيار اسم العميل ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false
                ],
            ]);
            ?>

        </div>

        <div class="col-md-4">
            <?php
            echo $form->field($model, 'currancy')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Currancy::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => ['placeholder' => '...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false
                ],
            ]);
            ?>
        </div>

    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-search"></i>' . ' ' . Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>' . ' ' . Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger btn-lg']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>