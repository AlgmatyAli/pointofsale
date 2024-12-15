<?php

use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PurchasesDetailsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-purchases-details-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>
    <div class="row">

        <div class="col-md-3">
            <?php
            echo '<label class="control-label">تاريخ الفاتورة</label>';
            echo DateRangePicker::widget([
                'model' => $model,
                'attribute' => 'at',
                'language' => 'en',
                'convertFormat' => false,
                'pluginOptions' => [
                    'timePicker' => false,
                    'timePickerIncrement' => 30,
                    'locale' => [
                        'format' => 'YYYY-MM-DD'
                    ]
                ]
            ]); ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'type')->dropDownList(['1' => 'فاتورة مشتريات', '2' => 'فاتورة مسترجع مشتريات', '3' => 'فاتورة مشتريات معلقة'], ['prompt' => 'نوع الحركـة']) ?>
        </div>
        <div class="col-md-6">
    <?php
            $formatJs = <<< 'JS'
                var formatProduct = function (product)
            {
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
            echo $form->field($model, 'category')->widget(Select2::class, [
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
        </div>
    </div>
    <br>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Reset'), ['/purchases-details'], ['class' => 'btn btn-danger']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>