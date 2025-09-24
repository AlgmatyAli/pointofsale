<?php

use app\models\Client;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TempInvoice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="temp-back-sale">


    <div class="row">
        <div class="col-md-12">
            <?php
            if (is_null($serial_number)) {
                $client = 1;
            } else {
                $client = $serial_number->serial_number;
            }
            echo Html::button(
                '<i class="fa fa-fw fa-step-forward"></i>' . ' ' . Yii::t('app', 'Complete Sale'),
                ['value' => Url::to(['sales/back-create', 'client' => $client]), 'class' => 'btn btn-danger popup']
            ); ?>

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
    <?php $form = ActiveForm::begin(['id' => 'backSale']) ?>
    <div class="row">
        <div class="col-md-8">
            <?php
            echo $form->field($model, 'client')->widget(Select2::class, [
                'data' =>  ArrayHelper::map(Client::find()
                    ->where(['in', 'type', [0, 2]])
                    ->orderBy('id')->asArray()->all(), 'id', 'name'),
                'options' => ['placeholder' => Yii::t('app', 'Select a client ...'), 'dir' => 'rtl', 'id' => 'client'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label(Yii::t('app', 'اسم الزبون'));
            ?>

            <?php
            $formatJs = <<< 'JS'
   var formatProduct = function (product) {
   if (product.loading) {
    return product.text;
   }
 var markup =
    '<div class="row">' + 
      '<div class="col-sm-7">' +
      '<b style="margin-center:5px">' + product.text + '</b>' + 
      '</div>' +
       '<div class="col-sm-3"><i class="badge badge-primary badge-pill">تاريخ الحركة</i> <span class="label label-danger">'  + product.at + '</span></div>' +
       '<div class="col-sm-2"><i class="badge badge-primary badge-pill">تسلسل الحركة</i> ' + product.id + '</div>' +

      '</div>' +
      '<br>'+
      '<div class="row">' + 
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill">الكمـية</i> ' + '  ' + product.quantity + '</div>' +
      '<div class="col-sm-3"><i class="badge badge-primary badge-pill">سعر البيع</i> ' + product.salePrice + '</div>' +
      '<div class="col-sm-4"><i class="badge badge-primary badge-pill">1 رقم القطعة</i> ' + product.serialNo + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill">الشركة</i> ' + product.company + '</div>' +
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
            echo $form->field($model, 'id')->widget(Select2::class, [
                'language' => 'en',
                'options' => [
                    'placeholder' => Yii::t('app', 'Search...'),
                    'dir' => 'rtl',
                    'multiple' => false,
                    'id' => 'category'
                ],
                'pluginOptions' => [
                    'autofocus' => false,
                    'minimumInputLength' => 1,
                    'ajax' => [
                        'url' => Url::to(['/except/item-list-by-client']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term, client: $("#client").val()}; }'),
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

                var el = $(this).find(":selected");
                 var id = el.val();
                $.get("' . Url::to(['temp-back-sales/get-info']) . '&id="+id, function(data){
                if(data != null){
               $("#tempbacksales-quantity").val(1);
               $("#tempbacksales-saleprice").val(data.salePrice);
               $("#tempbacksales-category").val(data.category);
               $("#tempbacksales-costprice").val(data.costPrice);
               }
           });
       }',
                ],
            ]);
            ?>
        </div>
    </div>
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
            echo $form->field($model, 'salePrice')->textInput(['placeholder' => 'Sale Price']);
            ?>
        </div>
        <div class="col-md-2">
            <?php
            echo $form->field($model, 'category')->hiddenInput(['placeholder' => 'Category'])->label('');
            ?>
            <?php
            echo $form->field($model, 'costPrice')->hiddenInput(['placeholder' => 'Category'])->label('');
            ?>
        </div>
    </div>

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
        // 'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-temp-invoice']],
        'showPageSummary' => true,
    ]); ?>
</div>

<?php
$script = <<< JS
$('form').on('beforeSubmit', function(e) {
    e.preventDefault();
    var form = $(this);
    if (form.find('.has-error').length) {
        return false;
    }
    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        success: function(result) {
            if(result == 'success') {
                // handle success response
                $.pjax.reload({container: '#w0'}); // Reload the grid
                form.trigger('reset');
            } else {
                // handle validation errors
                alert('Error occurred while submitting the form');
                form.trigger('reset');
            }
        },
    });
    return false;
});
JS;
$this->registerJs($script);
?>