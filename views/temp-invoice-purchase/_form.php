<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\TempInvoicePurchase */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="temp-invoice-purchase-form">
    <div class="row">
        <div class="col-md-4">
            <h3><?= Html::encode($this->title) ?></h3>
            <hr>
        </div>
        <?php

        if (isset($_GET['rate'])) {
            $rate = $_GET['rate'];
        } else {
            $rate = '0';
        }
        if (isset($_GET['totalCost'])) {
            $totalCost = $_GET['totalCost'];
        } else {
            $totalCost = '0';
        }
        if (isset($_GET['totalInvoice'])) {
            $totalInvoice = $_GET['totalInvoice'];
        } else {
            $totalInvoice = '0';
        }
        if (isset($_GET['derhamRate'])) {
            $derhamRate = $_GET['derhamRate'];
        } else {
            $derhamRate = '0';
        }

        ?>
        <div class="col-md-4"></div>

        <div class="col-md-6 pull-left " style="margin-top: 15px">
            <div class="btn-group">
                <?php echo Html::button(
                    '<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Save'),
                    [
                        'value' => Url::to(['purchases/create', 'totalInvoice' => $totalInvoice]), 'class' => 'btn btn-danger popup'
                    ]
                ); ?>

                <?php echo Html::button(
                    '<i class="fa fa-fw fa-plus"></i>' . ' ' . Yii::t('app', 'Create Category'),
                    ['value' => Url::to(['category/create-category']), 'class' => 'btn btn-primary popup']
                ); ?>

                <?= Html::a('<i class="fa fa-fw fa-upload"></i>' . ' ' . Yii::t('app', "Upload"), Url::toRoute(['upload']), ['class' => 'btn btn-info']) ?>

                <?= Html::a('<i class="fa fa-fw fa-trash"></i>' . ' ' . Yii::t('app', 'Delete All'), ['delete-all'], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ])
                ?>
            </div>
        </div>
    </div>
    <br>
    <?php $form = ActiveForm::begin(
        [
            'id' => 'temp-invoice-purchase-form',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
        ]
    ); ?>

    <?= $form->errorSummary($model); ?>
    <div class="row">

        <div class='col-md-3'>
            <?= $form->field($model, 'totalInvoice')->textInput([
                'placeholder' => 'Total Invoice',
                'value' => $totalInvoice
            ])
            ?>
        </div>
        <div class='col-md-3'>
            <?= $form->field($model, 'totalCost')->textInput([
                'maxlength' => true,
                'onfocusout' => 'totalRate( $(this) )',
                'value' => $totalCost
            ]) ?>
        </div>
        <div class='col-md-3'>
            <?= $form->field($model, 'rate')->textInput([
                'placeholder' => 'Rate',
                'value' => $rate
            ])
            ?>
        </div>
        <div class='col-md-3'>
            <?= $form->field($model, 'derhamRate')->textInput([
                'placeholder' => 'derhamRate',
                'value' => $derhamRate
            ])
            ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
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
      //'<div class="col-sm-2"><i class="badge badge-primary badge-pill"> الكمية = </i> ' + '  ' + product.quantity + '</div>' +
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
        echo $form->field($model, 'category')->widget(Select2::classname(), [
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
            'pluginEvents' => [
                'change' => 'function(event){
            var data_id = event.currentTarget.value;
            $.get("' . Url::to(['temp-invoice-purchase/get-inv']) . '&category="+data_id, function(data){
                
                if(data != null){
                $("#tempinvoicepurchase-quantity").val(data.quantity);
                $("#tempinvoicepurchase-costprice").val(data.costPrice);
                $("#tempinvoicepurchase-saleprice").val(data.maxPrice);
                $("#tempinvoicepurchase-saleprice_").val(data.minPrice);
                $("#tempinvoicepurchase-saleprice_2").val(data.minPrice2);
                $("#tempinvoicepurchase-saleprice_3").val(data.minPrice3);
                $("#category-serialno").val(data.serialNo);
                }
            });
        }'
            ]

        ]);
        //var data=$.parseJSON(data);
        ?>

    </div>

</div>

