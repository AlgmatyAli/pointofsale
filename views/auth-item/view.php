<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\AuthItem $model */
/** @var yii\data\ActiveDataProvider $authItemChildProvider */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Auth Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

// نوع العنصر في RBAC: 1 = دور، 2 = صلاحية
$types = [
    1 => ['دور', 'text-bg-primary', 'bi-person-badge'],
    2 => ['صلاحية', 'text-bg-info', 'bi-key'],
];
[$typeText, $typeClass, $typeIcon] = $types[(int) $model->type] ?? [(string) $model->type, 'text-bg-secondary', 'bi-shield'];

$permCount = $authItemChildProvider->getTotalCount();
?>

<div class="auth-item-view">

    <!-- الرأس -->
    <div class="card mb-3 border-primary">
        <div class="card-body d-flex flex-wrap align-items-center gap-3">
            <span class="rounded-circle bg-primary-subtle text-primary-emphasis d-inline-flex align-items-center justify-content-center"
                  style="width:56px;height:56px"><i class="bi <?= $typeIcon ?> fs-3"></i></span>
            <div class="me-auto">
                <h3 class="mb-1"><?= Html::encode($this->title) ?></h3>
                <span class="badge <?= $typeClass ?>"><?= Html::encode($typeText) ?></span>
                <span class="badge text-bg-success"><i class="bi bi-key"></i> <?= (int) $permCount ?> صلاحية</span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <?= Html::a(
                    '<i class="bi bi-pencil-square"></i> ' . Yii::t('app', 'Update'),
                    ['update', 'id' => $model->name],
                    ['class' => 'btn btn-primary']
                ) ?>
                <?= Html::a(
                    '<i class="bi bi-trash"></i> ' . Yii::t('app', 'Delete'),
                    ['delete', 'id' => $model->name],
                    [
                        'class' => 'btn btn-outline-danger',
                        'data' => [
                            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                    ]
                ) ?>
            </div>
        </div>
    </div>

    <!-- الصلاحيات الممنوحة -->
    <div class="card">
        <div class="card-header fw-semibold bg-success-subtle text-success-emphasis">
            <i class="bi bi-shield-check"></i> الصلاحيات
        </div>
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $authItemChildProvider,
                'summary' => '',
                'tableOptions' => ['class' => 'table table-striped table-hover mb-0'],
                'emptyText' => 'لا توجد صلاحيات ممنوحة لهذا الدور',
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'child0.description',
                ],
            ]) ?>
        </div>
    </div>
</div>
