<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;
use app\models\Branches;
use app\models\User;
use app\models\Currancy;
use app\models\PaymentTypes;

/** @var yii\web\View $this */
/** @var app\models\Sales $model */
?>

<div class="sales-search">


    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($model, 'billId') ?>

            <?php
            echo '<label class="control-label">تاريخ الفاتورة</label>';
            echo DateRangePicker::widget([
                'model' => $model,
                'attribute' => 'at',
                'language' => 'en',
                'convertFormat' => false,
                'pluginOptions' => [
                    'timePicker' => false,
                    'timePickerIncrement' => 30,
                    'locale' => [
                        'format' => 'YYYY-MM-DD'
                    ]
                ]
            ]);

            ?><br>

            <?php
            echo $form->field($model, 'payment_type')->widget(Select2::class, [
                'data' => ArrayHelper::map(PaymentTypes::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'options' => [
                    'placeholder' => 'الرجاء اختيار  طريقة الدفع ...',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]);
            ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'user_insert')->widget(Select2::class, [
                // من لا يملك userCanSeeOtherUsersSales لا يرى إلا نفسه (والخادم يفرض ذلك أيضاً)
                'data' => ArrayHelper::map(User::find()
                    ->where(['=', 'isActive', 'active'])
                    ->andFilterWhere(['id' => Yii::$app->user->can('userCanSeeOtherUsersSales') ? null : Yii::$app->user->identity->id])
                    ->all(), 'id', 'username'),
                'language' => 'ar',
                'options' => ['placeholder' => 'الرجاء اختيار اسم المستخدم ...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                ],
            ]); ?>


            <?php
            if (Yii::$app->user->identity->client <> null) {
                echo $form->field($model, 'clinet')->widget(\app\widgets\LookupSelect2::class, [
                    'lookup' => 'client', 'types' => [0, 2],
                    'options' => [
                        'placeholder' => 'الرجاء اختيار اسم العميل ...'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            } else {
                echo $form->field($model, 'clinet')->widget(\app\widgets\LookupSelect2::class, [
                    'lookup' => 'client', 'types' => [0, 2],
                    'options' => [
                        'placeholder' => 'الرجاء اختيار اسم العميل ...'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            }
            ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'payWay')->dropDownList(['0' => 'نقدا', '1' => 'آجـــل', '2' => 'دفعة على الحساب',], ['prompt' => 'اختيار طريقة الدفع']) ?>
            <?= $form->field($model, 'deleviried')->dropDownList(['0' => 'لم يتم التسليم', '1' => 'تم التسليم'], ['prompt' => 'اختيار حالة الفاتورة']) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, "phone")->textInput(['maxlength' => true]) ?>

            <?php
            echo '<label class="control-label">تاريخ التسليم</label>';
            echo DateRangePicker::widget([
                'model' => $model,
                'attribute' => 'deleviryAt',
                'language' => 'en',
                'convertFormat' => false,
                'pluginOptions' => [
                    'timePicker' => false,
                    'timePickerIncrement' => 30,
                    'locale' => [
                        'format' => 'YYYY-MM-DD'
                    ]
                ]
            ]);

            ?><br>

        </div>
        <div class="col-sm-2">

            <?php
            // الفرع: يبقى المختار في البحث ظاهراً. من لا يملك can_see_other_branch_sales يُثبَّت على فرعه (والخادم يفرض ذلك)
            $canSeeOtherBranches = Yii::$app->user->can('can_see_other_branch_sales');
            echo $form->field($model, 'branch')->widget(Select2::class, [
                'data' => ArrayHelper::map(Branches::find()->all(), 'id', 'name'),
                'language' => 'ar',
                'disabled' => !$canSeeOtherBranches,
                'pluginOptions' => [
                    'allowClear' => $canSeeOtherBranches,
                    'multiple' => false,
                ],
            ]);
            ?>
            <?= $form->field($model, 'type')->widget(Select2::class, [
                'data' => [
                    '1' => 'مبيعات',
                    '2' => 'مسترجع مبيعات',
                    '3' => 'فاتورة حجز',
                    '4' => 'فاتورة مبدئية'
                ],
                'language' => 'ar',
                'options' => [
                    //  'placeholder' => 'الرجاء اختيار اسم العميل ...'
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,


                ],
            ]); ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'wholesale')->dropDownList(['0' => 'بيع عادي', '1' => 'بيع بجملة الجملة'], ['prompt' => 'اختيار حالة البيـع']) ?>

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
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-sm-4">

            <div class="form-group">
                <?= Html::submitButton('<i class="bi bi-search"></i>' . ' ' . Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-lg']) ?>
                <?= Html::a('<i class="bi bi-eraser"></i>' . ' ' . Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger btn-lg']) ?>
            </div>
        </div>
    </div>
    <br>
    <?php ActiveForm::end(); ?>

</div>