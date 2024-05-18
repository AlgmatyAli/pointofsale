<?php

use app\models\CustomsDeclaration;
use dosamigos\datepicker\DateRangePicker;
use kartik\daterange\DateRangePicker as DaterangeDateRangePicker;
use kartik\popover\PopoverX;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ShipmentDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Shipment Datas');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shipment-data-index">

  <h1><?= Html::encode($this->title) ?></h1>
  <hr>

  <p>
    <?php //Html::a(Yii::t('app', ''), ['create'], ['class' => 'btn btn-success']) 
    ?>
    <?php echo Html::button('<i class="fa fa-fw fa-fast"></i>' . ' ' . Yii::t('app', 'Create Shipment Data'), ['value' => Url::to(['create']), 'class' => 'btn btn-success popup']); ?>

  </p>
  <br>
  <?php Pjax::begin(); ?>
  <?php // echo $this->render('_search', ['model' => $searchModel]); 
  ?>

  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      ['class' => 'yii\grid\SerialColumn'],
      'shipmentId',

      [
        'label' => Yii::t('app', 'Custom Name'),
        'format' => 'raw',
        'value' => function ($data) {
          return $data->custom->name;
        },
        'filter' => Html::activeDropDownList(
          $searchModel,
          'customsOffice',
          ArrayHelper::map(CustomsDeclaration::find()->asArray()->all(), 'id', 'name'),
          ['class' => 'form-control', 'prompt' => 'اختيار ...']
        ),

      ],

      [
        'label' => Yii::t('app', 'اسم العملة'),
        'format' => 'raw',
        'value' => function ($data) {
          return $data->currancy0->name;
        },
      ],

      [
        'label' => Yii::t('app', 'القيـمة'),
        'format' => 'raw',
        'value' => function ($data) {
          return $data->value;
        },
      ],

      [
        'attribute' => 'at',
        'value' => 'at',
        'label' => Yii::t('app', 'At'),
        'headerOptions' => ['style' => 'width:20%'],
        'filter' => DaterangeDateRangePicker::widget([
          'model' => $searchModel,
          'attribute' => 'created_at',
          'language' => 'en',
          'convertFormat' => false,
          'pluginOptions' => [
            'timePicker' => false,
            'timePickerIncrement' => 30,
            'locale' => [
              'format' => 'YYYY-MM-DD'
            ]
          ]
        ])
      ],
      
      'size',
      'country',
      // 'type',
      //'notes',
      //'created_by',
      //'created_at',
      //'updated_by',
      //'updated_at',
      [
        'format' => 'raw',
        'value' => function ($model) {
          return PopoverX::widget([
            'header' => 'منظومة  المبيعات',
            'size' => PopoverX::SIZE_MEDIUM,
            'type' => PopoverX::TYPE_SUCCESS,
            'placement' => PopoverX::ALIGN_RIGHT,
            'content' => $model['notes'],
            // 'footer' => Html::button('Submit', ['class'=>'btn btn-sm btn-primary']),
            'toggleButton' => ['label' => 'عرض المحتوى', 'class' => 'btn btn-success btn-outline-secondary btn-sm'],
          ]);
        },
      ],
      [
        'class' => 'yii\grid\ActionColumn',
        'options' => ['style' => 'width:120px;'],
        'template' => '<div class="btn-group btn-group-sm" role="group" aria-label="...">{view}{update}</div>',
        'buttons' => [
          'view' => function ($url, $searchModel, $key) {
            return Html::a('<i class="fa fa-eye"></i>', $url, ['class' => 'btn btn-default']);
          },
          'update' => function ($url, $searchModel, $key) {
            return Html::a('<i class="fa fa-edit"></i>', $url, ['class' => 'btn btn-default']);
          },


        ]
      ],
    ],
  ]); ?>

  <?php Pjax::end(); ?>

</div>