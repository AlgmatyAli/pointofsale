<?php

use app\models\Currancy;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use app\models\Employee;

/* @var $this yii\web\View */
/* @var $model app\models\EmpSalary */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="emp-salary-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
      <div class="col-md-2"></div>
      <div class="col-md-4">
        
          <?php
           $years = range(2020, date('Y'));
           $years = array_combine($years, $years);
          echo $form->field($model, 'year')->dropDownList($years, 
            ['prompt' => 'الرجاء اختيار السنـة...']
        ) ?>
      </div>
      <div class="col-md-4">
      <?= $form->field($model, 'month')->dropDownList([ '0'=>'اختيار الشهر','1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', 
        '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', ], 
        ['prompt' => 'الرجاء اختيار الشهر...',
        //'onchange' => 'getDrawing( $(this) )'
        ]
        ) ?>
      </div>
    </div>
    <div class="row">
      <div class="col-md-2"></div>
      <div class="col-md-8">

        <?= $form->field($model, 'employee')->widget(Select2::class, [
        'data' => ArrayHelper::map(Employee::find()->where(['=','state',0])
        ->all(),'id', 'name'),
        'language' => 'ar',
        'options' => ['placeholder' => 'الرجاء اختيار اسم الموظف ...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple'=>false,
        ],
        'pluginEvents' => [
            'select2:select' => 'function(event) { '
            . 'var employee = event.currentTarget.value;'
            . 'year = document.querySelector("#empsalary-year");'
            . 'selectedYear = year.options[year.selectedIndex].value;'
            . 'month = document.querySelector("#empsalary-month");'
            . 'selectedMonth = month.options[month.selectedIndex].value;'
            . 'var price_group = $("#empsalary-employee").val();'
            //. '$.get("' . Url::to(['emp-salary/get-salary']) . '&employee="+employee, function(data){'
            . '$.get("index.php?r=emp-salary/get-salary" ,{employee: employee, year: selectedYear, month: selectedMonth}, function(data){'
            //. '$.get("' . Url::to(['emp-salary/get-salary']) . '&employee="+employee, function(data){'

            //. '$.get("' . Url::to(['emp-salary/get-salary']) . '&employee=+employee", function(data){'
            //. 'var data=$.parseJSON(data);'
            . 'if(data != null){'
            . '$("#empsalary-salary").val(data.salary);'
            . '$("#empsalary-lastpay").val(data.lastpay);'
            . '$("#empsalary-totalpay").val(data.value);'
            . '$("#empsalary-lastpaydate").val(data.at);'
            . '$("#empsalary-remaining").val(data.salary - data.value);'
            . '}});}',
        ],
        ]); ?>
        
    <div class='row'>
    <div class='col-md-2'>
    <?= $form->field($model, 'salary')->textInput(['disabled' => true]) ?>
    </div>
    <div class='col-md-2'>
    <?= $form->field($model, 'lastPay')->textInput(['disabled' => true]) ?>
    </div>
    <div class='col-md-3'>
    <?= $form->field($model, 'totalPay')->textInput(['disabled' => true])->label('اجمالي المقبوض بالشهر') ?>
    </div>
    <div class='col-md-2'>
    <?= $form->field($model, 'remaining')->textInput(['disabled' => true])->label('المتبقي') ?>
    </div>
    <div class='col-md-2'>
    <?= $form->field($model, 'lastPayDate')->textInput(['disabled' => true]) ?>
    </div>
    </div>
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

    <?= $form->field($model, 'value')->textInput(['onfocusout' => 'checValue( $(this) )']) ?>

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

    <?= $form->field($model, 'why')->textarea(['rows' =>6]) ?>

    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>'.' '.Yii::t('app', 'Save'), ['class' => 'btn btn-success  ']) ?>
        <?= Html::a('<i class="fa fa-fw fa-window-close"></i>'.' '.Yii::t('app', 'Clear'), ['/emp-salary/create'], ['class'=>'btn btn-danger  ']) ?>
    </div>

    </div>

    <?php ActiveForm::end(); ?>

</div>
