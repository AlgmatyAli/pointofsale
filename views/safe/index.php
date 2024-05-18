<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\SafeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Saves');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="safe-index">

    <h1><?= Html::encode($this->title) ?></h1><hr>
    <?php Pjax::begin(); ?>
    <?php
    $sumDisscount = 0;
    $sumExtra = 0;
    $sumDrawing = 0;
    $sumDiff = 0;
    //$data = $dataProvider->getModels();
    $dataProvider->pagination->pageSize = 10000;
    foreach ($dataProvider->getModels() as $value){
        if ($value['type'] == 1) {
            $sumDrawing = $sumDrawing + $value['value'];
        } elseif ($value['type'] == 2) {
            $sumDisscount = $sumDisscount + $value['value'];
        }
        $sumDiff =  $sumDisscount - $sumDrawing;
    }
    ?>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-fw fa-info"></i></span>

            <div class="info-box-content">
                <span class="info-box-text"><?= Yii::t('app', 'اجمالي الصادر') ?></span>
                <span class="info-box-number"><?= number_format($sumDrawing, 3)  ?><small></small></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-fw fa-warning"></i></span>

            <div class="info-box-content">
                <span class="info-box-text"><?= Yii::t('app', 'اجمالي الوارد') ?></span>
                <span class="info-box-number"><?= number_format($sumDisscount, 3)  ?><small></small></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-fw fa-warning"></i></span>

            <div class="info-box-content">
                <span class="info-box-text"><?= Yii::t('app', 'صافي الخزينة') ?></span>
                <span class="info-box-number"><?= number_format($sumDiff, 3)  ?><small></small></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="row">
        <div class="col-md-6"></div>
        <div class="col-md-6">
            <?php
            //echo ' <center> <h3 class="text-danger"><span id="remainingBalance"> ' . number_format($total, 3) . "\n" . '</span></h3> </center>'
            ?>
        </div>
    </div>
    
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'showPageSummary' => true,
        'summary' => '',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => Yii::t('app', 'Br ID'),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->br->name;
                }
            ],
            'safeNo',
            'at',
            [
                'label' => Yii::t('app', 'Type'),
                'format' => 'raw',
                'value' => function ($searchModel) {

                    if ($searchModel->type == 1) {
                        return 'صادر';
                    } elseif ($searchModel->type == 2) {
                        return 'وارد';
                    }
                }
            ],

            [
                'label' => Yii::t('app', 'Safe No.'),
                'format' => 'raw',
                'value' => function ($searchModel) {

                    if ($searchModel->safeNo == 1) {
                        return 'خزينة رقم 1';
                    } elseif ($searchModel->safeNo == 2) {
                        return 'خزينة رقم 2';
                    }
                }
            ],

            'why',
            [
                'label' => Yii::t('app', 'Value'),
                //'attribute' => 'value',
                'value' => function ($searchModel) {

                    if ($searchModel->type == 1) {
                        return $searchModel->value * -1;
                    } elseif ($searchModel->type == 2) {
                        return $searchModel->value;
                    }
                },
                'format' => 'decimal',
                'hAlign' => 'right',
                'pageSummary' => true,
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