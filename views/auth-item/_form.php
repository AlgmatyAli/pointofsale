<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AuthItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-item-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <br>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-1"></div>

        <div class="col-md-11">

            <div class="col-xs-12 col-lg-12 no-padding">
                <div id="root-container-id" class="col-xs-12 col-sm-12 col-lg-12">

                    <?php
                    if ($giving != null) {
                        foreach ($giving as $key => $value) {
                            $result[] = ($value);
                        }
                        $model->permission = $result;
                    }

                    echo Html::checkbox(null, false, [
                        'label' => Yii::t('app', 'Check All'),
                        'class' => 'check-all',
                    ]);

                    echo $form->field($model, 'permission')->checkboxList($userPermission, [
                        'item' => function ($index, $label, $name, $checked, $value) {
                            $checked = $checked ? 'checked' : '';
                            return "<label class='checkbox col-md-4' style='font-weight: normal;'><input type='checkbox' {$checked} name='{$name}' value='{$value}'>{$label}</label>";
                        },
                    ])->label(false)
                    //->label(Yii::t('app', '') . ' <label><input type="checkbox" id="root-container-id" class="checkbox">Check All</label>');
                    ?>

                </div>
            </div>

            <?php

            // $options = [
            //     'multiple' => true,
            //     'size' => 30,
            //     'selection' => ["categoryHistrans"],
            // ];
            // echo DualListbox::widget([

            //     'model' => $model,
            //     'attribute' => 'permission',
            //     'items' => $userPermission,
            //     'options' => $options,
            //     'clientOptions' => [
            //         'moveOnSelect' => false,
            //         'selectedListLabel' => 'الصلاحيات الممنوحة',
            //         'nonSelectedListLabel' => 'الصلاحيات والأدونات',
            //     ],
            // ]);
            ?>

            <div class="form-group">
                <br><br>
                <?= Html::submitButton('<i class="fa fa-fw fa-save"></i>' . '' . Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-lg']) ?>

                <?= Html::a('<i class="fa fa-fw fa-window-close"></i>' . ' ' . Yii::t('app', 'Cancel'), ['/auth-item/index'], ['class' => 'btn btn-primary btn-lg']) ?>
            </div>
        </div>
        <div class="col-md-4"></div>
        <div class="col-md-2"></div>
    </div>
    <?php ActiveForm::end(); ?>

</div>