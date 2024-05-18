<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\StocksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Inventories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stocks-index">

    <center>
        <h1><?= Html::encode($this->title) . ' ' . date('Y-m-d') ?></h1>
        <hr>
    </center>

    <?php Pjax::begin(); ?>
        <?php 
            $quantity=0; $costPrice=0; $salePrice=0;
            $data = $dataProvider->getModels();

            foreach ($data as $value) {
                $quantity =  $quantity + $value['quantity'];
                $costPrice =  $costPrice + $value['costPrice'] * $value['quantity'];
                $salePrice = $salePrice + $value['maxPrice'] * $value['quantity'];
            }
        ?>
        <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-fw fa-info"></i></span>

                    <div class="info-box-content">
                    <span class="info-box-text"><?=Yii::t('app', 'اجمالي الكمية')?></span>
                    <span class="info-box-number"><?= $quantity ?><small></small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-fw fa-save"></i></span>

                    <div class="info-box-content">
                    <span class="info-box-text"><?=Yii::t('app', 'اجمالي التكلفة')?></span>
                    <span class="info-box-number"><?= $costPrice ?><small></small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-fw fa-save"></i></span>

                    <div class="info-box-content">
                    <span class="info-box-text"><?=Yii::t('app', 'اجمالي البيع')?></span>
                    <span class="info-box-number"><?= $salePrice ?><small></small></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
        </div>
      <br><br><hr>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php
    $gridColumn = [
        [
            'label' => Yii::t('app', 'Name'),
            // 'headerOptions' => ['style' => 'width:30%'],
            'value' => function ($data) {
                return $data->name;
            },
           // 'group' => true,
            // 'groupFooter' => function ($model, $key, $index, $widget) { // Closure method
            //     return [
            //         // 'mergeColumns' => [[1,2]], // columns to merge in summary
            //         'content' => [             // content to show in each summary cell
            //             //  2 => 'مجموع الكمية ',
            //             4 => GridView::F_SUM,
            //         ],

            //         'contentFormats' => [      // content reformatting for each summary cell
            //             3 => ['format' => 'number', 'decimals' => 3],
            //             4 => ['format' => 'number', 'decimals' => 3],
            //             5 => ['format' => 'number', 'decimals' => 3],
            //             6 => ['format' => 'number', 'decimals' => 3],
            //         ],
            //         'contentOptions' => [      // content html attributes for each summary cell
            //             1 => ['style' => 'font-variant:small-caps'],
            //             3 => ['style' => 'text-align:right'],
            //             4 => ['style' => 'text-align:right'],
            //             5 => ['style' => 'text-align:right'],
            //             6 => ['style' => 'text-align:right'],
            //         ],
            //         // html attributes for group summary row
            //         'options' => ['class' => 'info table-info', 'style' => 'font-weight:bold;']
            //     ];
            // }

        ],
        [
            'label' => Yii::t('app', 'Serial No'),
            'headerOptions' => ['style' => 'width:5px'],
            'value' => function ($data) {
                return $data->serialNo;
            }

        ],
        // [
        //     'label' => Yii::t('app', 'Comm Code'),
        //    // 'headerOptions' => ['style' => 'width:15%'],
        //     'value' => function ($data) {
        //         return $data->commCode;
        //     }

        // ],

        'company',

        [
            'label' => Yii::t('app', 'quantity'),
            // 'headerOptions' => ['style' => 'width:10%'],
            'value' => function ($data) {
                return $data->quantity;
            }

        ],

        [
            'label' => Yii::t('app', 'Cost Price'),
            // 'headerOptions' => ['style' => 'width:10%'],
            'value' => function ($data) {
                return $data->costPrice;
            }

        ],

        [
            'label' => Yii::t('app', 'Max Price'),
            // 'headerOptions' => ['style' => 'width:10%'],
            'value' => function ($data) {
                return $data->maxPrice;
            }
        ],

        [
            'label' => Yii::t('app', 'Min Price'),
            // 'headerOptions' => ['style' => 'width:10%'],
            'value' => function ($data) {
                return $data->minPrice;
            }

        ],

        // [
        //     'label' => Yii::t('app', 'Branch'),
        //     'headerOptions' => ['style' => 'width:10%'],
        //     'value' => function ($data) {
        //         return $data->branches0->name;
        //     }

        // ],

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

    <?php Pjax::end(); ?>

</div>
