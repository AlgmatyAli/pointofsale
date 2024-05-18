<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\FtranSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;

$this->title = Yii::t('app', 'Ftran');
$this->params['breadcrumbs'][] = $this->title;
$search = "$('.search-button').click(function(){
	$('.search-form').toggle(1000);
	return false;
});";
$this->registerJs($search);
?>
<div class="ftran-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
     
        <?= Html::a(Yii::t('app', 'Advance Search'), '#', ['class' => 'btn btn-info search-button']) ?>
    </p>
    <div class="search-form" style="display:none">
        <?=  $this->render('_search', ['model' => $searchModel]); ?>
    </div>
   





<?php echo GridView::widget([
    'dataProvider' => $dataProvider,
    // 'filterModel' => $searchModel,
    'showPageSummary' => true,
    'pjax' => true,
    'striped' => false,
    'hover' => true,
    'toggleDataContainer' => ['class' => 'btn-group mr-2'],
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],
      
        [
            'attribute' => 'date_', 
            'width' => '310px',
            'value' => function ($model, $key, $index, $widget) { 
                return $model->date_;
            },
          
            'group' => true,  // enable grouping
          
        ],
        [
            'attribute' => 'user_insert', 
            'width' => '250px',
            'value' => function ($model, $key, $index, $widget) { 
                return $model->user->username;
            },
          
            // 'filterInputOptions' => ['placeholder' => 'Level2'],
            'group' => true,  // enable grouping
            'subGroupOf' => 1, // supplier column index is the parent group,
            'groupFooter' => function ($model, $key, $index, $widget) { // Closure method
                return [
                    // 'mergeColumns' => [[2, 3]], // columns to merge in summary
                    'content' => [              // content to show in each summary cell
                        2 =>yii::t('app','Summary'). ' (' . $model->user->username . ') ' ,
                        5 => GridView::F_SUM,
                        3 => GridView::F_SUM,
                        6 => GridView::F_SUM,
                    ],
                    'contentFormats' => [      // content reformatting for each summary cell
                        5 => ['format' => 'number', 'decimals' => 2],
                        3 => ['format' => 'number', 'decimals' => 0],
                        6 => ['format' => 'number', 'decimals' => 2],
                    ],
                    'contentOptions' => [      // content html attributes for each summary cell
                        5 => ['style' => 'text-align:right'],
                        3 => ['style' => 'text-align:right'],
                        6 => ['style' => 'text-align:right'],
                    ],
                    // html attributes for group summary row
                    'options' => ['class' => 'success table-success','style' => 'font-weight:bold;']
                ];
            },
        ],
        'description',
        [
            'attribute' => 'sader', 
            'width' => '250px',
            'value' => function ($model, $key, $index, $widget) { 
                return $model->sader;
            },
          
            // 'filterInputOptions' => ['placeholder' => 'Level3'],
            
        ],
        [
            'attribute' => 'wared', 
            'width' => '250px',
            'pageSummary' => Yii::t('app', 'Page Summary'),
            'value' => function ($model, $key, $index, $widget) { 
                return $model->wared;
            },
          
            // 'filterInputOptions' => ['placeholder' => 'Level3'],
            
        ],
        [
            'label' => Yii::t('app', 'Total'),
            'contentOptions' => ['style' => 'font-size:12px;'],
            'headerOptions' => ['style' => 'width:20%'],
            'pageSummary' => true,
            'format' => ['decimal', 3],
            'value' => function ($data) {
              return $data->wared - $data->sader;
            }
      
          ]
    
      
    ],
    ]);
   
   ?>





</div>
