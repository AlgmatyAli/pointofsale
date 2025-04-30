<?php

use app\models\CustomsDeclaration;
use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomsOfficeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Customs Offices');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customs-office-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>
    <p>
        <?= Html::a(Yii::t('app', 'Create Customs Office'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <br>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => '',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => Yii::t('app', 'Custom Name'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->custom->name;
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'customId',
                    ArrayHelper::map(CustomsDeclaration::find()->asArray()->all(), 'id', 'name'),
                    ['class' => 'form-control', 'prompt' => 'اختيار ...']
                ),

            ],
            [
                'label' => Yii::t('app', 'القيـمة'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->value;
                },
            ],

            [
                'label' => Yii::t('app', 'اسم العملة'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->currancy0->name;
                },
            ],

            [
                'label' => Yii::t('app', 'Custom Name'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->custom->name;
                },
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'customId',
                    ArrayHelper::map(CustomsDeclaration::find()->asArray()->all(), 'id', 'name'),
                    ['class' => 'form-control', 'prompt' => 'اختيار ...']
                ),

            ],
            [
                // 'attribute' => 'at',
                'value' => 'at',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'at',
                    'language' => 'ar',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'todayBtn' => true,
                    ]
                ])
            ],
            'why',
            [
                'class' => 'yii\grid\ActionColumn',
                'options' => ['style' => 'width:120px;'],
                'template' => '<div class="btn-group btn-group-sm" role="group" aria-label="...">{view}</div>',
                'buttons' => [
                    'view' => function ($url, $searchModel, $key) {
                        return Html::a('<i class="fa fa-eye"></i>', $url, ['class' => 'btn btn-default']);
                    },
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>