<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Url;
use app\models\Category;
use app\models\Client;
use app\models\Inventory;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model app\models\ReceiptSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="histrans">

    <?php $form = ActiveForm::begin(); ?>
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
    //   '<div class="col-sm-2"><i class="badge badge-primary badge-pill">  سعر البيع - </i> ' + product.maxPrice + '</div>' +
    //   '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> سعر البيع الأدنى - </i> ' + product.minPrice + '</div>' +
    //   '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> مكان الصنف - </i> ' + product.place + '</div>' +
      '</div>' +
      '<br>'+
      '<div class="row">' + 
      '<div class="col-sm-3"><i class="badge badge-primary badge-pill"> 1 رقم القطعة - </i> ' + product.serialNo + '</div>' +
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
    echo $form->field($model, 'id')->widget(Select2::class, [
        'name' => 'kv-repo-template',
        'id' => 'focus_first',
        'language' => 'en',
        'options' => [
            'placeholder' => Yii::t('app', 'Search...'),
            'dir' => 'rtl',
            'multiple' => true,
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
    
    <?php
    echo $form->field($model, 'client')->widget(Select2::class, [
        'data' =>  ArrayHelper::map(Client::find()
            ->where(['in', 'type', [0, 2]])
            //->andWhere(['branch' => Yii::$app->user->identity->branch])                       
            ->orderBy('id')->asArray()->all(), 'id', 'name'),
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);

    ?>
    <?php
    //  echo $form->field($model, 'id')->widget(\kartik\widgets\Select2::class, [
    //    'id' => 'categoryx',
    //    'data' => \yii\helpers\ArrayHelper::map($data, 'id', 
    //     function($model) {
    //         return $model['name'].' --  '.$model['serialNo'];
    //     }
    // ),

    //     'language' => 'en',
    //     'options' => ['placeholder' => Yii::t('app', 'Choose Category'),
    //     'dir' => 'rtl',
    //     'multiple'=>true,
    //     ],
    //     'pluginOptions' => [
    //         'allowClear' => true 
    //     ],

    // ]); 
    ?>

    <?=
    $form->field($model, 'min_date')->widget(
        DatePicker::class,
        [
            'language' => 'ar',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'todayBtn' => true,
            ]
        ]
    )->label(Yii::t('app', 'Min Date'));
    ?>
    <?=
    $form->field($model, 'max_date')->widget(
        DatePicker::class,
        [
            'language' => 'ar',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'todayBtn' => true,
            ]
        ]
    )->label(Yii::t('app', 'Max Date'));;
    ?>
    <?= $form->field($model, 'allData')->checkBox(['checked' => false]) ?>
    <br>
    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Run'), ['class' => 'btn btn-success btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-eraser"></i>' . ' ' . "Erase", Url::toRoute(['index']), ['class' => 'btn btn-danger btn-lg']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>