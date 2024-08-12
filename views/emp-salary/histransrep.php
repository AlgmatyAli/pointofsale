<?php

use yii\helpers\Html;
use app\models\CompanyInfo;

/* @var $this yii\web\View */
/* @var $model app\models\Salaryroll */
?>
        <?php 
         foreach ($models as $model) {
          $name = $model["name"];
          $id = $model["employee"];
         }
        ?>
<div class="row">

<div class="col-md-1">
</div>

<div class="col-md-11">
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
   <br><br>
   <center>
    <div>
      <h3>كشف بالسحوبات والخصومات للموظف من الفترة : <b><?= $min_date ?></b> حتى تاريخ <b><?= $max_date ?></b></h3>
    </div>
    </center>
    <br>
    <div>
      <h3>اسـم الموظف: <b><?= $id ?></b> - <b><?= $name ?></b></h3>
    </div>
    <br><br>
      
    <table class="table table-hover">
      <thead class="thead-dark">
       <tr>
        <th>#</th>
        <th><h5> تاريخ الحركة</h5></th>
        <th><h5> البيــــــان</h5></th>
        <th><h5> القيــــــمة</h5></th>
       </tr>
      </thead>
    
       <?php 
         $sum=0;
         $coun=1;
         $sumsader=0;
         $count=0;
          foreach ($models as $model):
            
          $sumsader =  $sumsader + $model["sader"];
          $count = $count + 1;
       ?>
       

       <tbody>
          <tr>
           <td><?= $coun++?></td>
           <td><?= $model["at"]?></td>
           <td><?= $model["kind"].''.$model["id"]?></td>
           <td><?= number_format($model["sader"], 3)."\n"?></td>
          </tr>
       </tbody>
       <?php endforeach; ?>
       
       <tr>
           <td></td>
           <td></td>
           <td></td>
           <td><?php echo number_format($sumsader, 3)."\n"; ?>  </td>
           <td>الرصيـد : <?php echo number_format($sumsader, 3)."\n"; ?></td>
      </tr>
    </table>
</div>

<div class="col-md-2">
</div>  

</div>