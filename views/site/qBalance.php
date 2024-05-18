<?php

use app\models\Category;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $searchModel app\models\SalesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = Yii::t('app', 'تقرير تفصيلي بكميات الغير مستوية بين كشف الحساب ورصيد الجرد');
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
        'attribute' => 'category',
        'label' => Yii::t('app', 'رقم الصنف'),
        'value' => 'category',
        ],

        [
            'attribute' => 'category',
            'label' => Yii::t('app', 'Name'),
            'value' => 'category0.name',
            'filter' => ArrayHelper::map(Category::find()->asArray()->all(), 'id', 'name'),
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'options' => ['prompt' => 'اختيار'],
                'pluginOptions' => ['allowClear' => true],
            ],
        ],
        
        [
            'attribute' => 'quantity',
            'label' => Yii::t('app', 'الكمية بالجرد الفعلي'),
            'value' => 'quantity',
        ],

        [
            'attribute' => 'tq',
            'label' => Yii::t('app', 'الكمية بكشف الحساب '),
            'value' => 'tq',
        ],

    ];
       ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => '',
        'tableOptions' => [
            'class' => 'table card-table table-vcenter text-nowrap datatable',
        ],
        //     'clientOptions' => [
        //     "lengthMenu"=> [[20,-1], [20,Yii::t('app',"All")]],
        //     "info"=>true,
        //     "responsive"=>true, 
        //     "dom"=> 'lfTrtip',
        //     "deferRender" => true,
        //     "autoWidth" => true,
        //     "tableTools"=>[
        //         "aButtons"=> [ 
        //             [
        //             "sExtends"=> "csv",
        //             "sButtonText"=> Yii::t('app',"Save to CSV")
        //             ],[
        //             "sExtends"=> "xls",
        //             "oSelectorOpts"=> ["page"=> 'current']
        //             ],
        //             [
        //             "sExtends"=> "print",
        //             "sButtonText"=> Yii::t('app',"Print")
        //             ],
        //         ]
        //     ]
        // ],
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-inventory']],
        'panel' => [
            'type' => GridView::TYPE_INFO,
            // 'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title),
        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>