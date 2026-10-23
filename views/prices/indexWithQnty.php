<?php

/** @var yii\web\View $this */
/** @var app\models\PricesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

use app\models\Category;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Prices');
$this->params['breadcrumbs'][] = $this->title;

// سعر التكلفة يظهر فقط لمن يملك صلاحية seeCostPrice (الشرط نفسه المستخدم في بقية الصفحات)
$canSeeCost = Yii::$app->user->identity->seeCostPrice == 1;

// تحرير داخل الخلية (بدل النافذة المنبثقة) بتنسيق Bootstrap 5
$editable = [
    'asPopover' => false,
    'bsVersion' => '5.x',
    'submitButton' => ['icon' => '<i class="bi bi-check-lg"></i>'],
    'resetButton' => ['icon' => '<i class="bi bi-arrow-counterclockwise"></i>'],
];

?>
<div class="prices-index">

    <h3 class="mb-3"><i class="bi bi-tags"></i> <?= Html::encode($this->title) ?></h3>

    <?php
    $gridColumns = [
        ['class' => 'yii\grid\SerialColumn'],

        ['attribute' => 'id', 'visible' => false],

        [
            'attribute' => 'category',
            'label' => Yii::t('app', 'Category'),
            'value' => function ($model) {
                return $model->category0?->name;
            },
            'width' => '30%',
            'filterType' => \app\widgets\LookupSelect2::class,
            'filter' => [],
            'filterWidgetOptions' => [
                'lookup' => 'category',
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => Yii::t('app', 'Category'), 'id' => 'grid-prices-search-category'],
        ],

        [
            'attribute' => 'company',
            'label' => Yii::t('app', 'Company'),
            'width' => '10%',
            'value' => function ($model) {
                return $model->category0?->company;
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => ArrayHelper::map(Category::find()->select('company')->distinct()->asArray()->all(), 'company', 'company'),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            // في الأصل كان المفتاح 'company' بدل 'id' (خطأ إملائي)، فلم يُطبَّق المعرّف
            'filterInputOptions' => ['placeholder' => Yii::t('app', 'Company'), 'id' => 'grid-prices-search-company'],
        ],

        [
            'attribute' => 'category0.serialNo',
            'label' => Yii::t('app', 'Serial No'),
            'width' => '20%',
            'value' => function ($model) {
                return $model->category0->serialNo;
            },
            // 'contentOptions' => ['class' => 'fw-semibold'],
        ],

        [
            'attribute' => 'stocks0.quantity',
            'label' => Yii::t('app', 'quantity'),
            'value' => function ($model) {
                return $model->stocks0?->quantity;
            },
            'contentOptions' => ['class' => 'fw-semibold'],
        ],

        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'costPrice',
            'editableOptions' => $editable,
            'visible' => $canSeeCost,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'maxPrice',
            'editableOptions' => $editable,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'minPrice',
            'editableOptions' => $editable,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'minPrice2',
            'editableOptions' => $editable,
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'minPrice3',
            'editableOptions' => $editable,
        ],
    ];
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $gridColumns,
        'bordered' => false,
        'striped' => true,
        'condensed' => true,
        'hover' => true,
        'responsive' => true,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-prices']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<i class="bi bi-book"></i> ' . Html::encode($this->title),
        ],
    ]) ?>

</div>