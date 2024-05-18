<?php

use app\models\Category;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\CategorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="row">
        <div class="col-lg-3">

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
      '<div class="col-sm-6"><i class="badge badge-primary badge-pill"> رقم القطعة 1 - </i> ' + product.serialNo + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم القطعة 2 - </i> ' + product.commCode + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم التسلسل - </i> '  + product.id + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> الشركة - </i> ' + product.company + '</div>' +
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
            echo $form->field($model, 'id')->widget(Select2::classname(), [
                'name' => 'kv-repo-template',
                // 'id' => 'tempinvoicepurchase-category',
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

            <?php //$form->field($model, 'class') ?>
        </div>

        <div class="col-lg-3">
            <?= $form->field($model, 'serialNo') ?>

            <?= $form->field($model, 'commCode') ?>
        </div>

        <div class="col-lg-3">
            <?= $form->field($model, 'status')->dropDownList(['0' => 'نشط', '1' => 'موقوف'], ['prompt' => 'اختيار حالة الصنف...']) ?>

            <?= $form->field($model, 'place') ?>
        </div>

        <div class="col-lg-3">
            <?php
            $data = Category::find()->select(['class'])->distinct()->all();
            $listData = ArrayHelper::map($data, 'class', 'class');

            echo $form->field($model, 'class')->dropDownList($listData, ['prompt' => 'اختيار حالة الصنف...']) ?>

            <?= $form->field($model, 'company') ?>
        </div>

        <br>
    </div>
     <div class="row">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
            <div class="form-group">
                <?= Html::a('<i class="fa fa-fw fa-plus"></i>' . ' ' . Yii::t('app', 'New Create'), ['create'], ['class' => 'btn btn-success btn-lg']) ?>
                <?= Html::submitButton('<i class="fa fa-fw fa-search"></i>' . ' ' . Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-lg']) ?>
                <?= Html::a('<i class="fa fa-fw fa-eraser"></i>' . ' ' . Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger btn-lg']) ?>
                <?= Html::a('<i class="fa fa-fw fa-upload"></i>' . ' ' . Yii::t('app', "upload"), Url::toRoute(['upload']), ['class' => 'btn btn-info btn-lg']) ?>

            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>


</div>