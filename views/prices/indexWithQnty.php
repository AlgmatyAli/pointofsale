<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\PricesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\models\Category;
use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('app', 'قائمة الاسعار بالكميات');
$this->params['breadcrumbs'][] = $this->title;
$search = "$('.search-button').click(function(){
	$('.search-form').toggle(1000);
	return false;
});";
$this->registerJs($search);
?>
<div class="prices-index">
    <?php 
      $classData = Category::find()->select(['class'])->distinct()->all();
      $listClass = ArrayHelper::map($classData, 'class', 'class');

      $companyData = Category::find()->select(['company'])->distinct()->all();
      $listCompany = ArrayHelper::map($companyData, 'company', 'company');
    ?>
    <h1><?= Html::encode($this->title) ?></h1><hr>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php //Html::a(Yii::t('app', 'Advance Search'), '#', ['class' => 'btn btn-info search-button']) ?>
    </p>
    <br>
    <div class="search-form" style="display:none">
        <?php // $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    <?php 
    $gridColumn = [
       // ['class' => 'yii\grid\SerialColumn'],
       // ['attribute' => 'id', 'visible' => false],
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
            'attribute' => 'company',
            'label' => Yii::t('app', 'Company'),
            'value' => 'category0.company',
            'filter' => ArrayHelper::map(Category::find()->asArray()->all(), 'company', 'company'),
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'options' => ['prompt' => 'اختيار الشركة'],
                'pluginOptions' => ['allowClear' => true],
            ],
        ],
            'stocks0.quantity',

            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute' => Yii::t('app', 'costPrice'),
                'editableOptions' => [                
                    'asPopover' => true,
                ],
                
                'options' => ['class'=>'form-control', 'placeholder'=>'...'],
                // 'group' => true,
            ],

            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute' => Yii::t('app', 'maxPrice'),
                'editableOptions' => [                
                    'asPopover' => true,
                ],
                
                'options' => ['class'=>'form-control', 'placeholder'=>'...'],
                // 'group' => true,
            ],

            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute' => Yii::t('app', 'minPrice'),
                'editableOptions' => [                
                    'asPopover' => true,
                ],
                
                'options' => ['class'=>'form-control', 'placeholder'=>'...'],
                // 'group' => true,
            ],

            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute' => Yii::t('app', 'minPrice2'),
                'editableOptions' => [                
                    'asPopover' => true,
                ],
                
                'options' => ['class'=>'form-control', 'placeholder'=>'...'],
                // 'group' => true,
            ],

            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute' => Yii::t('app', 'minPrice3'),
                'editableOptions' => [                
                    'asPopover' => true,
                ],
                
                'options' => ['class'=>'form-control', 'placeholder'=>'...'],
                // 'group' => true,
            ],
        
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-prices']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title),
        ],
        
    ]); ?>

</div>
