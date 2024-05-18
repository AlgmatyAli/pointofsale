<?php

use app\models\Branches;
use app\models\User;
use kartik\daterange\DateRangePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FtranSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-ftran-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="col-md-4">
        <?php
     echo '<label class="control-label">تاريخ الفاتورة</label>';
     echo DateRangePicker::widget([
        'model'=>$model,
        'attribute'=>'date_',
        'language' => 'en',
        'convertFormat'=>false,
        'pluginOptions'=>[
            'timePicker'=>false,
            'timePickerIncrement'=>30,
            'locale'=>[
                'format'=>'YYYY-MM-DD'
            ]
        ]
    ]);

    ?>

            <?= $form->field($model, 'description')->textInput(['maxlength' => true, 'placeholder' => 'description']) ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'wared')->textInput(['maxlength' => true, 'placeholder' => 'Wared']) ?>

            <?php echo  $form->field($model, 'branch')->widget(Select2::classname(), [
        'value' => [Yii::$app->user->identity->branch],
     'data' =>ArrayHelper::map(Branches::find()
    //  ->where(['in', 'type', [0,2]])
     //->andWhere(['branch' => Yii::$app->user->identity->branch])
     ->all(),'id', 'name'),
     'language' => 'ar',
     'options' => [
        'value' => Yii::$app->user->identity->branch,
         'placeholder' => 'الرجاء اختيار اسم العميل ...'
        // 'disabled' => true,
        ],
     'pluginOptions' => [
         'allowClear' => true,
         'multiple'=>false,
         
     
     ],
    ]);  ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'sader')->textInput(['placeholder' => 'Sader']) ?>

            <?=  $form->field($model, 'user_insert')->widget(Select2::classname(), [
     'data' => ArrayHelper::map(User::find()
     ->where(['=', 'isActive', 'active'])
     ->all(),'id', 'username'),
     'language' => 'ar',
     'options' => ['placeholder' => 'الرجاء اختيار اسم المستخدم ...'],
     'pluginOptions' => [
         'allowClear' => true,
         'multiple'=>false,
     ],
    ]); ?>
        </div>

    </div>
</div>













<div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
    <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
</div>

<?php ActiveForm::end(); ?>

</div>