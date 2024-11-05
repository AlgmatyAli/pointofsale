<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Branches;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model app\models\InventorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-stocks-search">
<?php $form = ActiveForm::begin([
        'action' => ['price-list'],
        'method' => 'get',
    ]); ?>
    <div class='row'>
    <div class='col-md-6'>
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
    'options' => ['placeholder' => Yii::t('app','Search...'), 
    'dir' => 'rtl',
    'multiple'=>false,
    ],
        'pluginOptions' => [
        'autofocus' =>true,
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
    <?php  echo $form->field($model, 'costPrice')->textInput(['maxlength' => true, 'placeholder' => 'Unit'])  ?>
    <?= $form->field($model, 'company')->textInput(['placeholder' => 'quantity']) ?>

    </div>
    <div class='col-md-6'>
    <?php  echo $form->field($model, 'class')->textInput(['maxlength' => true, 'placeholder' => 'Class']) ?>

        <?php  
                echo $form->field($model, 'branch')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Branches::find()->all(),'id', 'name'),
                'language' => 'ar',
                'options' => ['placeholder' => 'الرجاء اختيار اسم الفرع ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple'=>false
                ],
             ]);
            ?>
    <?php  echo $form->field($model, 'serialNo')->textInput(['maxlength' => true, 'placeholder' => 'Serial No'])  ?>

    </div>
    </div>
    <div class='row'>
    <div class='col-md-6'>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-md']) ?>
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>'.' '.Yii::t('app', "Erase"), Url::toRoute(['price-list']), ['class' => 'btn btn-danger btn-md']) ?>
    </div>
    </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
