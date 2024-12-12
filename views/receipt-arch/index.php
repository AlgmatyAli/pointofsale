<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use app\models\Client;
use kartik\date\DatePicker as DateDatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReceiptArchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Receipt Arches');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="receipt-arch-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>


    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => '',
        'columns' => [
            'rId',
            [
                'attribute' => 'clinet',
                'label' => 'Client',
                'value' => function ($model) {
                    return $model->c->name;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(Client::find()->asArray()->all(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Client', 'id' => 'grid--search-client']
            ],

            [
                'attribute' => 'at',
                'filter' => DateDatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'at',
                    'language' => 'ar',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'todayBtn' => true,
                    ]
                ])
            ],
            'value',
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>