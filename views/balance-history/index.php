<?php

/* @var $this yii\web\View */
/* @var $searchModel app\models\BalanceHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;

$this->title = Yii::t('app', 'Balance History');
$this->params['breadcrumbs'][] = $this->title;
$search = "$('.search-button').click(function(){
	$('.search-form').toggle(1000);
	return false;
});";
$this->registerJs($search);
?>
<div class="balance-history-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <hr>

    <p>
        <?= Html::a(Yii::t('app', 'Advance Search'), '#', ['class' => 'btn btn-info search-button']) ?>
    </p><br>
    <div class="search-form" style="display:none">
        <?= $this->render('_search', ['model' => $searchModel]); ?>
    </div>




    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'summary' => '',
        'showPageSummary' => true,
        'pjax' => true,
        'striped' => false,
        'hover' => true,
        'toggleDataContainer' => ['class' => 'btn-group mr-2'],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            [
                'attribute' => 'cleint',
                'label' => 'اسم العميل',
                'width' => '310px',
                'value' => function ($model, $key, $index, $widget) {
                    return $model->client0->name;
                },

                'group' => true,  // enable grouping

            ],
            [
                'attribute' => 'currancy',
                'width' => '125px',
                'value' => function ($model, $key, $index, $widget) {
                    return $model->currancy0->name;
                },

                'filterInputOptions' => ['placeholder' => 'Level2'],
                'group' => true,  // enable grouping
                'subGroupOf' => 1, // supplier column index is the parent group,
                'groupFooter' => function ($model, $key, $index, $widget) { // Closure method
                    return [
                        // 'mergeColumns' => [[2, 3]], // columns to merge in summary
                        'content' => [              // content to show in each summary cell
                            2 => yii::t('app', 'Summary') . ' (' . $model->currancy0->name . ') ',
                            // 4 => GridView::F_SUM,
                            3 => GridView::F_SUM,
                            // 6 => GridView::F_SUM,
                        ],
                        'contentFormats' => [      // content reformatting for each summary cell
                            // 4 => ['format' => 'number', 'decimals' => 2],
                            3 => ['format' => 'number', 'decimals' => 0],
                            // 6 => ['format' => 'number', 'decimals' => 2],
                        ],
                        'contentOptions' => [      // content html attributes for each summary cell
                            // 4 => ['style' => 'text-align:right'],
                            3 => ['style' => 'text-align:right'],
                            // 6 => ['style' => 'text-align:right'],
                        ],
                        // html attributes for group summary row
                        'options' => ['class' => 'success table-success', 'style' => 'font-weight:bold;']
                    ];
                },
            ],
            [
                'attribute' => 'amount',
                'label' => 'القيمة',
                'width' => '125px',
                'value' => function ($model, $key, $index, $widget) {
                    return $model->value;
                },

                // 'filterInputOptions' => ['placeholder' => 'Level3'],

            ],
            [
                'attribute' => 'AT',
                'label' => 'تاريخ الحركة',
                'width' => '125px',
                'value' => function ($model, $key, $index, $widget) {
                    return $model->AT;
                },

                // 'filterInputOptions' => ['placeholder' => 'Level3'],

            ],
            'why',

            'type'

        ],
    ]);

    ?>

</div>