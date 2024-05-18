<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use app\models\TempInvoice;
use yii\widgets\Pjax;
use yii\helpers\Url;
use app\models\Category;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use kartik\editable\Editable;
use kartik\select2\Select2; // or kartik\select2\Select2
use yii\base\View;


/* @var $this yii\web\View */
/* @var $model app\models\TempInvoice */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="temp-invoice-form">

<div class="row">
    <?php if( $dataProvider->getCount() !=0 ){?>
          

        <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-fw fa-save"></i></span>

                    <div class="info-box-content">
                    <span class="info-box-text"><?=Yii::t('app','Items Count')?></span>
                    <span class="info-box-number"><?=$dataProvider->getCount() ?><small></small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
        </div>
    <?php }?>


    <?php $count=0 ;
        foreach ($dataProvider->getModels() as $dataP) {
            $count=$dataP->quantity+$count;
        }

        if( $count !=0 ){ ?>
            
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-fw fa-gear"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text"><?=Yii::t('app','items quantity')?></span>
                                <span class="info-box-number"><?=$count ?><small></small></span>
                            </div>
                        </div>
                    </div>
    <?php }?>


    <?php $count=0 ;
        $total= 0;
        $profit = 0;
        foreach ($dataProvider->getModels() as $sum) {
       
            $count=$sum->quantity*$sum->salePrice;
            $countProfit=($sum->quantity*$sum->salePrice)-($sum->quantity*$sum->costPrice);
            $total = $total + $count;
            $profit = $profit + $countProfit;

        }

        if( $count !=0 ){ ?>
            <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="fa fa-fw fa-dollar"></i></span>

                        <div class="info-box-content">
                        <span class="info-box-text"><?=Yii::t('app','Total Invoice')?></span>
                        <span class="info-box-number"><?=number_format('%i',$total ) ?>
                        <br>
                       <?php if (Yii::$app->user->can('seeCostPrice')) { ?>
                        <small><?=number_format('%i',$profit )?></small>
                       <?php } ?>
                        </span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <hr>
        <?php  }?>
</div>

