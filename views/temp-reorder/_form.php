<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\grid\GridView;
use app\models\TempReorder;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\TempReorder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="temp-reorder-form">
<div class="col-md-12">
    <?php 
    echo Html::button('<i class="fa fa-fw fa-cart"></i>' . ' ' . Yii::t('app', 'Create Category'),
        ['value' => Url::to(['category/create-category']), 'class' => 'btn btn-primary popup']); ?>
    
        <?= Html::button('<i class="fa fa-fw fa-step-forward"></i>' . ' ' . Yii::t('app', 'Complete'), ['value' => Url::to(['reorder/create']), 'class' => 'btn btn-danger popup']); ?>

        
            <?php
              echo  Html::a('<i class="fa fa-fw fa-trash "></i>' . ' ' .Yii::t('app', 'Delete All'), ['delete-all'], [
                'class' => 'btn btn-warning pull-left',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ])
            ?>
</div> 
</div> 
<br><br><hr>
    <?php $form = ActiveForm::begin([
        'options' => [
        'enableClientValidation'=>false,
        ]
    ]

    ); ?>

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

    <?php 
    // echo $form->field($model, 'category')->widget(\kartik\widgets\Select2::classname(), [
    //    'id' => 'categoryx',
    //    'data' => \yii\helpers\ArrayHelper::map($data, 'id', 
    //     function($model) {
    //         return $model['name'].' --  '.$model['serialNo'];
    //     }
    // ),
        
    //     'language' => 'en',
    //     'options' => ['placeholder' => Yii::t('app', 'Choose Category'),
    //     'dir' => 'rtl',
    //     'onchange' => 'getInv( $(this) )'
    //     ],
    //     'pluginOptions' => [
    //         'allowClear' => true 
    //     ],
        
    // ]);
     ?> 

    <?= $form->field($model, 'quantity')->textInput() ?>
    <br>
    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-plus"></i>' . ' ' .Yii::t('app', 'Add'), ['class' => 'btn btn-success']) ?>
    </div>
    <br>
    <?php ActiveForm::end(); ?>
    <?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
       
        ['attribute' => 'id', 'visible' => false],
        [
                'attribute' => 'category',
                'label' => Yii::t('app', 'Category'),
                'contentOptions' => ['style' => 'font-size:14px;'],
                'value' => function($model){                   
                    return $model->category0->name;                   
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\app\models\Category::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Category', 'id' => 'grid-temp-invoice-purchase-search-category']
        ],

        'category0.serialNo',

        [
            'class'=>'kartik\grid\EditableColumn',
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
        'containerOptions' => ['style'=>'overflow: auto'], 
        'layout' => '{items}{pager}',
        'summary'=>true,
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' =>[

            'neverTimeout'=>true,
    
            'options'=>[
    
                    'id'=>'w1',
    
                ]
    
            ],  
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-temp-invoice-purchase']],
        'showPageSummary' => true,        
    ]); ?>
</div>