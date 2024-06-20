<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\TempInvoice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="temp-invoice-form">


    <div class="row">

        <div class="col-md-12">
            <?php echo Html::button(
                '<i class="fa fa-fw fa-cart"></i>' . ' ' . Yii::t('app', 'Create Category'),
                ['value' => Url::to(['category/create-category']), 'class' => 'btn btn-primary popup']
            ); ?>

            <?php echo Html::button('<i class="fa fa-fw fa-step-forward"></i>' . ' ' . Yii::t('app', 'Complete Sale'), ['value' => Url::to(['sales/back-create']), 'class' => 'btn btn-danger popup']); ?>


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
    <br>
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
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم القطعة 1 - </i> ' + product.serialNo + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم القطعة 2 - </i> ' + product.commCode + '</div>' +
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
            echo Select2::widget([
                'name' => 'kv-repo-template',
                //'value' => '14719648',
                //'initValueText' => 'kartik-v/yii2-widgets',
                'id' => 'focus_first',
                'language' => 'en',
                'options' => [
                    'placeholder' => Yii::t('app', 'Search...'),
                    'dir' => 'rtl',
                    'multiple' => true,

                    'onchange' => '
   
        $.get( "index.php?r=temp-back-sales/add&categoryid="+$(this).val()+"&type="+1, function( data ) {
              
        });
        ',
                ],
                // $.get( "add?categoryid="+category+"&type="+1, function( data ) {});
                'pluginOptions' => [
                    'autofocus' => true,
                    // 'allowClear' => true,
                    'minimumInputLength' => 1,
                    'ajax' => [
                        'url' => Url::to(['/except/itemlist']),
                        'dataType' => 'json',
                        // 'delay' => 250,
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
                    'class' => 'kartik\grid\EditableColumn',
                    'attribute' => Yii::t('app', 'salePrice'),
                    'contentOptions' => ['style' => 'font-size:14px;'],
                    'headerOptions' => ['style' => 'width:15%'],
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
                        'delete' => function ($url, $searchModel, $key) {
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
            <br>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                // 'filterModel' => $searchModel,
                'layout' => '{items}{pager}',
                'summary' => true,
                'columns' => $gridColumn,
                'pjax' => true,
                'pjaxSettings' => [
                    'neverTimeout' => true,
                    'options' => [
                        'id' => 'w0',
                    ]

                ],
                'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-temp-invoice']],
                'showPageSummary' => true,
            ]); ?>

        </div>

    </div>

</div>

<?php
$this->registerJs("$('#focus_first').select2('focus');");
?>

<?php $this->registerJs("
    $(function () {
    $('[data-toggle=\"tooltip\"]').tooltip()});", $this::POS_END, 'tooltips');
?>