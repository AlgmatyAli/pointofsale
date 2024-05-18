<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Client;
use app\models\User;
use dosamigos\datepicker\DatePicker;
use app\models\Branches;
use wbraganca\dynamicform\DynamicFormWidget;
use app\models\Category;
use kartik\widgets\DepDrop;
use kartik\file\FileInput;
/* @var $this yii\web\View */
/* @var $model app\models\Sales */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sales-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <div class="row">
      <div class="col-md-1"></div>
      <div class="col-md-3">

        <?= 
        $form->field($model, 'at')->widget(
        DatePicker::className(),
        [
            'value' => '02-16-2012',
            'language' => 'ar',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'todayBtn' => true,
            ]
        ]
        )->label('Invoice Date'); ?>

        <?= $form->field($model, 'paintId')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(User::find()->where(['in', 'isActive', ['active']])->all(),'id', 'username'),
            'language' => 'ar',
            'id'=>'paintId',
            'options' => ['placeholder' => 'الرجاء اختيار اسم الزواق ...'],
            'pluginOptions' => [
                'allowClear' => true,
                'multiple'=>false,
            ],
        ]); ?>

        <?= $form->field($model, 'deleviryId')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(User::find()->where(['in', 'isActive', ['active']])->all(),'id', 'username'),
            'language' => 'ar',
            'id'=>'deleviryId',
            'options' => ['placeholder' => 'الرجاء اختيار اسم مسؤول التسليم ...'],
            'pluginOptions' => [
                'allowClear' => true,
                'multiple'=>false,
          ],
        ]); ?> 

    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'clinet')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Client::find()->where(['in', 'type', [0,2]])->all(),'id', 'name'),
        'language' => 'ar',
        'id'=>'clinet',
        'options' => ['placeholder' => 'الرجاء اختيار اسم العميل ...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple'=>false,
        ],
        ]); ?>
        
        <?= $form->field($model, 'carpenter')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(User::find()->where(['in', 'isActive', ['active']])->all(),'id', 'username'),
        'language' => 'ar',
        'id'=>'carpenter',
        'options' => ['placeholder' => 'الرجاء اختيار اسم النجار ...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple'=>false,
        ],
    ]); ?>

        <?= $form->field($model, 'deleviryAt')->widget(
        DatePicker::className(),
        [
            'language' => 'ar',
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'todayBtn' => true,
                'value' => '2016-06-01',

            ]
        ]
        ) ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'payWay')->dropDownList([ '0' => 'نقدا', '1' => 'آجـــل', '2' => 'دفعة على الحساب', ], ['prompt' => '']) ?>

        <?= $form->field($model, 'upholstered')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(User::find()->where(['in', 'isActive', ['active']])->all(),'id', 'username'),
        'language' => 'ar',
        'id'=>'upholstered',
        'options' => ['placeholder' => 'الرجاء اختيار اسم المنجد ...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple'=>false,
        ],
        ]); ?>
    </div>
    </div>
    <hr>
    <!-- =================== -->
  <div class="row">
    <div class="col-md-1"></div> 
    <!-- =================== -->
    <div class="col-md-8">
    <div class="row">
      <div class="panel panel-info">
        <div class="panel-heading"></div>
        <div class="panel-body">
             <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                'widgetBody' => '.container-items', // required: css class selector
                'widgetItem' => '.item', // required: css class
                'limit' => 999, // the maximum times, an element can be cloned (default 999)
                'min' => 1, // 0 or 1 (default 1)
                'insertButton' => '.add-item', // css class
                'deleteButton' => '.remove-item', // css class
                'model' => $models[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'salesId',
                    'category',
                    'quantity',
                    'costPrice',
                    'salePrice',
                    'box',
                    'expire',
                ],
             ]); ?>
             <div class="container-items"><!-- widgetContainer -->
               <?php foreach ($models as $i => $modelSales) : ?>

                <div class="item panel panel-default">
                    <!-- widgetBody -->
                    <div class="panel-heading">
                        <h5 class="panel-title pull-left">Prices</h5>
                        <div class="pull-right">
                            <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                            <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <?php
                            // necessary for update action.
                        if (!$modelSales->isNewRecord) {
                            echo Html::activeHiddenInput($modelSales, "[{$i}]id");
                        }
                        ?>
                        <div class="row">
                        <div class="col-md-6">
                            <?php 
                             echo $form->field($modelSales, "[{$i}]category")->widget(Select2::classname(), [
                                'data' =>ArrayHelper::map(Category::find()
                                   ->all(),'id', 'name'),
                                'language' => 'ar',
                                'options' => ['placeholder' => 'الرجاء اختيار اسم الصنف ...',
                                //'onchange' => 'getInfo( $(this) )'
                            ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'multiple'=>false,
                                ],
                            ]);
                          ?>
                        </div>
                          <div class="col-md-3">
                           <?= $form->field($modelSales, "[{$i}]quantity")->textInput(['maxlength' => true]) ?>
                          </div>
                          
                          <div class="col-md-3">
                           <?= $form->field($modelSales, "[{$i}]salePrice")->textInput(['maxlength' => true,
                           'onfocusout' => 'totalsSalesCalculate( $(this) )' 
                           ]) ?>  
                          </div>
                          
                    </div>
                    </div> 
            </div>
            <?php endforeach; ?>
             
            <?php DynamicFormWidget::end(); ?>
            </div></div></div></div>
    </div> 
    <!-- =================== -->
    <div class="col-md-3">
     <div class="panel panel-info">
       <div class="panel-heading"></div>
        <div class="panel-body panelx">
            <?= $form->field($model, 'total')->textInput() ?>
        
            <?= $form->field($model, 'paid')->textInput(['maxlength' => true,
                'onfocusout' => 'netTotalSales( $(this) )',
                ]); ?>

            <div class="input-group">
             <label for="male"> Net </label><br>
             <input type="text" value="<?php echo htmlspecialchars($model->total - $model->paid); ?>" class="form-control" id="sales-netTotal" aria-describedby="basic-addon1" readonly = true size="70">
            </div> <br>

            <?= $form->field($model, 'notes')->textarea(['rows' => 3]) ?>
        
            <?= $form->field($model, 'type')->textInput(['readonly' => true, 'value' => $model->isNewRecord ? $_GET['type'] : $model->type]) ?>
                <br>
                <?php if (empty($model->path)) {
                      
                    echo $form->field($model, 'file')->widget(FileInput::classname(),['options' => ['accept' => 'image/*'],]);  
                }else{
                    $allimage[] = Html::img("$model->path",  ['class'=>'file-preview-image']);
                
                    echo $form->field($model, 'file')->widget(FileInput::classname(),['options' => ['accept' => 'image/*'],
                    'pluginOptions' => [
                    'initialPreview'=>[$allimage],
                    'overwriteInitial'=>false],
                    ]
                );    

                }
                ?>
        </div>
        <div class="panel-footer">
          <div class="form-group">
           <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>'.' '.Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
           <?= Html::a('<i class="fa fa-fw fa-window-close"></i>'.' '.Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class'=>'btn btn-danger']) ?>
          </div>
        </div>
     </div> 

    </div> 
    
 </div>
 </div>  
    <?php ActiveForm::end(); ?>
</div>
