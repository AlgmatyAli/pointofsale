<?php

use yii\helpers\Html;
use app\models\CompanyInfo;

/* @var $this yii\web\View */
/* @var $model app\models\Salaryroll */
?>

<div class="row">

<div class="col-md-1">
</div>

<div class="col-md-10">
<br><br>
   <p>
     <button class='btn btn-info' onClick="window.print()"><?= Yii::t('app', 'Print')?></button>`
     <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer , ['class'=> 'btn btn-danger btnx']) ?>
     <br>
     <br>
   </p>
   
   <?php $title = CompanyInfo::find()->select(['*'])->asArray()->one();?>
   
    <img src=<?php echo $title["path"]?> class="img-circle logo" alt="Cinque Terre">
    <br><br>
    <h4>
      <?php
     echo  $title['name'];
     echo '<br><br><br><br><br>';
    ?> 
    </h4>
   
   <center>
    <div>
      <h3>تقرير بالجرد النهائي حسب السنة<b></b></h3>
    </div>
    </center>
    <br>
    
    <br><br>
      
    <table class="table table-hover">
      <thead class="thead-dark">
       <tr>
        <th>#</th>
        <th><h5> تاريخ الحركة</h5></th>
        <th><h5> كود الصنف</h5></th>
        <th><h5> اسم الصنف</h5></th>
        <th><h5> الشركة المصنعة</h5></th>
        <th><h5> الكمية</h5></th>
        <th><h5> نوع الحركة</h5></th>
        <th><h5> السنة</h5></th>
       </tr>
      </thead>
    
       <?php 
         $count=1;
         $coun=0;
          foreach ($models as $model):
          $count = $count + 1;
       ?>
       

       <tbody>
          <tr>
           <td><?= $count++?></td>
           <td><?= $model["at"]?></td>
           <td><?= $model["serialNo"]?></td>
           <td><?= $model["name"]?></td>
           <td><?= $model["company"]?></td>
           <td><?= $model["quantity"]?></td>
           <td><?= $model["type"]?></td>
           <td><?= $model["year"]?></td>
           <td></td>
          </tr>
       </tbody>
       <?php endforeach; ?>
    </table>
</div>

<div class="col-md-1">
</div>  

</div>