<div class="row">

    <div class="col-md-2">
        <?= $form->field($model, 'quantity')->textInput(['placeholder' => 'Quantity']) ?>
        <br>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-fw fa-plus"></i>' . ' ' . Yii::t('app', 'Add') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

    </div>
    <div class="col-md-2">
        <?= $form->field($model, 'costPrice')->textInput([
            'maxlength' => true, 'placeholder' => 'Cost Price',
            //'onfocusout' => 'totalCost( $(this) )'
        ]) ?>
    </div>

    <div class="col-md-2">
        <?= $form->field($model, 'salePrice')->textInput(['maxlength' => true, 'placeholder' => 'Sale Price']) ?>
    </div>

    <div class="col-md-2">
        <?= $form->field($model, 'salePrice_')->textInput(['maxlength' => true, 'placeholder' => 'Sale Price']) ?>
    </div>

    <div class="col-md-2">
        <?= $form->field($model, 'salePrice_2')->textInput(['maxlength' => true, 'placeholder' => 'Sale Price 2']) ?>
    </div>

    <div class="col-md-2">
        <?= $form->field($model, 'salePrice_3')->textInput(['maxlength' => true, 'placeholder' => 'Sale Price 3']) ?>
    </div>

    <div class="col-md-2">
        <?= $form->field($model, 'box')->hiddenInput(['placeholder' => 'Box', 'value' => '1'])->label(false) ?>
    </div>

    <div class="col-md-2">
        <?= $form->field($model, 'state')->hiddenInput(['readonly' => true, 'value' => '0'])->label(false) ?>
    </div>
</div>
</div>

<?php ActiveForm::end(); ?>

<?php
$gridColumn = [
    ['class' => 'yii\grid\SerialColumn'],

    ['attribute' => 'id', 'visible' => false],
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
        'attribute' => Yii::t('app', 'costPrice'),
        'contentOptions' => ['style' => 'font-size:14px;'],

        'editableOptions' => [
            'asPopover' => true,
        ],
        'format' => ['decimal', 3],
        'pageSummary' => true,
        'footer' => true
    ],

    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'quantity',
        'contentOptions' => ['style' => 'font-size:14px;'],
        'label' => Yii::t('app', 'quantity'),
        'editableOptions' => [
            'header' => Yii::t('app', 'quantity'),
            'inputType' => kartik\editable\Editable::INPUT_TEXT,
            'options' => [
                'pluginOptions' => []
            ]
        ],
        'format' => ['decimal', 3],
        'pageSummary' => true,
        'footer' => true
    ],

    [
        'class' => 'kartik\grid\FormulaColumn',
        'contentOptions' => ['style' => 'font-size:14px;'],
        'header' => Yii::t('app', 'Total'),
        'vAlign' => 'middle',
        'value' => function ($model, $key, $index, $widget) {
            $p = compact('model', 'key', 'index');
            return $widget->col(3, $p) * $widget->col(4, $p);
        },
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'hAlign' => 'right',
        'width' => '10%',
        'format' => ['decimal', 3],
        'mergeHeader' => true,
        'pageSummary' => true,
        'footer' => true
    ],

    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => Yii::t('app', 'costTotal'),
        'contentOptions' => ['style' => 'font-size:14px;'],

        'editableOptions' => [
            'asPopover' => true,
        ],
        'format' => ['decimal', 3],
        'pageSummary' => true,
        'footer' => true
    ],

    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => Yii::t('app', 'salePrice'),
        'contentOptions' => ['style' => 'font-size:14px;'],

        'editableOptions' => [
            'asPopover' => true,
        ],
        'format' => ['decimal', 3],
        'pageSummary' => true,
        'footer' => true
    ],

    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => Yii::t('app', 'salePrice_'),
        'contentOptions' => ['style' => 'font-size:14px;'],

        'editableOptions' => [
            'asPopover' => true,

        ],
        'format' => ['decimal', 3],
        'pageSummary' => true,
        'footer' => true
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
?>
<?php Pjax::begin();
?>
<?= GridView::widget([
    'id' => 'my-gridview',
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

<?php Pjax::end(); ?>
</div>
