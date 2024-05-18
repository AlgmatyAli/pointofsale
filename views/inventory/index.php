<?php
/* @var $this yii\web\View */
/* @var $searchModel app\models\InventorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;

$this->title = Yii::t('app', 'Inventories');
$this->params['breadcrumbs'][] = $this->title;
$search = "$('.search-button').click(function(){
	$('.search-form').toggle(1000);
	return false;
});";
$this->registerJs($search);
?>

<br>
<div class="inventory-index">
<div class="row">

        <?php
        $count = 0;
        foreach ($infos as $model) {
            $count = $model["quantity"];
        }

        if ($count != 0) { ?>
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-fw fa-dollar"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text"><?= Yii::t('app', 'Sum Quantity') ?></span>
                        <span class="info-box-number"><?= number_format($count, 0) . "\n"; ?><small></small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

        <?php }
        ?>

        <?php
        $totalCost = 0;
        foreach ($infos as $model) {
            $totalCost = $model["TotalCost"];
        }

        if ($count != 0) { ?>
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-fw fa-dollar"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text"><?= Yii::t('app', 'Sum Cost Price') ?></span>
                        <span class="info-box-number"><?= number_format($totalCost, 3) . "\n"; ?><small></small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>

        <?php  }
        ?>

    

         <?php 
        $price= 0;  
        foreach ($infos as $model) {
            $price = $model["Price"];
        }
        
        if( $count !=0 ){ ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-red"><i class="fa fa-fw fa-dollar"></i></span>

                        <div class="info-box-content">
                        <span class="info-box-text"><?=Yii::t('app','Sum Sale Price')?></span>
                        <span class="info-box-number"><?= number_format($price, 3)."\n";  ?><small></small></span>
                        </div>
                        <!-- /.info-box-content -->
                        </div>
                    <!-- /.info-box -->
                </div>

        <?php }
        ?>

    <?php 
        $totalCostWOHangOut= 0;  
        foreach ($inventory as $model) {
            $totalCostWOHangOut = $model["TotalCost"];
        }
        
        if( $count !=0 ){ ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-black"><i class="fa fa-fw fa-dollar"></i></span>

                        <div class="info-box-content">
                        <span class="info-box-text"><?=Yii::t('app','Sum HangOut Cost Price')?></span>
                        <span class="info-box-number"><?= number_format($totalCostWOHangOut, 3)."\n";  ?><small></small></span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
               
        <?php  }
        ?>

    </div>
    <br><br>
    <center>
        <h1><?= Html::encode($this->title) . ' ' . date('Y-m-d') ?></h1>
        <hr>
    </center>
    <p>
        <?= Html::a(Yii::t('app', 'Advance Search'), '#', ['class' => 'btn btn-info search-button']) ?>
    </p>
    <div class="search-form" style="display:none">
        <?= $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    <br>
    <?php
    $gridColumn = [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'label' => Yii::t('app', 'Serial No'),
            'headerOptions' => ['style' => 'width:15%'],
            'value' => function ($data) {
                return $data->serialNo;
            }

        ],
        [
            'label' => Yii::t('app', 'Serial No'),
            'headerOptions' => ['style' => 'width:15%'],
            'value' => function ($data) {
                return $data->serialNo;
            }

        ],

        [
            'label' => Yii::t('app', 'Name'),
            'headerOptions' => ['style' => 'width:30%'],
            'value' => function ($data) {
                return $data->name;
            },
            'group' => true,
            'groupFooter' => function ($model, $key, $index, $widget) { // Closure method
                return [
                    // 'mergeColumns' => [[1,2]], // columns to merge in summary
                    'content' => [             // content to show in each summary cell
                        //  2 => 'مجموع الكمية ',
                        4 => GridView::F_SUM,
                    ],

                    'contentFormats' => [      // content reformatting for each summary cell
                        3 => ['format' => 'number', 'decimals' => 3],
                        4 => ['format' => 'number', 'decimals' => 3],
                        5 => ['format' => 'number', 'decimals' => 3],
                        6 => ['format' => 'number', 'decimals' => 3],
                    ],
                    'contentOptions' => [      // content html attributes for each summary cell
                        1 => ['style' => 'font-variant:small-caps'],
                        3 => ['style' => 'text-align:right'],
                        4 => ['style' => 'text-align:right'],
                        5 => ['style' => 'text-align:right'],
                        6 => ['style' => 'text-align:right'],
                    ],
                    // html attributes for group summary row
                    'options' => ['class' => 'info table-info', 'style' => 'font-weight:bold;']
                ];
            }

        ],

        'category0.company',

        [
            'label' => Yii::t('app', 'quantity'),
            'headerOptions' => ['style' => 'width:10%'],
            'value' => function ($data) {
                return $data->quantity;
            }

        ],

        [
            'label' => Yii::t('app', 'Cost Price'),
            'headerOptions' => ['style' => 'width:10%'],
            'value' => function ($data) {
                return $data->prices->costPrice;
            }

        ],

        [
            'label' => Yii::t('app', 'Max Price'),
            'headerOptions' => ['style' => 'width:10%'],
            'value' => function ($data) {
                return $data->prices->maxPrice;
            }
        ],

        [
            'label' => Yii::t('app', 'Min Price'),
            'headerOptions' => ['style' => 'width:10%'],
            'value' => function ($data) {
                return $data->prices->minPrice;
            }

        ],

        [
            'label' => Yii::t('app', 'Branch'),
            'headerOptions' => ['style' => 'width:10%'],
            'value' => function ($data) {
                return $data->branches0->name;
            }

        ],

    ];
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => $gridColumn,
        'summary' => '',
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-inventory']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            // 'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title),
        ],
        // your toolbar can include the additional full export menu

    ]);
    ?>
