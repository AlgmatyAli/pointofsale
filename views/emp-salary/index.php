<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\EmpSalarySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Emp Salaries');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="emp-salary-index">
<?php Pjax::begin(); ?>
    <h1><?= Html::encode($this->title) ?></h1><hr>
    <?php
    $sumDisscount=0; $sumExtra=0; $sumDrawing=0;
    $data = $dataProvider->getModels();
   
    foreach ($data as $value) {
  
    if($value['type'] == 1){
        $sumDrawing = $sumDrawing + $value['value'];
    }elseif($value['type'] == 2){
        $sumDisscount = $sumDisscount + $value['value'];
    }elseif($value['type'] == 3){
        $sumExtra = $sumExtra + $value['value'];
    }
    
    }
    ?>
    <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-fw fa-info"></i></span>

                    <div class="info-box-content">
                    <span class="info-box-text"><?=Yii::t('app', 'اجمالي الخصم')?></span>
                    <span class="info-box-number"><?= $sumDisscount ?><small></small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-fw fa-save"></i></span>

                    <div class="info-box-content">
                    <span class="info-box-text"><?=Yii::t('app', 'اجمالي الاضافي')?></span>
                    <span class="info-box-number"><?= $sumExtra ?><small></small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-fw fa-save"></i></span>

                    <div class="info-box-content">
                    <span class="info-box-text"><?=Yii::t('app', 'اجمالي السحوبات')?></span>
                    <span class="info-box-number"><?= $sumDrawing ?><small></small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-fw fa-money"></i></span>

                    <div class="info-box-content">
                    <span class="info-box-text"><?=Yii::t('app', 'اجمالي السحوبات + الاضافي')?></span>
                    <span class="info-box-number"><?= $sumDrawing + $sumExtra ?><small></small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
        </div>
    <br><br>
   
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'showPageSummary' => true,
        'summary' => '',
        'rowOptions' => function ($searchModel) {
           if($searchModel->type ==3){
                return ['class' => 'danger'];
            }
            if($searchModel->type ==2){
                return ['class' => 'warning'];
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'emp.name',
            [
                'attribute' => 'at',
               // 'pageSummary' => 'اجمالي الحركات',
               // 'pageSummaryOptions' => ['class' => 'text-right text-end'],
            ],

            [
                'label' => Yii::t('app', 'Value'),
                'format' => 'decimal',
                'value'=>function($searchModel) {
                        return $searchModel->value;
                },
                'hAlign' => 'right',
                'pageSummary' => false,
            ],

            'month',
            'year',

            [
                'label' => Yii::t('app', 'TranType'),
                'format' => 'raw',
                   'value'=>function($searchModel) {
    
                    if($searchModel->type ==1){
                        return 'سحب';
                    }elseif($searchModel->type ==2){
                        return 'خصم';
                    }elseif($searchModel->type ==3){
                        return 'اضافي';
                    }

                }
            ],
            [
            'class' => 'yii\grid\ActionColumn',
            'options'=>['style'=>'width:120px;'],
            'template'=>'<div class="btn-group btn-group-sm" role="group" aria-label="...">
            {view}{update}{delete}</div>',
            'buttons'=>[
                'view'=>function($url){
                    return Html::a('<i class="fa fa-eye"></i>',$url,['class'=>'btn btn-default']);
                },
                'update'=>function($url){
                    return Html::a('<i class="fa fa-edit"></i>',$url,['class'=>'btn btn-default']);
                },
                'delete'=>function($url){
                    return Html::a('<i class="fa fa-trash"></i>',$url,['class'=>'btn btn-default']);
                },
                
            ]
            ],
            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
