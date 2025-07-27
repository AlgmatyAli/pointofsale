<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model app\models\TempArrangement */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="temp-arrangement-form">
    <div class="col-md-12">
        <?php echo Html::button('<i class="fa fa-fw fa-step-forward"></i>' . ' ' . Yii::t('app', 'Complete'), ['value' => Url::to(['arrangement/create']), 'class' => 'btn btn-danger popup']); ?>

        <?= Html::a('<i class="fa fa-fw fa-trash "></i>' . ' ' . Yii::t('app', 'Delete All'), ['delete-all'], [
            'class' => 'btn btn-warning pull-left',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ])
        ?>
    </div>
</div>
<br><br>
<hr>
<?php $form = ActiveForm::begin(); ?>

<?= $form->errorSummary($model); ?>

<?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

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
echo $form->field($model, 'category')->widget(Select2::class, [
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
            'url' => Url::to(['/temp-arrangement/itemlist']),
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
           $.get("' . Url::to(['temp-arrangement/get-inv']) . '&category="+data_id, function(data){
              
               if(data != null){
               $("#temparrangement-realquantity").val(data.quantity);
               }
           });
       }',
    ],
]);
//  var data = $.parseJSON(data);
?>
<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'realQuantity')->textInput(['placeholder' => 'Real Quantity']) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'quantity')->textInput(['placeholder' => 'Quantity']) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'type')->dropDownList(['1' => 'اضافة للمخزون', '-1' => 'انقاص من المخزون'], ['prompt' => yii::t('app', 'Transaction Type')])->label(yii::t('app', 'Transaction Type')) ?>
        <?= $form->field($model, 'stockTaking')->checkbox(['checked' => false]) ?>
    </div>
</div>
<p>
    <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Add'), ['class' => 'btn btn-success']) ?>
</p>
<br>
<?php ActiveForm::end(); ?>

<div class="row">
    <?php
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],

        ['attribute' => 'id', 'visible' => false],

        'category0.serialNo',

        [
            'attribute' => 'category',
            'headerOptions' => ['style' => 'width:30%'],
            'value' => function ($model) {
                return Html::a(Yii::t('app', ' {modelClass}', [
                    'modelClass' => $model->category0->name,
                ]), ['category/change-place', 'id' => $model->category0->id], ['class' => 'btn-link popupModal']);
            },
            'format' => 'raw',
        ],

        [
            'attribute' => 'category0.place',
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->category0->place == null) {
                    return "<i class='fa fa-minus'></i>";; // "x" icon in red color
                } else {
                    return $model->category0->place;
                }
            },
        ],

        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'quantity',
            'contentOptions' => ['style' => 'font-size:14px;'],
            'label' => Yii::t('app', 'quantity'),
            'editableOptions' => [
                'asPopover' => true,
            ],
            'format' => ['decimal', 3],
            'pageSummary' => true,
            'footer' => true
        ],

        'type',

        [
            'attribute' => 'stockTaking',
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->stockTaking === 1) {
                    return "<i class='fa fa-check'></i>";; // "x" icon in red color
                } else {
                    return "<i class='fa fa-times'></i>";; // check icon 
                }
            },
        ],

        // [
        //     'attribute' => 'created_at',
        //     'label' => 'سنة الجرد',
        //     'format' => 'raw',
        //     'value' => function ($model) {
        //         return  date('Y', $model->created_at);
        //     }
        // ],

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
            'buttons' => [
                'save-as-new' => function ($url) {
                    return Html::a('<h1><span class="glyphicon btn-lg glyphicon-copy"></span></h1>', $url, ['title' => 'Save As New']);
                },
            ],
        ],
    ];
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['style' => 'font-size:10px;'],
        'containerOptions' => ['style' => 'overflow: auto'],
        'layout' => '{items}{pager}',
        'summary' => true,
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' => [

            'neverTimeout' => true,

            'options' => [

                'id' => 'w1',
            ]

        ],
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-temp-transfer-items']],
        'showPageSummary' => true,
    ]); ?>
</div>