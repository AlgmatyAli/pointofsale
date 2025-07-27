<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Employees');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employee-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?><br>
    <?php Pjax::begin(['id' => 'grid-id']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'summary' => '',
        'rowOptions' => function ($searchModel) {
            if ($searchModel->state == '1') {
                return ['class' => 'danger'];
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'salary',
            'dayOfWork',
            'salaryByDay',
            'startWork',
            'notes',
            [
                'attribute' => 'state',
                'format' => 'raw',
                'value' => function ($searchModel) {
                    return $searchModel->state == 1 ? '<span class="label label-danger">موقوف</span>' : '<span class="label label-success">يعمل</span>';
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'options' => ['style' => 'width:120px;'],
                'template' => '<div class="btn-group btn-group-sm" role="group" aria-label="...">{view}</div>',
                'buttons' => [
                    'view' => function ($url, $searchModel, $key) {
                        return Html::a('<i class="fa fa-eye"></i>', $url, ['class' => 'btn btn-default']);
                    },
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>