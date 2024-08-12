<?php

use app\models\base\Currancy;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Client;
use app\models\ShippingType;
use kartik\date\DatePicker;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\Purchases */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="purchases-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="form-group">
        <div class="btn-group">
            <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            <?= Html::a(
                '<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Back'),
                Yii::$app->request->referrer,
                ['class' => 'btn btn-danger']
            ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'id')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'type')->dropDownList([
                '1' => 'فاتورة مشتريات',
                '2' => 'فاتورة مسترجع مشتريات',
                '3' => 'فاتورة مشتريات معلقة'
            ]) ?>
        </div>
    </div>


    <div class="row">

        <div class="col-md-3">
            <?php
            echo $form->field($model, 'clinet')->widget(Select2::class, [
                'data' => ArrayHelper::map(Client::find()
                    ->where(['in', 'type', [1, 2]])
                    ->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => ['placeholder' => 'الرجاء اختيار اسم العميل ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>

            <?= $form->field($model, 'clientBill')->textInput(['maxlength' => true]) ?>

            <?php
            echo $form->field($model, 'currancy')->widget(Select2::class, [
                'data' => ArrayHelper::map(Currancy::find()
                    ->all(), 'id', 'name'),
                'language' => 'ar',
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>

        </div>
        <div class="col-md-3">
            <?php
            echo $form->field($model, 'at')->widget(
                DatePicker::class,
                [
                    'language' => 'ar',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'todayBtn' => true,
                    ]
                ]
            );
            ?>

            <?= $form->field($model, 'totalCost')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'total_currancy')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'payWay')->dropDownList(
                [
                    '0' => 'نقدا',
                    '1' => 'آجـــل',
                    '2' => 'دفعة على الحساب',
                ],
                ['prompt' => '']
            ) ?>

            <?= $form->field($model, 'total')->textInput() ?>

            <?= $form->field($model, 'paid')->textInput([
                'maxlength' => true,
                'onfocusout' => 'netTotalsPurchases( $(this) )',
            ]); ?>
        </div>

        <div class="col-md-3">
            <?php
            echo $form->field($model, 'dateOfArrival')->widget(
                DatePicker::class,
                [
                    'language' => 'ar',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'todayBtn' => true,
                    ]
                ]
            );
            ?>

            <?php
            echo $form->field($model, 'shippingType')->widget(Select2::class, [
                'data' => ArrayHelper::map(ShippingType::find()
                    ->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => ['placeholder' => 'الرجاء اختيار اسم طريقة الشحن ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,


                ],
            ]);
            ?>
            <br>
            <?php echo $form->field($model, 'changeSalePrice')->checkbox() ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'notes')->textarea(['rows' => 3, 'columns' => 6]) ?>
        </div>
    </div>

</div>
<br>

<?php ActiveForm::end(); ?>
</div>
<br>

<?php ActiveForm::begin(); ?>


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
      '</div>'+
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill">  سعر البيع - </i> ' + product.maxPrice + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> سعر البيع الأدنى - </i> ' + product.minPrice + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> مكان الصنف - </i> ' + product.place + '</div>' +
      '</div>'+
      '<br>'+
      '<div class="row">'+
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
echo Select2::widget([
    'name' => 'kv-repo-template',
    'language' => 'en',
    'options' => [
        'placeholder' => Yii::t('app', 'Search...'),
        'dir' => 'rtl',
        'onchange' => '
        var purchasesId= getElementById("purchases-id").value;
        $.get( "index.php?r=purchases-details/add-purchases&categoryid="+$(this).val()+"&purchasesId="+purchasesId, function(data){
        });
        ',
    ],
    'pluginOptions' => [
        'allowClear' => true,
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

<?php ActiveForm::end(); ?>
<br>
<?php
$gridColumn = [
    ['class' => 'yii\grid\SerialColumn'],
    'category0.id',
    ['attribute' => 'id', 'visible' => false],
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
    'category0.company',

    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => Yii::t('app', 'costPrice'),
        'editableOptions' => [
            'asPopover' => true,
        ],

    ],

    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => Yii::t('app', 'totalCost'),
        'label' => Yii::t('app', 'Cost Total'),
        'editableOptions' => [
            'asPopover' => true,
        ],

    ],

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
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => Yii::t('app', 'salePrice_'),
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
            return $widget->col(5, $p) * $widget->col(7, $p);
        },
        'headerOptions' => ['class' => 'kartik-sheet-style'],
        'hAlign' => 'right',
        'format' => ['decimal', 3],
        'mergeHeader' => true,
        'pageSummary' => true,
        'footer' => true

    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{remove}{transfer}',
        'buttons' => [
            'remove' => function ($url, $model, $key) {

                $url = Url::to(['purchases/remove', 'id' => $model['id']]);
                return Html::a(
                    '<span class="glyphicon glyphicon-trash"></span>',
                    $url,
                    ['class' => 'btn btn-default']
                );
            },
            'transfer' => function ($url, $model, $key) {

                $url = Url::to(['purchases/transfer-to-temp-invoice', 'id' => $model['id']]);
                return Html::a(
                    '<i class="glyphicon glyphicon-open"></i>',
                    $url,
                    ['class' => 'btn btn-default']
                );
            }
        ],

    ],
];
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
        ]
    ],
    'showPageSummary' => true,
]); ?>

<?php
$this->registerJs("$(function() {
     $('.popupModal').click(function(e) {
     e.preventDefault();
     $('#modal').modal('show').find('.modal-content')
     .load($(this).attr('href'));
     });
});");

?>