<?php

use app\models\Employee;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\LoanPaid */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="loan-paid-form">
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
                    . '$.get("' . Url::to(['loans/get-total']) . '&employee="+employee, function(data){'
                    . 'var data=$.parseJSON(data);'
                    . 'if(data != null){'
                    . '$("#loans-total").val(data.loanValue);'
                    . '$("#loans-kest").val(data.kestValue);'
                    . '}});}',
            ],
        ]); ?>

        <div class='row'>
            <div class='col-md-3'>
                <div class="input-group">
                    <label for="male">قيمة السلفة</label><br>
                    <input type="text" value="<?php
                                                if ($model->id != null) {
                                                    echo htmlspecialchars($model->employee0->salary);
                                                }
                                                ?>" class="form-control" id="loans-total" aria-describedby="basic-addon1" readonly=true size="50">
                </div>
            </div>
            <div class='col-md-3'>
                <div class="input-group">
                    <label for="male">قيمة القسط</label><br>
                    <input type="text" value="<?php
                                                if ($model->id != null) {
                                                    echo htmlspecialchars($model->employee0->salary);
                                                }
                                                ?>" class="form-control" id="loans-kest" aria-describedby="basic-addon1" readonly=true size="50">
                </div>
            </div>
        </div> <br>

        <?= $form->field($model, 'loanId')->textInput() ?>

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

        <?= $form->field($model, 'month')->dropDownList(
            [
                '0' => 'اختيار الشهر',
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
                '8' => '8',
                '9' => '9',
                '10' => '10',
                '11' => '11',
                '12' => '12',
            ],
            ['prompt' => 'الرجاء اختيار الشهر...',]
        ) ?>

        <?= $form->field($model, 'year')->dropDownList(
            ['2021' => '2021', '2022' => '2022', '2023' => '2023', '2024' => '2024', '2025' => '2025'],
            ['prompt' => 'الرجاء اختيار السنـة...']
        ) ?>

        <?= $form->field($model, 'notes')->textarea(['maxlength' => true, 'rows' => 6]) ?>

        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . ' ' . Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-lg']) ?>
            <?= Html::a('<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-danger btn-lg']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

</div>