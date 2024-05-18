<?php

use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TempInvoice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="temp-invoice-form">

    <div class="row">

        <?php if ($dataProvider->getCount() != 0) { ?>


            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-fw fa-save"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text"><?= Yii::t('app', 'Items Count') ?></span>
                        <span class="info-box-number"><?= $dataProvider->getCount() ?><small></small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
        <?php } ?>


        <?php $count = 0;
        foreach ($dataProvider->getModels() as $dataP) {
            $count = $dataP->quantity + $count;
        }

        if ($count != 0) { ?>

            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-fw fa-gear"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text"><?= Yii::t('app', 'items quantity') ?></span>
                        <span id='counts' class="info-box-number"><?= $count ?><small></small></span>
                    </div>
                </div>
            </div>
        <?php } ?>


        <?php $count = 0;
        $total = 0;
        $profit = 0;
        foreach ($dataProvider->getModels() as $sum) {

            $count = $sum->quantity * $sum->salePrice;
            $countProfit = ($sum->quantity * $sum->salePrice) - ($sum->quantity * $sum->costPrice);
            $total = $total + $count;
            $profit = $profit + $countProfit;
        }

        if ($count != 0) { ?>
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-fw fa-dollar"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text"><?= Yii::t('app', 'Total Invoice') ?></span>
                        <span id='totalUp' class="info-box-number"><?= @money_format('%i', $total) ?>
                            <br>
                            <?php if (Yii::$app->user->identity->seeCostPrice == 1) { ?>
                                <small><?= @money_format('%i', $profit) ?></small>
                            <?php } ?>
                        </span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <hr>
        <?php } ?>
    </div>

    <div class="row">

        <div class="col-md-12">
            <?php echo Html::button(
                '<i class="fa fa-fw fa-cart"></i>' . ' ' . Yii::t('app', 'Create Category'),
                ['value' => Url::to(['category/create-category']), 'class' => 'btn btn-primary popup']
            ); ?>

            <?php echo Html::button('<i class="fa fa-fw fa-step-forward"></i>' . ' ' . Yii::t('app', 'Complete Sale'), ['value' => Url::to(['sales/create']), 'class' => 'btn btn-danger popup']); ?>

            <?= Html::a('<i class="fa fa-fw fa-pause"></i>' . Yii::t('app', 'Hold'), ['hold'], ['class' => 'btn btn-success']) ?>

            <?php echo Html::button('<i class="fa fa-fw fa-cloud-upload"></i>' . ' ' . Yii::t('app', 'Holded Invoices'), ['value' => Url::to(['holded']), 'class' => 'btn btn-warning popup']); ?>

            <?php echo Html::button('<i class="fa fa-fw fa-fast"></i>' . ' ' . Yii::t('app', 'Adedd Fast'), ['value' => Url::to(['fast']), 'class' => 'btn btn-info popup']); ?>

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
    <?php $form = ActiveForm::begin(
        [
            'options' => [
                //'enableClientValidation'=>false,
            ],
        ]

    ); ?>
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
     '<div class="col-sm-4">' +
       '<b style="margin-center:5px">' + product.text + '</b>' +
     '</div>' +
     //if (Yii::$app->user->can('seeCostPrice')) {
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill">  سعر التكلفة - </i> '+product.costPrice + '</div>' +
     //}
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill">  سعر البيع - </i> <span class="label label-danger">' + product.maxPrice + '</span> </div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> سعر البيع الأدنى - </i> ' + product.minPrice + '</div>' +
     '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> مكان الصنف - </i> ' + product.place + '</div>' +
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
           var id = event.currentTarget.value;
           
         
           $.get("' . Url::to(['except/get-inv']) . '&category="+data_id, function(data){
               var data=$.parseJSON(data);
               if(data != null){
                var salePrice = data.maxPrice;
                var costPrice = data.costPrice;
                var serialNo = data.serialNo;
                var category = data.name;
              
                

                saveData(id,salePrice,costPrice,serialNo,category);
               $("#tempinvoice-quantity").val();
               $("#tempinvoice-saleprice").val(data.maxPrice);
               $("#tempinvoice-category").val(data.id);
               }
           });
       }',
                ],
            ]);
            ?>

            <?php

            $formatJs = <<< 'JS'
 var formatProduct = function (product) {
   if (product.loading) {
    return product.text;
   }
 var markup =
    '<div class="row">' +
      '<div class="col-sm-4">' +
        '<b style="margin-center:5px">' + product.text + '</b>' +
      '</div>' +
      //if (Yii::$app->user->can('seeCostPrice')) {
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill">  سعر التكلفة - </i> '+product.costPrice + '</div>' +
      //}
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill">  سعر البيع - </i> <span class="label label-danger">' + product.maxPrice + '</span> </div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> سعر البيع الأدنى - </i> ' + product.minPrice + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> مكان الصنف - </i> ' + product.place + '</div>' +
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
                echo $form->field($model, 'cat')->widget(Select2::classname(), [
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
                var data=$.parseJSON(data);
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
            ?>

            <div class="row">
                <div class="col-md-12">
                    <table id="myTable" class="table table-hover">
                        <thead>
                            <tr class="titlerow">
                                <th scope="col"><?= yii::t('app', 'ID') ?></th>
                                <th scope="col"><?= yii::t('app', 'Category') ?></th>
                                <th scope="col"><?= yii::t('app', 'Serial Number') ?></th>
                                <th scope="col"><?= yii::t('app', 'Quantity') ?></th>
                                <th scope="col"><?= yii::t('app', 'Sale Price') ?></th>
                                <th scope="col"><?= yii::t('app', 'Total') ?></th>
                                <th scope="col"><?= yii::t('app', '') ?></th>
                            </tr>
                        </thead>
                        <tbody id="invoice">
                            <?php
                            $total = 0;
                            $models = $dataProvider->getModels();
                            foreach ($models as $data) :

                                $total = $total + $data->salePrice;
                            ?>
                                <tr id=<?= $data->id ?>>
                                    
                                <td><?= $data->id ?></td>

                                    <td data-field='name' data-footer-formatter='nameFormatter'><?= $data->category0->name ?></td>
                                   
                                    <td><?= $data->category0->serialNo ?></td>
                                   
                                    <td ><input id='qyt-<?=$data->id?>'  class='form-control' type='number'  min='1' max='100' name='qyt' value=<?=$data->quantity?> onfocusout='updateQyt(this)' /></td>
                                   
                                    <td id='saleP-<?=$data->id?>' ><?= $data->salePrice ?></td>
                                   
                                    <td id='salePrice-<?=$data->id?>' class='salePrice<?=$data->id?> salePrice' data-field='total' data-footer-formatter='totalFormatter'><?= $data->salePrice  * $data->quantity ?></td>
                                   
                                    <td><button type='button' class='btn btn-danger' onclick='deleteRow(this)'><i class='fa fa-trash' aria-hidden='true'></i></button></td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                
                                
                                <td></td>
                                <td><?=yii::t('app', 'Total') ?></td>
                                <td id="total"><?= $total ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

    </div>




</div>

<?php
$this->registerJs("$(function() {
     $('.popupModal').click(function(e) {
     e.preventDefault();
     $('#modal').modal('show').find('.modal-content')
     .load($(this).attr('href'));
     });
});");

?>
<?php
$this->registerJs("$('#focus_first').select2('focus');"); ?>
<?php $this->registerJs("
    $(function () {
    $('[data-toggle=\"tooltip\"]').tooltip()});", $this::POS_END, 'tooltips'); ?>