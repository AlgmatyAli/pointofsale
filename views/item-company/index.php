<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $q string */

$this->title = Yii::t('app', 'الشركات المصنّعة');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-company-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>

    <div class="d-flex flex-wrap gap-2 mb-3">
        <?= Html::a('<i class="bi bi-plus-lg"></i> ' . Yii::t('app', 'إضافة شركة'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::beginForm(['index'], 'get', ['class' => 'd-flex gap-2']) ?>
            <?= Html::textInput('q', $q, ['class' => 'form-control', 'placeholder' => Yii::t('app', 'بحث باسم الشركة')]) ?>
            <?= Html::submitButton('<i class="bi bi-search"></i>', ['class' => 'btn btn-primary', 'title' => Yii::t('app', 'Search')]) ?>
        <?= Html::endForm() ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'label' => Yii::t('app', 'اسم الشركة'),
            ],
            [
                'label' => Yii::t('app', 'عدد الأصناف'),
                'value' => 'itemsCount',
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, $row) {
                    return [$action, 'id' => $row['id']];
                },
            ],
        ],
    ]); ?>

</div>
