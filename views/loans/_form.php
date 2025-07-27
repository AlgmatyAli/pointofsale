<?php

use app\models\Employee;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Loans */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="loans-form">
    <div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'employee')->widget(Select2::class, [
            'data' => ArrayHelper::map(Employee::find()->where(['=', 'state', 0])
                ->all(), 'id', 'name'),
            'language' => 'ar',
            'options' => ['placeholder' => 'الرجاء اختيار اسم الموظف ...'],
            'pluginOptions' => [
                'allowClear' => true,
                'multiple' => false,
            ],
            'pluginEvents' => [
                'select2:select' => 'function(event) { '
                    . 'var employee = event.currentTarget.value;'
                    . 'var price_group = $("#loans-employee").val();'
                    . '$.get("' . Url::to(['emp-salary/get-salary']) . '&employee="+employee, function(data){'
                    . 'var data=$.parseJSON(data);'
                    . 'if(data != null){'
                    . '$("#loans-salary").val(data.salary);'
                    . '$("#loans-lastpay").val(data.value);'
                    . '$("#loans-lastpaydate").val(data.at);'
                    . '}});}',
            ],
        ]); ?>
        <div class='row'>
            <div class='col-md-3'>
                <div class="input-group">
                    <label for="male">قيمة المرتب</label><br>
                    <input type="text" value="<?php
                                                if ($model->id != null) {
                                                    echo htmlspecialchars($model->employee0->salary);
                                                }
                                                ?>" class="form-control" id="loans-salary" aria-describedby="basic-addon1" readonly=true size="50">
                </div>
            </div>
        </div> <br>


        <?= $form->field($model, 'loanValue')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'parts')->textInput(['onfocusout' => 'getKestVal( $(this) )']) ?>

        <?= $form->field($model, 'kestValue')->textInput(['maxlength' => true]) ?>

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

        <?= $form->field($model, 'notes')->textarea(['rows' => 6]) ?>


        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success  ']) ?>
            <?= Html::a('<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-danger  ']) ?>
        </div>
    </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

</div>