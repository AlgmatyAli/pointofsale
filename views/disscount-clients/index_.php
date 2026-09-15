<?php

use app\models\DisscountClients;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\DisscountClientsSearch_ $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Disscount Suppliers');
?>
<div class="disscount-clients-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>
    <?= $this->render('_search_', ['model' => $searchModel]); ?>
    <?php Pjax::begin(); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'summary' => '',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => Yii::t('app', 'Client Name'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->client0->name;
                }
            ],
            'at',
            'value',
            'notes:ntext',
            [
                'class' => ActionColumn::class,
                'template' => '{view}',
                'urlCreator' => function ($action, DisscountClients $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>