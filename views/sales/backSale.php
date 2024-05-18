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
        ) ?>

    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'clinet')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Client::find()
        ->where(['in', 'type', [0,2]])
        ->andWhere(['in', 'id', explode(',', Yii::$app->user->identity->client)])
        ->all(),'id', 'name'),
        'language' => 'ar',
        'id'=>'clinet',
        'options' => ['placeholder' => 'الرجاء اختيار اسم العميل ...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple'=>false,
        ],
        ]); ?>
        
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'payWay')->dropDownList([ '1' => 'نقدا', '2' => 'على الحساب', ], ['prompt' => '']) ?>
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

                <div class="item panel panel-default"><!-- widgetBody -->
                    <div class="panel-heading">
                        <!-- <h5 class="panel-title pull-left">Prices</h5> -->
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
                        <div class="col-md-8">
                       
                            <?php 
                            //  echo $form->field($modelSales, "[{$i}]category")->widget(Select2::classname(), [
                            //     'data' =>ArrayHelper::map(Category::find()
                            //        ->all(),'id', 'name'),
                            //     'language' => 'ar',
                            //     'options' => ['placeholder' => 'الرجاء اختيار اسم الصنف ...',
                            //     'dir' => 'rtl',
                            // ],
                            //     'pluginOptions' => [
                            //         'allowClear' => true,  
                            //     ],
                            // ]);
                          ?>

                          <?= $form->field($modelSales, "[{$i}]category")->widget(Select2::classname(), [
                               'id' => 'categoryx',
                               'data' => \yii\helpers\ArrayHelper::map($data, 'id', 
                                function($model) {
                                    return $model['name'].' --  '.$model['serialNo'];
                                }
                            ),
                                'language' => 'en',
                                'options' => ['placeholder' => Yii::t('app', 'Choose Category'),
                                'dir' => 'rtl',
                                //'multiple'=>true,
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true 
                                ],  
                            ]); ?> 

                        </div>
                          <div class="col-md-2">
                           <?= $form->field($modelSales, "[{$i}]quantity")->textInput(['maxlength' => true]) ?>
                          </div>
                          
                          <div class="col-md-2">
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
             <label for="male"><?=Yii::t('app','Net')?>  </label><br>
             <input type="text" value="<?php echo htmlspecialchars($model->total - $model->paid); ?>" class="form-control" id="sales-netTotal" aria-describedby="basic-addon1" readonly = true size="70">
            </div> <br>

            <?= $form->field($model, 'notes')->textarea(['rows' => 3]) ?>
            <?= $form->field($model, 'type')->hiddenInput(['readonly' => true, 'value' => $model->isNewRecord ? $_GET['type'] : $model->type])->label(false) ?>

        
        </div>
        <div class="panel-footer">
          <div class="form-group">
           <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>'.' '.Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
           <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer , ['class'=> 'btn btn-danger btnx']) ?>
          </div>
        </div>
     </div> 

    </div> 
    
 </div>
 </div>  
    <?php ActiveForm::end(); ?>
</div>
