<?php

use yii\helpers\Html;
use app\models\CompanyInfo;


/* @var $this yii\web\View */
/* @var $model app\models\Salaryroll */ 
?>
        <?php 
         foreach ($models as $model) {
          $name = $model["name"];
          $id = $model["id"];
         }
        ?>
<div class="row">

<div class="col-md-1"></div>
<div class="col-md-10">
<br><br>
   <p>
     <button class='btn btn-info' onClick="window.print()"><?= Yii::t('app', 'Print')?></button>`
     <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer , ['class'=> 'btn btn-danger btnx']) ?>
   </p>
   <br><br><br>

      <?php 
          $sum=0;
          $coun=1;
          $sumwared = 0;
          $sumsader = 0;
          $deleviried = 0;
          foreach ($lastBalance as $model):
          $sumwared +=  (float) $model["wared"];
          $sumsader +=  (float) $model["sader"];
          if($model['type'] == 0 || $model['type'] == 2){
            $balance =  $sumsader - $sumwared ;
          }
          if($model['type'] == 1){
            $balance =   $sumwared - $sumsader ;
          }
       ?> 
        <?php endforeach; ?>  

   <?php $title = CompanyInfo::find()->select(['*'])->asArray()->one();?>

    <img src=<?php echo $title["path"]?> class="img-circle logo" alt="Cinque Terre">
    <h4><br><?php
     echo  $title['name'];
     echo '<br><br><br>';
    ?> </h4>
   
    <center>
      <div>
        <h3>كشف حساب عميل من الفترة : <b><?= $min_date ?></b> حتى تاريخ <b><?= $max_date ?></b></h3>
      </div>
    </center>
    <br><br>
    <div>
      <h3>اسـم العميل: <b><?= $id ?></b> - <b><?= $name ?></b></h3>
    </div>
   
    
    <br><br>
    <div class="row">
     <div class="col-md=4"><div>
     <div class="col-md=4"><div>
     <div class="col-md=4"><div>
    </div>
    <table class="table table-hover">
      <thead class="thead-dark">
      <tr>
        <th></th>
        <th><h5> </h5></th>
        <th><h5> </h5></th>
        <th><h5> </h5></th>
        <th><h5> </h5></th>
        <th><h4> رصيد سابق : <?php echo number_format($balance, 3)."\n"; ?></h4></th>
       </tr>
       <tr>
        <th>#</th>
        <th><h5> تاريخ الحركة</h5></th>
        <th><h5> البيــــــان</h5></th>
        <th><h5> دائــــــــن</h5></th>
        <th><h5> مديــــــــن</h5></th>
        <th><h5> الرصيــــــد</h5></th>
       </tr>
      </thead>
    
       <?php 
         $sum=0;
         $coun=1;
         $sumwared = 0;
         $sumsader = 0;
          foreach ($models as $model):
          $sumwared +=  (float) $model["wared"];
          $sumsader +=  (float) $model["sader"];
          $count = $count + 1;
          
          if($model['type'] != 1 ){
          if($model["deleviried"] == 0){
            $deleviried +=  (float) $model["sader"]; 
          }
        }
        
       ?>

       <tbody>
          <tr>
           <td><?= $coun++?></td>
           <td><?= $model["trandate"]?></td>
           <td><?= $model["kind"].' '.$model["billId"]?></td>
           <td><?= number_format((float) $model["wared"], 3)."\n"?></td>
           <td><?= number_format((float) $model["sader"], 3)."\n"?></td>
           <td><?php 
           if($model['type'] == 0 || $model['type'] == 2){
             echo number_format(($sumsader - $sumwared) + $balance, 3)."\n"; 
           }
           if($model['type'] == 1 ){
            echo number_format(($sumwared - $sumsader) + $balance, 3)."\n"; 
          }
           ?>
           </td>
           <td>
            <?php
              if($model["kind"] == 'مشتريات قاتورة رقم - ' || $model["kind"] == 'ترجيع مشتريات فاتورة رقـم - ' ||
              $model['kind'] == 'فاتورة مشتريات رقـم - '){ 
                echo Html::a('<i class="fa fa-folder-open"></i>', ['purchases/view','id'=>$model["printId"]],[
                                'title' => Yii::t('yii', 'View'),
                                'class'=>'btn btn-success'
                                ]);
              }
             
              if($model["kind"] == 'مبيعات فاتورة رقم - ' || $model["kind"] == 'ترجيع مبيعات فاتورة رقم - '){ 
                echo Html::a('<i class="fa fa-folder-open"></i>', ['sales/print','id'=>$model["printId"]],[
                                'title' => Yii::t('yii', 'View'),
                                'class'=>'btn btn-primary'
                                ]);
              }

              if($model["kind"] == 'تحصيل ايصال رقـم - '){ 
                echo Html::a('<i class="fa fa-folder-open"></i>', ['receipt/view','id'=>$model["printId"]],[
                                'title' => Yii::t('yii', 'View'),
                                'class'=>'btn btn-danger'
                                ]);
              }

              if($model["kind"] == 'ايصال صرف رقـم - '){
                echo Html::a('<i class="fa fa-folder-open"></i>', ['receipt/view','id'=>$model["printId"]],[
                  'title' => Yii::t('yii', 'View'),
                  'class'=>'btn btn-warning'
                  ]);
              }
            ?>
           </td>
           <td>
             <?php
                if($model["type"] != 1 ){
                if($model["deleviried"] == 0){
                 echo Html::a('<i class="fa fa-circle opacity2"></i>', ['#'],[
                   'title' => Yii::t('yii', 'View'),
                   'class'=>''
                   ]);
              }
            }
             ?>

           </td>
          </tr>
       </tbody>
       <?php endforeach; ?>
      
       <tr>
           <td></td>
           <td>رصيد قبل: <?=$min_date?></td>
           <td><?php echo number_format($balance, 3)."\n"; ?></td>
           <td><?php echo number_format($sumwared, 3)."\n"; ?>  </td>  
           <td><?php echo number_format($sumsader, 3)."\n"; ?>  </td>
           <td>الرصيـد : <?php 
           if($model['type'] == 0 || $model['type'] == 2){
            echo number_format(($sumsader - $sumwared) + $balance, 3)."\n"; 
          }
          if($model['type'] == 1 ){
           echo number_format(($sumwared - $sumsader) + $balance, 3)."\n"; 
         }
           
           ?></td>
           <td>اجمالي الغير مستلمة : <?php 
            echo number_format($deleviried, 3)."\n"; 
           ?></td>
      </tr>
    </table>
</div>

<div class="col-md-1">
</div>  

</div>