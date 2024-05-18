<?php

use kartik\field\FieldRange;
use kartik\widgets\Spinner;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model app\models\PricesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="prices-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class=row>
        <div class="col-md-6">
            <?php
            $formatJs = <<< 'JS'
   var formatProduct = function (product) {
   if (product.loading) {
    return product.text;
   }
 var markup =
    '<div class="row">' + 
      '<div class="col-sm-6">' +
        '<b style="margin-center:5px">' + product.text + '</b>' + 
      '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill">  سعر البيع - </i> ' + product.maxPrice + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> سعر البيع الأدنى - </i> ' + product.minPrice + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> مكان الصنف - </i> ' + product.place + '</div>' +
      '</div>' +
      '<br>'+
      '<div class="row">' + 
      '<div class="col-sm-3"><i class="badge badge-primary badge-pill"> رقم القطعة 1 - </i> ' + product.serialNo + '</div>' +
      '<div class="col-sm-3"><i class="badge badge-primary badge-pill"> رقم القطعة 2 - </i> ' + product.commCode + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم التسلسل - </i> '  + product.id + '</div>' +
      //'<div class="col-sm-2"><i class="badge badge-primary badge-pill">حالة القطعة</i> ' +product.type+ '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> الشركة - </i> ' + product.company + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> الكمية = </i> ' + '  ' + product.quantity + '</div>' +
    '</div>';
    return '<div style="overflow:hidden;">' + markup + '</div>';
  };                
    

  var formatProductSelection = function (product) {
    return product.name || product.text;
  }
JS;

            // Register the formatting script
            $this->registerJs($formatJs, $this::POS_HEAD);

            // script to parse the results into the format expected by Select2
            $resultsJs = <<< JS
    function (data, params) {
       params.page = params.page || 5;
        return {
            // Change `data.items` to `data.results`.
            // `results` is the key that you have been selected on
            // `actionJsonlist`.
            results: data.results
        };
    } 
JS;
            //var salesId= getElementById("sales-id").value;
            echo $form->field($model, 'category')->widget(Select2::classname(), [
                'name' => 'kv-repo-template',
                'id' => 'focus_first',
                'language' => 'en',
                'options' => [
                    'placeholder' => Yii::t('app', 'Search...'),
                    'dir' => 'rtl',
                    'multiple' => false,
                ],
                'pluginOptions' => [
                    'autofocus' => true,
                    'minimumInputLength' => 1,
                    'ajax' => [
                        'url' => Url::to(['/except/itemlist']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term, page: params.page}; }'),
                        'processResults' => new JsExpression($resultsJs),
                        'cache' => true
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('formatProduct'),
                    'templateSelection' => new JsExpression('formatProductSelection'),
                ],
            ]);
            ?>

            <?= $form->field($model, 'serialNo') ?>

            <?= $form->field($model, 'costPrice') ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'maxPrice') ?>

            <?php
            // echo FieldRange::widget([
            //     'form' => $form,
            //     'model' => $model,
            //     'label' => 'Enter amount range',
            //     'attribute1' => 'minPrice',
            //     'attribute2' => 'maxPrice',
            //     'type' => FieldRange::INPUT_SPIN,
            // ]);
            ?>

            <?= $form->field($model, 'minPrice') ?>

            <?= $form->field($model, 'minPrice2') ?>


        </div>
        <div class="col-md-2"><?= $form->field($model, 'lowPrice') ?></div>
        <div class="col-md-2"><?= $form->field($model, 'bigPrice') ?></div>
    </div>

    <?php // echo $form->field($model, 'minPrice3') 
    ?>

    <?php // echo $form->field($model, 'maxPrice') 
    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>