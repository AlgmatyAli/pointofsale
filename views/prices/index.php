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

// البحث المتقدم يُطوى بـ Bootstrap collapse (بدل jQuery toggle)
?>
<div class="prices-index">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h3 class="mb-0"><i class="bi bi-tags"></i> <?= Html::encode($this->title) ?></h3>
        <button class="btn btn-outline-info" type="button" data-bs-toggle="collapse"
                data-bs-target="#prices-search" aria-expanded="false" aria-controls="prices-search">
            <i class="bi bi-funnel"></i> <?= Yii::t('app', 'Advance Search') ?>
        </button>
    </div>

    <div id="prices-search" class="collapse mb-3">
        <div class="card card-body">
            <?= $this->render('_search', ['model' => $searchModel]) ?>
        </div>
    </div>


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
        'filterType' => \app\widgets\LookupSelect2::class,
        'filter' => [],
        'filterWidgetOptions' => [
            'lookup' => 'category',
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => Yii::t('app', 'Category'), 'id' => 'grid-prices-search-category'],
    ],

    'category0.company',

    'category0.serialNo',

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
