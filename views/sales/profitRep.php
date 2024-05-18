<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\models\Credts;
use app\models\CompanyInfo;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\Salaryroll */
?>

<div class="row">

<div class="col-md-12">
<br><br>
   
   <p>
     <button class='btn btn-info' onClick="window.print()"><?= Yii::t('app', 'Print')?></button>`
     <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer , ['class'=> 'btn btn-danger btnx']) ?>
   </p>

   <div id='div1' class="site-about">
   
   <?php $title = CompanyInfo::find()->select(['*'])->asArray()->one();?>

    <img src=<?php echo $title["path"]?> class="img-circle logo" alt="Cinque Terre">
    <h4><?php
     echo  $title['name'];
     echo '<br>';
    ?> </h4>
   <br>
   <center>
    <div>
         <h3>تقرير الأرباح اليومية من تاريخ : <b><?= $min_date ?></b> حتى تاريخ <b><?= $max_date ?></b></h3>
    </div>
    </center>
    <br><br>
      
    <table class="table table-hover">
      <thead>
       <tr>
        <th>#</th>
        <th><h5> رقم الصنف</h5></th>
        <th><h5> اسم الصنف</h5></th>
        <th><h5> الكمية</h5></th>
        <th><h5> سعر التكلفة</h5></th>
        <th><h5> سعر البيع</h5></th>
        <th><h5> هامش الربح</h5></th>
        <th><h5> إجمالي الربح</h5></th>
        <th><h5> إجمالي البيع</h5></th>
       </tr>
      </thead>
    
       <?php 
         $coun=1;
         foreach ($models as $model):
          $count = $count + 1;
          $quantity = $quantity + $model["quantity"];
          $costPrice = $costPrice + $model["costPrice"];
          $salePrice =  $salePrice + $model["salePrice"];
          $profit = $profit + ($model["salePrice"] - $model["costPrice"]);
          $total += $model["profit"]*$model["quantity"] ;
          $totalPrice += $model["salePrice"]*$model["quantity"] ;
       ?>

       <tbody>
          <tr>
           <td><?= $coun++?></td>
           <td><?= $model["category"]?></td>
           <td><?= $model["name"]?></td>
           <td><?= number_format($model["quantity"], 0)."\n";?></td>
           <td><?= number_format($model["costPrice"], 3)."\n";?></td>
           <td><?= number_format($model["salePrice"], 3)."\n";?></td>
           <td><?= number_format($model["profit"], 3)."\n";?></td>
           <td><?= number_format($model["profit"]*$model["quantity"], 3)."\n";?></td>
           <td><?= number_format($model["salePrice"]*$model["quantity"], 3)."\n";?></td>
          </tr>
       </tbody>
       <?php endforeach; ?>
      
       <tr>
           <td></td>
           <td></td>
           <td></td>
           <td><?php echo number_format($quantity, 3)."\n"; ?></td>
           <td><?php echo number_format($costPrice, 3)."\n"; ?></td>  
           <td><?php echo number_format($salePrice, 3)."\n"; ?></td>     
           <td><?php echo number_format($profit, 3)."\n"; ?></td>   
           <td><?php echo number_format($total, 3)."\n"; ?></td>   
           <td><?php echo number_format($totalPrice, 3)."\n"; ?></td>   
      </tr>
    </table>
</div>



</div>