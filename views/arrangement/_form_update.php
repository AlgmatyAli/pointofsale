<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use kartik\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model app\models\Arrangement */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="arrangement-form">

    <?php $form = ActiveForm::begin(); ?>

     <?php 
       echo $form->field($model, 'at')->widget(
        DatePicker::className(),
        [
            'language' => 'ar',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'todayBtn' => true,
            ]
        ]
        );
    ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <br>
     <?php 
         $gridColumn = [
            ['class' => 'yii\grid\SerialColumn'],
    
            ['attribute' => 'id', 'visible' => false],
    
            'category0.serialNo',
    
            [
                'attribute' => 'category',
                'headerOptions' => ['style' => 'width:30%'],
                'value' => function ($model) {
                    return Html::a(Yii::t('app', ' {modelClass}', [
                        'modelClass' => $model->category0->name,
                    ]), ['category/change-place', 'id' => $model->category0->id], ['class' => 'btn-link popupModal']);
                },
                'format' => 'raw',
            ],
    
            [
                'attribute' => 'category0.place',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->category0->place == null) {
                        return "<i class='fa fa-minus'></i>";; // "x" icon in red color
                    } else {
                        return $model->category0->place;
                    }
                },
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
    
            'type',
    
            [
                'attribute' => 'stockTaking',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->stockTaking === 1) {
                        return "<i class='fa fa-check'></i>";; // "x" icon in red color
                    } else {
                        return "<i class='fa fa-times'></i>";; // check icon 
                    }
                },
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
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-temp-transfer-items']],
        'showPageSummary' => true,        
    ]); ?>

</div>
