<?php

use app\models\Category;
use app\models\Client;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = Yii::t('app', 'تقرير تفصيلي بكميات الإنتظار حسب اسم الصنف');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-index">
    <?php 
      $classData = Category::find()->select(['class'])->distinct()->all();
      $listClass = ArrayHelper::map($classData, 'class', 'class');

      $companyData = Category::find()->select(['company'])->distinct()->all();
      $listCompany = ArrayHelper::map($companyData, 'company', 'company');
    ?>
    <h1><?= Html::encode($this->title) ?></h1>
    <hr>
    <?php Pjax::begin(); ?>
    <?php
    $gridColumn = [
          [ 
            'attribute' => 'client',
            'label' => Yii::t('app', 'Client'),
            //'value' => 'sales.clinet',
            'width' => '310px',
            'filter' => ArrayHelper::map(Client::find()->asArray()->all(), 'id', 'name'),
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'options' => ['prompt' => 'اختيار'],
                'pluginOptions' => ['allowClear' => true],
            ],
            'group' => true,
            'groupedRow' => true, 
            'groupOddCssClass' => 'kv-grouped-row',  // configure odd group cell css class
            'groupEvenCssClass' => 'kv-grouped-row', // configure even group cell css class
        ],

        [
            'attribute' => 'category',
            'label' => Yii::t('app', 'Name'),
            'value' => 'category0.name',
            'width' => '310px',
            'filter' => ArrayHelper::map(Category::find()->asArray()->all(), 'id', 'name'),
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'options' => ['prompt' => 'اختيار'],
                'pluginOptions' => ['allowClear' => true],
            ],
        ],

        [
            'attribute' => Yii::t('app', 'Wait Qnty'),
            'value' => 'waitQnty',
            'filter' => Html::activeTextInput($searchModel, 'waitQnty',['class'=>'form-control','prompt' => 'اختيار ...'])
        ],

        [
            'attribute' => Yii::t('app', 'Serial No'),
            'value' => 'serialNo',
            'filter' => Html::activeTextInput($searchModel, 'serialNo',['class'=>'form-control','prompt' => 'اختيار ...'])
        ],

        [
            'attribute' => Yii::t('app', 'Comm Code'),
            'value' => 'commCode',
            'filter' => Html::activeTextInput($searchModel, 'commCode',['class'=>'form-control','prompt' => 'اختيار ...'])
        ],

        [
            'attribute' => 'company',
            'label' => Yii::t('app', 'Company'),
            'value' => 'company',
            'filter' => ArrayHelper::map(Category::find()->asArray()->all(), 'company', 'company'),
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'options' => ['prompt' => 'اختيار الشركة'],
                'pluginOptions' => ['allowClear' => true],
            ],
        ],
        [
            'attribute' => 'class',
            'label' => Yii::t('app', 'Class'),
            'value' => 'class',
            'filter' => ArrayHelper::map(Category::find()->asArray()->all(), 'class', 'class'),
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'options' => ['prompt' => 'اختيار التصنيف'],
                'pluginOptions' => ['allowClear' => true],
            ],
        ],
    ];
       ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumn,
        'showPageSummary' => false,
        'pjax' => true,
        'striped' => true,
        'hover' => true,
        'panel' => ['type' => 'primary', 'heading' => ''],
        'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
    ]); ?>
    <?php Pjax::end(); ?>

</div>