<?php

use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\TempInvoice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="temp-invoice-form">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between">
                <div class="btn-group btn-group-md">
                    <?php echo Html::button(
                        '<i class="fa fa-cart"></i>' . ' ' . Yii::t('app', 'Create Category'),
                        ['value' => Url::to(['category/create-category']), 'class' => 'btn btn-primary popup mr-2']
                    ); ?>

                    <?php echo Html::button(
                        '<i class="fa fa-fw fa-step-forward"></i>' . ' ' . Yii::t('app', 'Complete Sale'),
                        ['value' => Url::to(['sales/create']), 'class' => 'btn btn-danger popup mr-2']
                    ); ?>

                    <?= Html::a(
                        '<i class="fa fa-fw fa-pause"></i>' . Yii::t('app', 'Hold'),
                        ['hold'],
                        ['class' => 'btn btn-success mr-2']
                    ) ?>

                    <?php echo Html::button(
                        '<i class="fa fa-fw fa-cloud-upload"></i>' . ' ' . Yii::t('app', 'Holded Invoices'),
                        ['value' => Url::to(['holded']), 'class' => 'btn btn-warning popup mr-2']
                    ); ?>

                    <?php echo Html::button(
                        '<i class="fa fa-fw fa-fast"></i>' . ' ' . Yii::t('app', 'Adedd Fast'),
                        ['value' => Url::to(['fast']), 'class' => 'btn btn-info popup']
                    ); ?>
                </div>

                <?= Html::a(
                    '<i class="fa fa-fw fa-trash"></i>' . ' ' . Yii::t('app', 'Delete All'),
                    ['delete-all'],
                    [
                        'class' => 'btn btn-warning pull-left',
                        'data' => [
                            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                    ]
                ) ?>
            </div>
        </div>
    </div>
    <br>
    <!-- <div style="font-size:26px; color:red">عدد الأصناف:( <span id="submit-counter"> 0 </span> )</div> -->
    <!-- <hr> -->
    <div class="row info-boxes mb-4">
        <?php Pjax::begin(['id' => 'pjax-grid-view']); ?>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-aqua"><i class="fa fa-fw fa-dollar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?= Yii::t('app', 'Items Count') ?></span>
                    <span class="info-box-number"><?php echo $dataProvider->getCount() ?></span>
                </div>
            </div>
        </div>

        <?php $count = 0;
        foreach ($dataProvider->getModels() as $dataP) {
            $count = $dataP->quantity + $count;
        }

        //if ($count != 0) { 
        ?>

        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-green"><i class="fa fa-fw fa-dollar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?= Yii::t('app', 'items quantity') ?></span>
                    <span class="info-box-number"><?= $count ?></span>
                </div>
            </div>
        </div>


        <?php $count = 0;
        $total = 0;
        $profit = 0;
        foreach ($dataProvider->getModels() as $sum) {

            $count = $sum->quantity * $sum->salePrice;
            $countProfit = ($sum->quantity * $sum->salePrice) - ($sum->quantity * $sum->costPrice);
            $total = $total + $count;
            $profit = $profit + $countProfit;
        }

        //if ($count != 0) { 
        ?>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-yellow"><i class="fa fa-fw fa-dollar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?= Yii::t('app', 'Total Invoice') ?></span>
                    <span class="info-box-number">
                        <?= @number_format($total, 3) ?>
                        <?php if (Yii::$app->user->identity->seeCostPrice == 1) : ?>
                            <br><small class="text-muted"><?= @number_format($profit, 3) ?></small>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
        <?php Pjax::end(); ?>
    </div>
    <?php $form = ActiveForm::begin([
        'id' => 'temp-invoice-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'options' => ['class' => 'search-form mb-4']
    ]) ?>
    <div class="row">
        <div class="col-md-12">
            <?php
            if ($company->criteriaـvalue <> 0) {
                $formatJs = <<< 'JS'
            var formatProduct = function (product) {
            if (product.loading) {
                return product.text;
            }
         let maxPrice = parseFloat(product.maxPrice);
         maxPrice = Math.round(maxPrice);
         let diff
         let len = maxPrice.toString().length;
         let value = maxPrice.toString().substr(len-1, len-1);
         if(parseInt(value) < 5 && parseInt(value) != 0){
          diff = 5 - parseInt(value);
          maxPrice += diff;
         }
         if (parseInt(value) > 5) {
          diff = 10 - parseInt(value);
          maxPrice += diff;
         }
         let minPrice = parseFloat(product.minPrice);
         minPrice = Math.round(minPrice);
         let mDiff
         let mLen = minPrice.toString().length;

         let mValue = minPrice.toString().substr(mLen-1, mLen-1);
         if(parseInt(mValue) < 5 && parseInt(mValue) != 0){
            mDiff = 5 - parseInt(mValue);
          minPrice += mDiff;
         }
         if (parseInt(mValue) > 5) {
            mDiff = 10 - parseInt(mValue);
          minPrice += mDiff;
        }
    var markup =
     '<div class="row">' +
     '<div class="col-sm-3">' +
       '<b style="margin-center:5px">' + product.text + '</b>' +
     '</div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill">  سعر التكلفة - </i> '+product.costPrice + '</div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill">  سعر البيع - </i> <span class="label label-danger">' + maxPrice + '</span> </div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> سعر البيع الأدنى - </i> ' + minPrice + '</div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> مكان الصنف - </i> ' + product.place + '</div>' +
     '<div class="col-sm-1"><i class="badge badge-primary badge-pill"> - </i> <span class="label label-info"> ' + product.BRNAME + '</div>' +
     '</div>' +
     '<br>' +
     '<div class="row">' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم القطعة - </i> ' + product.serialNo + '</div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم القطعة التجاري- </i> ' + product.commCode + '</div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم التسلسل - </i> '  + product.id + '</div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill">حالة القطعة</i> ' +product.type+ '</div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> الكمية = </i> <span class="label label-success">' + '  ' + product.quantity + '<span> </div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> الشركة - </i> <span class="label label-warning">' + product.company + '</span> </div>' +
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
                echo $form->field($model, 'kind')->widget(Select2::classname(), [
                    'name' => 'kv-repo-template1',
                    'id' => 'kind',
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
                            'url' => Url::to(['/sales/itemlist']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term, page: params.page}; }'),
                            'processResults' => new JsExpression($resultsJs),
                            'cache' => true,
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('formatProduct'),
                        'templateSelection' => new JsExpression('formatProductSelection'),
                    ],
                    'pluginEvents' => [
                        'change' => 'function(event){
                var data_id = event.currentTarget.value;
                $.get("' . Url::to(['except/get-inv']) . '&category="+data_id, function(data){
                
                if(data != null){
                let maxPrice = parseFloat(data.maxPrice);
                maxPrice = Math.round(maxPrice);
                let diff
                let len = maxPrice.toString().length;
                let value = maxPrice.toString().substr(len-1, len-1);
                if(parseInt(value) < 5 && parseInt(value) != 0){
                 diff = 5 - parseInt(value);
                 maxPrice += diff;
                }
                if (parseInt(value) > 5) {
                 diff = 10 - parseInt(value);
                 maxPrice += diff;
               }
               $("#tempinvoice-quantity").val(1);
               $("#tempinvoice-saleprice").val(maxPrice);
               $("#tempinvoice-category").val(data.id);
               }
           });
       }',
                    ],
                ]);
            } else {
                $formatJs = <<< 'JS'
            var formatProduct = function (product) {
            if (product.loading) {
                return product.text;
            }
            // var data=$.parseJSON(data);
    var markup =
     '<div class="row">' +
     '<div class="col-sm-3">' +
       '<b style="margin-center:5px">' + product.text + '</b>' +
     '</div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill">  سعر التكلفة - </i> '+product.costPrice + '</div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill">  سعر البيع - </i> <span class="label label-danger">' + product.maxPrice + '</span> </div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> سعر البيع الأدنى - </i> ' +product. minPrice + '</div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> مكان الصنف - </i> ' + product.place + '</div>' +
     '<div class="col-sm-1"><i class="badge badge-primary badge-pill"> - </i> <span class="label label-info"> ' + product.BRNAME + '</div>' +
     '</div>' +
     '<br>' +
     '<div class="row">' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم القطعة - </i> ' + product.serialNo + '</div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم القطعة التجاري- </i> ' + product.commCode + '</div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم التسلسل - </i> '  + product.id + '</div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill">حالة القطعة</i> ' +product.type+ '</div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> الكمية = </i> <span class="label label-success">' + '  ' + product.quantity + '<span> </div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> الشركة - </i> <span class="label label-warning">' + product.company + '</span> </div>' +
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
                echo $form->field($model, 'kind')->widget(Select2::classname(), [
                    'name' => 'kv-repo-template1',
                    'id' => 'kind',
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
                            'url' => Url::to(['/sales/itemlist']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term, page: params.page}; }'),
                            'processResults' => new JsExpression($resultsJs),
                            'cache' => true,
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('formatProduct'),
                        'templateSelection' => new JsExpression('formatProductSelection'),
                    ],
                    'pluginEvents' => [
                        'change' => 'function(event){
                var data_id = event.currentTarget.value;
                $.get("' . Url::to(['except/get-inv']) . '&category="+data_id, function(data){
                
                if(data != null){
               $("#tempinvoice-quantity").val(1);
               $("#tempinvoice-saleprice").val(data.maxPrice);
               $("#tempinvoice-category").val(data.id);
               }
           });
       }',
                    ],
                ]);
            }
            //var data=$.parseJSON(data);

            ?>


            <?php
            $formatJs = <<< 'JS'
        var formatProduct = function (product) {
         if (product.loading) {
          return product.text;
         }
var markup =
  '<div class="row">' +
  '<div class="col-sm-3">' +
    '<b style="margin-center:5px">' + product.text + '</b>' +
  '</div>' +
  '<div class="col-sm-2"><i class="badge badge-primary badge-pill">  سعر التكلفة - </i> '+product.costPrice + '</div>' +
  '<div class="col-sm-2"><i class="badge badge-primary badge-pill">  سعر البيع - </i> <span class="label label-danger">' + product.maxPrice + '</span> </div>' +
  '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> سعر البيع الأدنى - </i> ' +product. minPrice + '</div>' +
  '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> مكان الصنف - </i> ' + product.place + '</div>' +
  '<div class="col-sm-1"><i class="badge badge-primary badge-pill"> - </i> <span class="label label-info"> ' + product.BRNAME + '</div>' +
  '</div>' +
  '<br>' +
  '<div class="row">' +
  '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم القطعة - </i> ' + product.serialNo + '</div>' +
  '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم القطعة التجاري- </i> ' + product.commCode + '</div>' +
  '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم التسلسل - </i> '  + product.id + '</div>' +
  '<div class="col-sm-2"><i class="badge badge-primary badge-pill">حالة القطعة</i> ' +product.type+ '</div>' +
  '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> الكمية = </i> <span class="label label-success">' + '  ' + product.quantity + '<span> </div>' +
  '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> الشركة - </i> <span class="label label-warning">' + product.company + '</span> </div>' +
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
            if ($company->searchById == 1) {
                echo $form->field($model, 'cat')->widget(Select2::class, [
                    'name' => 'kv-repo-template',
                    'id' => 'cat',
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
                            'url' => Url::to(['/sales/itemlistid']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term, page: params.page}; }'),
                            'processResults' => new JsExpression($resultsJs),
                            'cache' => true,
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('formatProduct'),
                        'templateSelection' => new JsExpression('formatProductSelection'),
                    ],
                    'pluginEvents' => [
                        'change' => 'function(event){
             var data_id = event.currentTarget.value;
             $.get("' . Url::to(['except/get-inv']) . '&category="+data_id, function(data){
            if(data != null){
            $("#tempinvoice-quantity").val(1);
            $("#tempinvoice-saleprice").val(data.maxPrice);
            $("#tempinvoice-category").val(data.id);
            }
        });
    }',
                        //var data=$.parseJSON(data);
                    ],
                ]);
            }
            ?>

            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-2">
                    <?= $form->field($model, 'quantity')->textInput(['placeholder' => 'Quantity']) ?>
                    <br>
                    <div class="form-group">
                        <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-fw fa-plus"></i>' . ' ' . Yii::t('app', 'Add') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

                    </div>
                </div>
                <div class="col-md-2">
                    <?php
                    if (Yii::$app->user->identity->editSalePrice == 1) {
                        echo $form->field($model, 'salePrice')->textInput(['placeholder' => 'Sale Price']);
                    } else {
                        echo $form->field($model, 'salePrice')->textInput(['placeholder' => 'Sale Price', 'readonly' => true]);
                    }
                    ?>
                </div>
                <div class="col-md-2">
                    <?php
                    echo $form->field($model, 'category')->hiddenInput(['placeholder' => 'Category'])->label('');
                    ?>
                </div>
                <div class="col-md-2">
                    <?php
                    if ($company->waitQnty == 1 && Yii::$app->user->identity->client == null) {
                        echo $form->field($model, 'waitQnty')->textInput(['placeholder' => 'Wait Qnty']);
                    }
                    ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

    </div>
    <?php
    if (Yii::$app->user->identity->editSalePrice == 0) {
        $gridColumn = [
            ['class' => 'yii\grid\SerialColumn'],

            ['attribute' => 'id', 'visible' => false],

            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => Yii::t('app', 'category'),
                'label' => Yii::t('app', 'ID'),
                'contentOptions' => ['style' => 'font-size:14px;'],
                'headerOptions' => ['style' => 'width:15%'],
            ],

            [
                'attribute' => 'category',
                'headerOptions' => ['style' => 'width:40%'],
                'value' => function ($model) {
                    return Html::a(Yii::t('app', ' {modelClass}', [
                        'modelClass' => $model->category0->name,
                    ]), ['category/info', 'id' => $model->category0->id], ['class' => 'btn-link popupModal']);
                },
                'format' => 'raw',
            ],

            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => Yii::t('app', 'serial_number'),
                'label' => Yii::t('app', 'Serial No'),
                'contentOptions' => ['style' => 'font-size:14px;'],
                'headerOptions' => ['style' => 'width:15%'],
                'value' => 'category0.serialNo',
                'editableOptions' => [
                    'asPopover' => true,
                ],
            ],

            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => Yii::t('app', 'serial_number'),
                'label' => Yii::t('app', 'Comm Code'),
                'contentOptions' => ['style' => 'font-size:14px;'],
                'headerOptions' => ['style' => 'width:15%'],
                'value' => 'category0.commCode',
                'editableOptions' => [
                    'asPopover' => true,
                ],
            ],

            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'quantity',
                'label' => Yii::t('app', 'quantity'),
                'contentOptions' => ['style' => 'font-size:14px;'],
                'headerOptions' => ['style' => 'width:15%'],
                'editableOptions' => [
                    'asPopover' => true,
                ],
            ],

            [
                //'class'=>'kartik\grid\EditableColumn',
                'attribute' => Yii::t('app', 'salePrice'),
                'contentOptions' => ['style' => 'font-size:14px;'],
                'headerOptions' => ['style' => 'width:15%'],
            ],

            [
                'class' => 'kartik\grid\FormulaColumn',
                'header' => Yii::t('app', 'Total'),
                'vAlign' => 'middle',
                'value' => function ($model, $key, $index, $widget) {
                    $p = compact('model', 'key', 'index');
                    return $widget->col(6, $p) * $widget->col(7, $p);
                },
                'headerOptions' => ['class' => 'kartik-sheet-style'],
                'hAlign' => 'right',
                'width' => '15%',
                'format' => ['decimal', 3],
                'mergeHeader' => true,
                'pageSummary' => true,
                'footer' => true,
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '<div class="btn-group btn-group-sm" role="group" aria-label="...">{delete}</div>',
                'buttons' => [
                    'delete' => function ($url) {
                        return Html::a('<i class="fa fa-trash"></i>', $url, [
                            'title' => Yii::t('yii', 'Delete'),
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'class' => 'btn btn-default',
                        ]);
                    },
                ],
            ],
        ];
    } else {
        $gridColumn = [
            ['class' => 'yii\grid\SerialColumn'],

            ['attribute' => 'id', 'visible' => false],

            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => Yii::t('app', 'category'),
                'label' => Yii::t('app', 'ID'),
                // 'contentOptions' => ['style' => 'font-size:14px;'],
                'headerOptions' => ['style' => 'width:5%'],
            ],

            [
                'attribute' => 'category',
                'headerOptions' => ['style' => 'width:30%'],
                'value' => function ($model) {
                    return Html::a(Yii::t('app', ' {modelClass}', [
                        'modelClass' => $model->category0->name,
                    ]), ['category/info', 'id' => $model->category0->id], ['class' => 'btn-link popupModal']);
                },
                'format' => 'raw',
            ],

            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => Yii::t('app', 'serial_number'),
                'label' => Yii::t('app', 'Serial No'),
                //'contentOptions' => ['style' => 'font-size:14px;'],
                'headerOptions' => ['style' => 'width:10%'],
                'value' => 'category0.serialNo',
                'editableOptions' => [
                    'asPopover' => true,
                ],
            ],

            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => Yii::t('app', 'serial_number'),
                'label' => Yii::t('app', 'Comm Code'),
                //'contentOptions' => ['style' => 'font-size:14px;'],
                'headerOptions' => ['style' => 'width:10%'],
                'value' => 'category0.commCode',
                'editableOptions' => [
                    'asPopover' => true,
                ],
            ],

            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'quantity',
                'label' => Yii::t('app', 'quantity'),
                //'contentOptions' => ['style' => 'font-size:14px;'],
                'headerOptions' => ['style' => 'width:8%'],
                'editableOptions' => [
                    'asPopover' => true,
                ],
                'pageSummary' => true
            ],

            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'waitQnty',
                'label' => Yii::t('app', 'Wait Qnty'),
                //'contentOptions' => ['style' => 'font-size:14px;'],
                'headerOptions' => ['style' => 'width:8%'],
                'editableOptions' => [
                    'asPopover' => true,
                ],
            ],

            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => Yii::t('app', 'salePrice'),
                //'contentOptions' => ['style' => 'font-size:14px;'],
                'headerOptions' => ['style' => 'width:8%'],
                'editableOptions' => [
                    'asPopover' => true,
                ],
            ],

            [
                'class' => 'kartik\grid\FormulaColumn',
                'header' => Yii::t('app', 'Total'),
                'vAlign' => 'middle',
                'value' => function ($model, $key, $index, $widget) {
                    $p = compact('model', 'key', 'index');
                    return $widget->col(6, $p) * $widget->col(8, $p);
                },
                'headerOptions' => ['class' => 'kartik-sheet-style'],
                'hAlign' => 'right',
                'width' => '10%',
                'format' => ['decimal', 3],
                'mergeHeader' => true,
                'pageSummary' => true,
                'footer' => true,
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '<div class="btn-group btn-group-sm" role="group" aria-label="...">{delete}{image}</div>',
                'buttons' => [
                    'image' => function ($url, $model, $key) {
                        $url = Url::to(['category/image', 'id' => $model['category']]);
                        return Html::a(
                            '<span class="glyphicon glyphicon-open"></span>',
                            $url,
                            ['class' => 'btn btn-default popupModal']
                        );
                    },
                    'delete' => function ($url) {
                        return Html::a('<i class="fa fa-trash"></i>', $url, [
                            'title' => Yii::t('yii', 'Delete'),
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                            'class' => 'btn btn-default',
                        ]);
                    },
                ],
            ],
        ];
    }
    ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{items}{pager}',
        'summary' => true,
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
                'id' => 'w0',
            ],

        ],
        'showPageSummary' => true,
    ]); ?>




</div>

<?php
$this->registerJs("$('#focus_first').select2('focus');"); ?>
<?php $this->registerJs("
    $(function () {
    $('[data-toggle=\"tooltip\"]').tooltip()});", $this::POS_END, 'tooltips'); ?>