<?php

use app\models\Currancy;
use app\models\PaymentTypes;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Sales */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sales-form">

    <br>
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="btn-group">
                <?= Html::submitButton(
                    $model->isNewRecord ? Yii::t('app', 'Create') : '<i class="fa fa-fw fa-edit "></i>' . ' ' . Yii::t('app', 'Save'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
                ) ?>
                <?php
                if ($model->type != 2) {
                    echo Html::button(Yii::t('app', 'Fast'), ['value' => Url::to(['fast', 'id' => $model->id]), 'class' => 'btn btn-info popup']);
                }
                ?>
                <?= Html::a('<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Back'), Yii::$app->request->referrer, ['class' => 'btn btn-danger']) ?>
            </div>
            <?= Html::a('<i class="fa fa-fw fa-trash "></i>' . ' ' . Yii::t('app', 'Delete All'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-warning pull-left',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ])
            ?>
        </div>
    </div>
    <div class="row">
        <?php Pjax::begin(['id' => 'pjax-grid-view']); ?>
        <?php //if ($dataProvider->getCount() == 0) { 
        ?>
        <div class="col-md-2 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-fw fa-dollar"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text"><?= Yii::t('app', 'Items Count') ?></span>
                    <span class="info-box-number"><?php echo $dataProvider->getCount() ?><small></small></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <?php  //} 
        ?>


        <?php $count = 0;
        foreach ($dataProvider->getModels() as $dataP) {
            $count = $dataP->quantity + $count;
        }

        //if ($count != 0) { 
        ?>

        <div class="col-md-2 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-fw fa-dollar"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text"><?= Yii::t('app', 'items quantity') ?></span>
                    <span class="info-box-number"><?= $count ?><small></small></span>
                </div>
            </div>
        </div>
        <?php //} 
        ?>


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
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-fw fa-dollar"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text"><?= Yii::t('app', 'Total Invoice') ?></span>
                    <span class="info-box-number"><?= @number_format($total, 3) ?>
                        <br>
                        <?php if (Yii::$app->user->identity->seeCostPrice == 1) { ?>
                            <small><?= @number_format($profit, 3) ?></small>
                        <?php } ?>
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <hr>
        <?php //} 
        ?>
    </div>
    <?php Pjax::end(); ?>
    <hr>
    <?= $form->errorSummary($model); ?>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'type')->dropDownList(['2' => 'مسترجع مبيعات', '1' => 'فاتورة نهائية', '3' => 'فاتورة حجز', '4' => 'فاتورة مبدئية'])->label(yii::t('app', 'Invoice Type')) ?>
            <?php
            if (Yii::$app->user->identity->client <> null) {
                echo $form->field($model, 'clinet')->widget(\kartik\widgets\Select2::class, [
                    'data' =>  ArrayHelper::map(\app\models\Client::find()
                        ->where(['in', 'type', [0, 2]])
                        //->andWhere(['branch' => Yii::$app->user->identity->branch])
                        ->andWhere(['in', 'id', explode(',', Yii::$app->user->identity->client)])

                        ->orderBy('id')->asArray()->all(), 'id', 'name'),
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            } else {
                echo $form->field($model, 'clinet')->widget(\kartik\widgets\Select2::class, [
                    'data' =>  ArrayHelper::map(\app\models\Client::find()
                        ->where(['in', 'type', [0, 2]])
                        //->andWhere(['branch' => Yii::$app->user->identity->branch])                       
                        ->orderBy('id')->asArray()->all(), 'id', 'name'),
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            }
            ?>
            <?= $form->field($model, 'notes')->textInput(['maxlength' => true, 'placeholder' => 'Notes']) ?>
            <?php
            if ($model->type != 2) {
                echo $form->field($model, 'deleviried')->checkbox(['id' => "todayis"]);
            } else {
                echo $form->field($model, 'deleviried')->checkbox(['checked' => true, 'id' => "todayis"]);
            }
            ?>
        </div>
        <div class="col-md-3">
            <?php
            echo '<label class="form-label">تاريخ الفاتورة</label>';
            echo  DatePicker::widget([
                'model' => $model,
                'attribute' => 'at',
                'options' => ['placeholder' => 'Enter date ...'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                ]
            ]);
            ?>
            <br>

            <?php
            if ($model->type != 2) {
                $payWay = ['0' => 'نقدا', '1' => 'آجـــل', '2' => 'دفعة على الحساب',];
            } else {
                $payWay = ['1' => 'نقدا', '2' => 'آجـــل'];
            }
            echo $form->field($model, 'payWay')->dropDownList($payWay, ['prompt' => 'اختيار طريقة الدفع'])
            ?>

            <?php
            echo $form->field($model, 'payment_type')->widget(Select2::class, [
                'data' => ArrayHelper::map(PaymentTypes::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => [
                    'placeholder' => 'الرجاء اختيار  طريقة الدفع ...',
                    'value' => $model->isNewRecord ? 1 : $model->payment_type
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>

            <br>
            <?php echo $form->field($model, 'wholesale')->checkbox(); ?>

        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'total')->textInput(['placeholder' => 'Total']) ?>

            <?= $form->field($model, 'disscount')->textInput(['placeholder' => 'Disscount']) ?>

            <?php
            if (Yii::$app->user->identity->client == NULL) {
                echo $form->field($model, 'paid')->textInput(['placeholder' => 'Paid']);
            } else {
                echo $form->field($model, 'paid')->textInput(['placeholder' => 'Paid', 'disabled' => true]);
            }
            ?>
        </div>
        <div class="col-md-3">
            <?=
            $form->field($model, 'deleviryAt')->widget(
                DatePicker::class,
                [
                    'value' => '02-16-2012',
                    //'id' => 'deleviryAt',
                    'language' => 'ar',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'todayBtn' => true,
                    ]
                ]
            )->label('تاريخ التسليم')
            ?>
            <?php
            echo $form->field($model, 'currancy')->widget(Select2::class, [
                'data' => ArrayHelper::map(Currancy::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>

            <?php
            echo '<label class="form-label">تاريخ الاستحقاق</label>';

            echo DatePicker::widget([
                'model' => $model,
                'attribute' => 'deserving',
                'options' => ['placeholder' => 'Enter Deserving ...'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'todayHighlight' => true,
                ]
            ]);
            ?>

        </div>
        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <br>
    <?php ActiveForm::begin(); ?>

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
      '<div class="col-sm-4">' +
        '<b style="margin-center:5px">' + product.text + '</b>' + 
      '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill">  سعر التكلفة - </i> '+ product.costPrice + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill">  سعر البيع - </i> </i> <span class="label label-danger">' + maxPrice + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> سعر البيع الأدنى - </i> ' + minPrice + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> مكان الصنف - </i> ' + product.place + '</div>' +
      '</div>' +
      '<br>' +
      '<div class="row">' + 
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم القطعة - </i> ' + product.serialNo + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم القطعة التجاري- </i> ' + product.commCode + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم التسلسل - </i> '  + product.id + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill">حالة القطعة</i> ' +product.type+ '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> الكمية = </i> <span class="label label-success">' + '  ' + product.quantity + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> الشركة - </i> <span class="label label-warning">' + product.company + '</div>' +
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
            'language' => 'en',
            'options' => [
                'placeholder' => Yii::t('app', 'Search...'),
                'dir' => 'rtl',
                'onchange' => '
        var salesId= getElementById("sales-id").value;
        $.get( "index.php?r=sales-details/addsale&categoryid="+$(this).val()+"&salesId="+salesId, function( data ) {
              
        });
        ',
            ],
            // $.get( "add?categoryid="+category+"&type="+1, function( data ) {});
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'ajax' => [
                    'url' => Url::to(['/sales/itemlist']),
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
    } else {
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
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill">  سعر التكلفة - </i> '+ product.costPrice + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill">  سعر البيع - </i> ' + product.maxPrice + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> سعر البيع الأدنى - </i> ' + product.minPrice + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> مكان الصنف - </i> ' + product.place + '</div>' +
      '</div>' +
      '<br>' +
      '<div class="row">' + 
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم القطعة - </i> ' + product.serialNo + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم القطعة التجاري- </i> ' + product.commCode + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> رقم التسلسل - </i> '  + product.id + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill">حالة القطعة</i> ' +product.type+ '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> الكمية = </i> ' + '  ' + product.quantity + '</div>' +
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
        //var salesId= getElementById("sales-id").value;
        echo Select2::widget([
            'name' => 'kv-repo-template',
            //'value' => '14719648',
            //'initValueText' => 'kartik-v/yii2-widgets',
            'language' => 'en',
            'options' => [
                'placeholder' => Yii::t('app', 'Search...'),
                'dir' => 'rtl',
                'onchange' => '
        var salesId= getElementById("sales-id").value;
        $.get( "index.php?r=sales-details/addsale&categoryid="+$(this).val()+"&salesId="+salesId, function( data ) {
              
        });
        ',
            ],
            // $.get( "add?categoryid="+category+"&type="+1, function( data ) {});
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'ajax' => [
                    'url' => Url::to(['/sales/itemlist']),
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
    }
    ?>

    <?php ActiveForm::end(); ?>
    <?php
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],

        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => Yii::t('app', 'category'),
            'label' => Yii::t('app', 'ID'),
            'contentOptions' => ['style' => 'font-size:14px;'],
            'headerOptions' => ['style' => 'width:10%'],
        ],

        [
            'attribute' => 'category',
            'headerOptions' => ['style' => 'width:20%'],
            'value' => function ($model) {
                return Html::a(Yii::t('app', ' {modelClass}', [
                    'modelClass' => $model->category0->name,
                ]), ['category/info', 'id' => $model->category0->id], ['class' => 'btn-link popupModal']);
            },
            'format' => 'html',
        ],
        // [ 
        //     'attribute' => 'category',
        //     'format' => 'text',
        //     'value' => 'cat.name',
        //     'contentOptions' => function($model) {
        //        if (Yii::$app->user->can('seeCostPrice')) {
        //         return [
        //             'class' => 'cell-with-tooltip',
        //             'data-toggle' => 'tooltip',
        //             'data-placement' => 'top', // top, bottom, left, right
        //             'data-container' => 'body', // to prevent breaking table on hover
        //             // 'title' => ' Cost Price '. $model->price->costPrice,
        //             // 'value' => $model->category0->name,
        //         ]; 
        //        }else{
        //         return [
        //             'class' => 'cell-with-tooltip',
        //             'data-toggle' => 'tooltip',
        //             'data-placement' => 'top', // top, bottom, left, right
        //             'data-container' => 'body', // to prevent breaking table on hover
        //             // 'title' => ' Minimum Price '. $model->price->minPrice,
        //             // 'value' => $model->category0->name,
        //         ]; 

        //        }

        //     }
        // ],

        [
            'label' => Yii::t('app', 'Serial No'),
            'format' => 'raw',
            'value' => function ($data) {
                return $data->category0->serialNo;
            }
        ],

        'category0.company',

        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'quantity',
            'label' => Yii::t('app', 'quantity'),
            'editableOptions' => [
                'asPopover' => true,
            ],
        ],

        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => Yii::t('app', 'salePrice'),
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
            'width' => '7%',
            'format' => ['decimal', 3],
            'mergeHeader' => true,
            'pageSummary' => true,
            'footer' => true
        ],

        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => Yii::t('app', 'packing'),
            'editableOptions' => [
                'asPopover' => true,
            ],
        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}{transfer}',
            'buttons' => [
                'view' => function ($url, $model, $key) {

                    $url = Url::to(['sales/remove', 'id' => $model['id']]);

                    // return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                    //     'title' => Yii::t('app', 'Asign'),
                    // ]);
                    return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, ['class' => 'btn btn-default']);
                },
                'transfer' => function ($url, $model, $key) {

                    $url = Url::to(['sales/transfer-to-temp-invoice', 'id' => $model['id']]);
                    return Html::a('<i class="glyphicon glyphicon-open"></i>', $url, ['class' => 'btn btn-default']);

                    // return Html::a('<span class="glyphicon glyphicon-open"></span>', $url, [
                    //     'title' => Yii::t('app', 'Asign'),
                    // ]);

                }
            ],
        ],
    ];
    ?>
    <br>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'layout' => '{items}{pager}',
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
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            // 'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title),
        ],
    ]); ?>

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