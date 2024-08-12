<?php

use fedemotta\datatables\DataTables;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Categories');
?>
<div class="category-data-table">
<?php Pjax::begin(); ?>

    <?php 
    echo DataTables::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'clientOptions' => [
        "lengthMenu"=> [[10, 50,-1], [10,50,Yii::t('app',"View All")]],
        "info"=>false,
        "responsive"=>true, 
        "autoWidth" => false,
        "dom"=> 'Qlfrtip',//'lfTrtip',
    ],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

            'name',
            'serialNo',
            'commCode',
            'class',
            'company',
            // 'place',   
            // 'total.quantity',   
            // [
            //     'class' => 'yii\grid\ActionColumn',
            //     'options'=>['style'=>'width:100px;'],
            //     'template'=>'<div class="btn-group btn-group-sm" role="group" aria-label="...">{view}</div>',
            //     'buttons'=>[
            //         'view'=>function($url,$searchModel,$key){
            //             return Html::a('<i class="fa fa-eye"></i>',$url,['class'=>'btn btn-default']);
            //         },
                   
                    
            //     ]
            // ],
    ],
    ]);
    
    ?>
    <?php Pjax::end(); ?>

</div>
