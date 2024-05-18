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
   
   <center>
    <div>
      <h3>قائمة بالأصناف التي سعر بيعها أقل من سعر تكلفتها<b><?php // date('Y-m-d') ?></b> </h3>
    </div>
    </center>
    <div class="clearfix"></div>
    <br><br>
    <table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
      <thead class="kv-table-header w1">
       <tr>
        <th>#</th>
        <th><h5> رقم الصنف</h5></th>
        <th><h5> اسم الصنف</h5></th>
        <th><h5> الكمية  الحالية </h5></th>
        <th><h5> سعر التكلفة </h5></th>
        <th><h5> سعر البيع</h5></th>
       </tr>
      </thead>
    
       <?php 
         $sum=0;
         $credt=0;
         $coun=1;
         foreach ($models as $model): 
          $count = $count + 1;
       ?>

       <tbody>
          <tr>
           <td><?= $coun++?></td>
           <td><?= $model["category"]?></td>
           <td><?= $model["name"]?></td>
           <td><?= $model["quantity"]?></td>
           <td style="color: red;"><?= number_format($model["costPrice"], 3)."\n" ?></td>
           <td style="color: blue;"><?= number_format($model["maxPrice"], 3)."\n"?></td>
           <td><?=  Html::a('<i class="fa fa-folder-open"></i>', 
            ['prices/update', 'id'=> $model["id"]], ['class' => 'btn btn-success'])?></td>
          </tr>
       </tbody>
       <?php endforeach; ?>
    </table>
</div>

</div>