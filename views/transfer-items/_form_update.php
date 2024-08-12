<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\models\Branches;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\TransferItems */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transfer-items-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <div class="btn-group">
                <?= Html::submitButton(
                    $model->isNewRecord ? Yii::t('app', 'Create') : '<i class="fa fa-fw fa-edit "></i>' . ' ' . Yii::t('app', 'Save'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
                ) ?>
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
</div>
    <br>
    <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
    <div class="row">
    <div class="col-md-1"></div>
        <div class="col-md-3">
            <?php
            echo $form->field($model, 'fromBranch')->widget(Select2::class, [
                'data' => ArrayHelper::map(Branches::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => ['placeholder' => 'اختيار اسم الفرع المسحوب منه...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>
        </div>
        <div class="col-md-3">
            <?php
            echo $form->field($model, 'toBranch')->widget(Select2::class, [
                'data' => ArrayHelper::map(Branches::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => ['placeholder' => 'اختيار اسم الفرع المودع له...'],
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
        </div>
    </div>
        <?php ActiveForm::end(); ?>
        <br>
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-10">
        <?php $form = ActiveForm::begin(); ?>

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
    echo Select2::widget([
        'name' => 'kv-repo-template',
        'language' => 'en',
        'options' => [
            'placeholder' => Yii::t('app', 'Search...'),
            'dir' => 'rtl',
            'onchange' => '
        var transferId= getElementById("transferitems-id").value;
        $.get( "index.php?r=transfer-items/add&categoryid="+$(this).val()+"&transfer="+transferId, function( data ) {
              
        });
        ',
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 1,
            'ajax' => [
                'url' => Url::to(['/sales/itemlist']),
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

            ['attribute' => 'id', 'visible' => false],

            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => Yii::t('app', 'category'),
                'label' => Yii::t('app', 'ID'),
                'contentOptions' => ['style' => 'font-size:14px;'],
                'headerOptions' => ['style' => 'width:15%'],
            ],

            [
                'label' => Yii::t('app', 'رقم تسلسل الصنف'),
                'contentOptions' => ['style' => 'font-size:14px;'],
                'headerOptions' => ['style' => 'width:15%'],
                'value' => function ($data) {
                    return $data->category0->serialNo;
                }
    
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
                'template' => '{view}{transfer}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
    
                        $url = Url::to(['transfer-items/remove', 'id' => $model['id']]);
                        return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, ['class' => 'btn btn-default']);
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