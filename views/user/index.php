<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */

/** @var app\models\UserSearch $searchModel */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
?>
<div class="user-index">

    <br>
    <h1><?= $this->title = Yii::t('app', 'Users'); ?></h1>
    <hr>
    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <div id='div1' class="site-about">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => '',
            'rowOptions' => function ($searchModel) {
                if ($searchModel->isActive == 'deActive') {
                    return ['class' => 'danger'];
                }
            },

            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                // 'id',
                'username',
                'phone',
                'email',
                'isActive',
                [
                    'label' => Yii::t('app', 'Permission'),
                    'format' => 'raw',
                    'value' => function ($data) {
                        return \yii\helpers\Html::encode($data->auth->name);
                    }
                ],

                [
                    'label' => Yii::t('app', 'Br ID'),
                    'format' => 'raw',
                    'value' => function ($data) {
                        return \yii\helpers\Html::encode($data->br->name);
                    }
                ],

                [
                    'class' => 'yii\grid\ActionColumn',
                    'options' => ['style' => 'width:120px;'],
                    'template' => '<div class="btn-group btn-group-sm" role="group" aria-label="...">{view}{update}</div>',
                    'buttons' => [
                        'view' => function ($url, $searchModel, $key) {
                            return Html::a('<i class="bi bi-eye"></i>', $url, ['class' => 'btn btn-default']);
                        },
                        'update' => function ($url, $searchModel, $key) {
                            return Html::a('<i class="bi bi-pencil"></i>', $url, ['class' => 'btn btn-default']);
                        },
                    ]
                ],

            ],
        ]);
        ?>
    </div>
    <?php Pjax::end(); ?>
</div>