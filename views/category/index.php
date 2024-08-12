<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>


    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php Pjax::begin(); ?>
    <?php
    yii\bootstrap\Modal::begin(['id' => 'modal']);
    yii\bootstrap\Modal::end();
    ?>
    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'grid-id',
        //'filterModel' => $searchModel,
        'summary' => '',
        'rowOptions' => function ($searchModel) {
            if ($searchModel->status == '1') {
                return ['class' => 'danger'];
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'serialNo',
            'commCode',
            'class',
            'company',
            'place',
            'quantity',
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
            //     [
            //         'attribute' => 'category',
            //         'headerOptions' => ['style' => 'width:40%'],
            //         'value' => function ($model) {
            //             return Html::a(Yii::t('app', ' {modelClass}', [
            //                 'modelClass' => 'open',
            //             ]), ['category/update', 'id' => $model->id], ['class' => 'btn btn-default popupModal']);
            //         },
            //         'format' => 'raw',
            //     ],
        ],
    ]);
    ?>

    <?php Pjax::end(); ?>

</div>

<?php
$this->registerJs("$(function() {
     $('.popupModal').click(function(e) {
     e.preventDefault();
     $('#modal').modal('show').find('.modal-content')
     .load($(this).attr('href'));
     });
});");

?>