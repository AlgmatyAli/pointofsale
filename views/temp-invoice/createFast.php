<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\TempInvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use fedemotta\datatables\DataTables;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="temp-invoice-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(); ?>
    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-lg']) ?>
        <?= Html::a('<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-danger btn-lg']) ?>
    </div>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?php
    echo DataTables::widget([
        'summary' => '',
        'formatter' => Yii::$app->formatter,
        'dataProvider' => $dataProvider,
        'clientOptions' => [
            "deferRender" => true,
            "responsive" => true,
            "autoWidth" => true,
            "lengthMenu" => [[50, 100, 250, 500, -1], [50, 100, 250, 500, "All"]],
        ],
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn'],
           
            [
                'label' => Yii::t('app', 'Serial'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->id;
                }
            ],
          
            [
                'label' => Yii::t('app', 'Serial No'),
                'format' => 'raw',
                'contentOptions' => ['style' => 'color: red'],
                'value' => function ($data) {
                    return $data->serialNo;
                }
            ],

            [
                'attribute' => 'category',
                'label' => Yii::t('app', 'Category'),
                'format' => 'raw',
                'value' => 'name',
                'headerOptions' => ['style' => 'width:50%'],
                'contentOptions' => function($model) {
                   if (Yii::$app->user->identity->seeCostPrice == 1) {
                    return [
                        'class' => 'cell-with-tooltip',
                        'data-toggle' => 'tooltip',
                        'style' => 'font-size:14px;',
                        'data-placement' => 'top', // top, bottom, left, right
                        'data-container' => 'body', // to prevent breaking table on hover
                        'title' => ' Cost Price '. $model->costPrice,
                    ];
                   }else{
                    return [
                        'class' => 'cell-with-tooltip',
                        'data-toggle' => 'tooltip',
                        'style' => 'font-size:14px;',
                        'data-placement' => 'top', // top, bottom, left, right
                        'data-container' => 'body', // to prevent breaking table on hover
                        'title' => ' Cost Price '. $model->minPrice,
                    ];
                   }
                }
            ],

            [
                'label' => Yii::t('app', 'Company'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->company;
                }
            ],

            'quantity',
            
            [
                'attribute' => 'textInputValues',
                'label' => 'كمية مطلوبة',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::textInput('TempInvoice[textInputValues]['.$model->id.']', null, ['class' => 'form-control', 'maxlength'=>10,'style'=>'width:40px; background-color: rgb(173, 171, 171); font-color: white']);
                }
            ],
        
            [
                'label' => Yii::t('app', 'Max Price'),
                'format' => 'raw',
                'contentOptions' => ['style' => 'color: red'],
                'value' => function ($data) {
                    return $data->maxPrice;
                }
            ],
            [
                'label' => Yii::t('app', 'Min Price'),
                'format' => 'raw',
                'contentOptions' => ['style' => 'color: brown'],
                'value' => function ($data) {
                    return $data->minPrice;
                }
            ],

        ],
    ]);
    ?>
    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs("$('#focus_first').select2('focus');"); ?>
<?php $this->registerJs("
    $(function () {
    $('[data-toggle=\"tooltip\"]').tooltip()});", $this::POS_END, 'tooltips'); ?>