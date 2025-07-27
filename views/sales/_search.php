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

/* @var $this yii\web\View */
/* @var $model app\models\SalesSearch */
/* @var $form yii\widgets\ActiveForm */
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
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'user_insert')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(User::find()
                    ->where(['=', 'isActive', 'active'])
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
                echo $form->field($model, 'clinet')->widget(\kartik\widgets\Select2::classname(), [
                    'data' => \yii\helpers\ArrayHelper::map(\app\models\Client::find()
                        ->where(['in', 'type', [0, 2]])
                        //->andWhere(['branch' => Yii::$app->user->identity->branch])
                        ->andWhere(['in', 'id', explode(',', Yii::$app->user->identity->client)])

                        ->orderBy('id')->asArray()->all(), 'id', 'name'),
                    'options' => [
                        'placeholder' => 'الرجاء اختيار اسم العميل ...'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            } else {
                echo $form->field($model, 'clinet')->widget(\kartik\widgets\Select2::classname(), [
                    'data' => \yii\helpers\ArrayHelper::map(\app\models\Client::find()
                        ->where(['in', 'type', [0, 2]])
                        //->andWhere(['branch' => Yii::$app->user->identity->branch])                       
                        ->orderBy('id')->asArray()->all(), 'id', 'name'),
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
            if (Yii::$app->user->can('can_see_other_branch_sales')) {
                echo  $form->field($model, 'branch')->widget(Select2::classname(), [
                    'value' => [Yii::$app->user->identity->branch],
                    'data' => ArrayHelper::map(Branches::find()
                        //  ->where(['in', 'type', [0,2]])
                        //->andWhere(['branch' => Yii::$app->user->identity->branch])
                        ->all(), 'id', 'name'),
                    'language' => 'ar',
                    'options' => [
                        'value' => Yii::$app->user->identity->branch,
                        //  'placeholder' => 'الرجاء اختيار اسم العميل ...'
                        // 'disabled' => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => false,


                    ],
                ]);
            } else {
                echo   $form->field($model, 'branch')->widget(Select2::classname(), [
                    'value' => [Yii::$app->user->identity->branch],
                    'data' => ArrayHelper::map(Branches::find()
                        //  ->where(['in', 'type', [0,2]])
                        //->andWhere(['branch' => Yii::$app->user->identity->branch])
                        ->all(), 'id', 'name'),
                    'language' => 'ar',
                    'options' => [
                        'value' => Yii::$app->user->identity->branch,
                        //  'placeholder' => 'الرجاء اختيار اسم العميل ...'
                        //'disabled' => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => false,


                    ],
                ]);
            }
            ?>
            <?= $form->field($model, 'type')->widget(Select2::classname(), [
                'data' => [
                    '1' => 'مبيعات', '2' => 'مسترجع مبيعات', '3' => 'فاتورة معلقة', '4' => 'فاتورة مبدئية'
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
               echo $form->field($model, 'currancy')->widget(Select2::classname(), [
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
    <div class="row">
        <div class="col-sm-4">
            <br>
            <div class="form-group">
                <?= Html::submitButton('<i class="fa fa-fw fa-search"></i>' . ' ' . Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-lg']) ?>
                <?= Html::a('<i class="fa fa-fw fa-eraser"></i>' . ' ' . Yii::t('app', "Erase"), Url::toRoute(['index']), ['class' => 'btn btn-danger btn-lg']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>