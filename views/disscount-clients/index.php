<?php

use app\models\DisscountClients;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\DisscountClientsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Disscount Clients');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="disscount-clients-index">

    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <h4 class="mb-0 me-auto"><i class="bi bi-tag"></i> <?= Html::encode($this->title) ?></h4>
    </div>

    <?php Pjax::begin(['id' => 'pjax-disc-clients']); ?>

    <?= $this->render('_search', ['model' => $searchModel]) ?>

    <div class="card">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => '',
            'layout' => "{items}\n<div class='p-2'>{pager}</div>",
            'options' => ['class' => 'table-responsive'],
            'tableOptions' => ['class' => 'table table-hover table-striped align-middle mb-0'],
            'emptyText' => '<div class="text-center text-body-secondary py-4"><i class="bi bi-inbox fs-2 d-block"></i>لا توجد نتائج مطابقة</div>',
            'emptyTextOptions' => ['tag' => 'div'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'label' => Yii::t('app', 'Client Name'),
                    'format' => 'raw',
                    'value' => fn($data) => Html::a(Html::encode((string) $data->client0?->name), ['view', 'id' => $data->id], ['class' => 'fw-semibold text-decoration-none']),
                ],
                ['attribute' => 'at', 'contentOptions' => ['class' => 'text-nowrap']],
                ['attribute' => 'value', 'contentOptions' => ['class' => 'fw-semibold text-nowrap']],
                [
                    'attribute' => 'notes',
                    'format' => 'raw',
                    'value' => fn($data) => Html::tag('span', Html::encode(mb_strimwidth((string) $data->notes, 0, 80, '…')), ['title' => (string) $data->notes]),
                ],
                [
                    'class' => ActionColumn::class,
                    'template' => '{view}',
                    'contentOptions' => ['class' => 'text-center', 'style' => 'width:3.5rem'],
                    'buttons' => [
                        'view' => fn($url, $model, $key) => Html::a('<i class="bi bi-eye"></i>', $url, ['class' => 'btn btn-sm btn-outline-secondary', 'title' => 'عرض']),
                    ],
                    'urlCreator' => function ($action, DisscountClients $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->id]);
                    },
                ],
            ],
        ]) ?>
    </div>

    <?php Pjax::end(); ?>

</div>
