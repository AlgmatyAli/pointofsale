<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model app\models\TempTransferItems */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="temp-transfer-items-form">
    <div class="row">
        <div class="col-md-6" style="margin-top: 15px">
            <?php echo Html::button(
                '<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Save'),
                [
                    'value' => Url::to(['transfer-items/create']), 'class' => 'btn btn-danger popup'
                ]
            ); ?>

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
    <br><br>
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-12">
            <?= $form->errorSummary($model); ?>
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
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> الشركة - </i> ' + product.company + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> الكمية = </i> ' + '  ' + product.quantity + '</div>' +
      '<div class="col-sm-2"><i class="badge badge-primary badge-pill"> - </i> <span class="label label-info"> ' + product.BRNAME + '</div>' +
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
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'quantity')->textInput(['maxlength' => true, 'placeholder' => 'Quantity']) ?>
        </div>
    </div>
    <br>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-fw fa-plus"></i>' . ' ' . Yii::t('app', 'Add') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php
    $gridColumn = [
        ['attribute' => 'id', 'visible' => false],

        [
            'label' => Yii::t('app', 'ID'),
            'headerOptions' => ['style' => 'width:10%'],
            'value' => function ($data) {
                return $data->category;
            },

        ],

        [
            'attribute' => Yii::t('app', 'Category'),
            'headerOptions' => ['style' => 'width:30%'],
            'value' => function ($data) {
                return Html::a(Yii::t('app', ' {modelClass}', [
                    'modelClass' => $data->category0->name,
                ]), ['category/info', 'id' => $data->category0->id], ['class' => 'btn-link popupModal']);
            },
            'format' => 'raw',
        ],

        [
            'label' => Yii::t('app', 'Serial No'),
            'headerOptions' => ['style' => 'width:20%'],
            'value' => function ($data) {
                return $data->category0->serialNo;
            },

        ],

        [
            'label' => Yii::t('app', 'Company'),
            'headerOptions' => ['style' => 'width:10%'],
            'value' => function ($data) {
                return $data->category0->company;
            },

        ],

        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'quantity',
            'headerOptions' => ['style' => 'width:10%'],
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

<?php
// $this->registerJs("$(function() {
//      $('.popupModal').click(function(e) {
//      e.preventDefault();
//      $('#modal').modal('show').find('.modal-content')
//      .load($(this).attr('href'));
//      });
// });");

?>