<div class="row">

    <div class="col-md-12">
        <?php echo Html::button('<i class="fa fa-fw fa-cart"></i>' . ' ' . Yii::t('app', 'Create Category'),
            ['value' => Url::to(['category/create-category']), 'class' => 'btn btn-primary popup']); ?>
        
            <?php echo Html::button('<i class="fa fa-fw fa-step-forward"></i>' . ' ' . Yii::t('app', 'Complete Sale'), ['value' => Url::to(['sales/create']), 'class' => 'btn btn-danger popup']); ?>

            
            <?= Html::a('<i class="fa fa-fw fa-pause"></i>'.Yii::t('app', 'Hold'), ['hold'], ['class' => 'btn btn-success']) ?>

            <?php echo Html::button('<i class="fa fa-fw fa-cloud-upload"></i>' . ' ' . Yii::t('app', 'Holded Invoices'), ['value' => Url::to(['holded']), 'class' => 'btn btn-warning popup']); ?>

                <?= Html::a('<i class="fa fa-fw fa-trash "></i>' . ' ' .Yii::t('app', 'Delete All'), ['delete-all'], [
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
    'options' => ['placeholder' => Yii::t('app','Search...'), 
    'dir' => 'rtl',
    'multiple'=>true,
    
    'onchange' => '
   
        $.get( "index.php?r=temp-invoice/add&categoryid="+$(this).val()+"&type="+1, function( data ) {
              
        });
        ',
    ],
// $.get( "add?categoryid="+category+"&type="+1, function( data ) {});
    'pluginOptions' => [
        'autofocus' =>true,
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
  
    <?php $form = ActiveForm::end(); ?>
       
       

    <?php 
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
       
        ['attribute' => 'id', 'visible' => false],
        [
            'attribute' => 'category',
            'format' => 'text',
            'value' => 'category0.name',
            'headerOptions' => ['style' => 'width:50%'],
            'contentOptions' => function($model) {
               if (Yii::$app->user->can('seeCostPrice')) {
                return [
                    'class' => 'cell-with-tooltip',
                    'data-toggle' => 'tooltip',
                    'style' => 'font-size:14px;',
                    'data-placement' => 'top', // top, bottom, left, right
                    'data-container' => 'body', // to prevent breaking table on hover
                    'title' => ' Cost Price '. $model->price->costPrice,
                    // 'value' => $model->category0->name,
                ]; 
               }else{
                return [
                    'class' => 'cell-with-tooltip',
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top', // top, bottom, left, right
                    'data-container' => 'body', // to prevent breaking table on hover
                    'title' => ' Minimum Price '. $model->price->minPrice,
                    // 'value' => $model->category0->name,
                ]; 

               }
              
            }
        ],
       
        // 'serial_number',
        [
            'class'=>'kartik\grid\EditableColumn',
            'attribute' => Yii::t('app', 'serial_number'),
            'label'=>Yii::t('app', 'Serial No'),
            'contentOptions' => ['style' => 'font-size:14px;'],
            'headerOptions' => ['style' => 'width:20%'],
            'value' => 'category0.serialNo',
            'editableOptions' => [                
                'asPopover' => true,
            ],
        ],
       
        [
            'class'=>'kartik\grid\EditableColumn',
            'attribute' => 'quantity',
            'label'=>Yii::t('app', 'quantity'),
            'contentOptions' => ['style' => 'font-size:14px;'],
            'headerOptions' => ['style' => 'width:20%'],
            'editableOptions' => [                
                'asPopover' => true,
            ],
        ],
        // [
        //     'class'=>'kartik\grid\EditableColumn',
        //     'attribute' => 'quantity',
        //     'format' => 'text',
        //     'value' => 'quantity',
        //     'contentOptions' => function($model) {
              
        //         return [
        //             'class' => 'cell-with-tooltip',
        //             'data-toggle' => 'tooltip',
        //             'data-placement' => 'top', // top, bottom, left, right
        //             'data-container' => 'body', // to prevent breaking table on hover
        //             'title' => 'Quantity'. $model->inventory->quantity,
        //             // 'value' => $model->category0->name,
        //         ]; 
               
              
        //     }
        // ],
        [
            'class'=>'kartik\grid\EditableColumn',
            'attribute' => Yii::t('app', 'salePrice'),
            'contentOptions' => ['style' => 'font-size:14px;'],
            'headerOptions' => ['style' => 'width:20%'],
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
                return $widget->col(4, $p) * $widget->col(5, $p);
            },
            'headerOptions' => ['class' => 'kartik-sheet-style'],
            'hAlign' => 'right', 
            'width' => '7%',
            'format' => ['decimal', 3],
            'mergeHeader' => true,
            'pageSummary' => true,
            'footer' => true
           
           
        ],
        [ 
            'class'=>'kartik\grid\EditableColumn',
            'attribute' => 'mac_address',
            'label'=>Yii::t('app', 'Mac Address'),
            'contentOptions' => ['style' => 'font-size:14px;'],
            'headerOptions' => ['style' => 'width:20%'],
          //  'inputType' => Editable::INPUT_TEXTAREA,
            'editableOptions' => [                
                'asPopover' => true,
                'inputType' => Editable::INPUT_TEXTAREA,
            ],
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
        // 'filterModel' => $searchModel,
        'layout' => '{items}{pager}',
        'summary'=>true,
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' =>[

            'neverTimeout'=>true,
    
            'options'=>[
    
                    'id'=>'w0',
    
                ]
    
            ],  
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-temp-invoice']],
        'showPageSummary' => true,
        // 'panel' => [
        //     'type' => GridView::TYPE_PRIMARY,
        //     'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title),
        // ],
        // your toolbar can include the additional full export menu
        
    ]); ?>
    
    
   
    
    </div>

</div>   
 
    
    
   
    

</div>


<?php $this->registerJs("
    $(function () {
        $('[data-toggle=\"tooltip\"]').tooltip();
    });
", $this::POS_END, 'tooltips'); ?>

