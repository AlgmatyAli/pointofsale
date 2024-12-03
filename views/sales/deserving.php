<?php

use yii\helpers\Html;
use app\models\CompanyInfo;
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
      <h3>قائمة بالفواتير التي قارب موعد استحقاقها<b><?php // date('Y-m-d') ?></b> </h3>
    </div>
    </center>
    <div class="clearfix"></div>
    <br><br>
    <table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
      <thead class="kv-table-header w1">
       <tr>
        <th>#</th>
        <th><h5> رقم الحساب</h5></th>
        <th><h5> اسم العميل</h5></th>
        <th><h5> رقم الفاتورة</h5></th>
        <th><h5> قيمة الفاتورة</h5></th>
        <th><h5> اجمالي الدين</h5></th>
        <th><h5> تاريخ الفاتورة</h5></th>
        <th><h5> تاريخ الاستحقاق</h5></th>
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
           <td><?= $model["id"]?></td>
           <td><?= $model["clientName"]?></td>
           <td><?= $model["billId"]?></td>
           <td style="color: red;"><?= number_format($model["total"] -  $model["disscount"] - $model["paid"], 3)."\n" ?></td>
           <td style="color: blue;"><?= number_format($model["credt"], 3)."\n"?></td>
           <td><?= $model["at"]?></td>
           <td><?= $model["deserving"]?></td>
           <td><?=  Html::a('<i class="fa fa-folder-open"></i>', 
            ['sales/print', 'id'=> $model["id"]], ['class' => 'btn btn-success  btn-sm'])?></td>
            <td><?=  Html::a('<i class="fa fa-paper-plane"></i>', 
            ['sales/done', 'id'=> $model["id"]], ['class' => 'btn btn-default  btn-sm'])?></td>
            <td><?=  Html::a('<i class="fa fa-stop"></i>', 
            ['sales/stop-credit', 'id'=> $model["client"]], ['class' => 'btn btn-default btn-sm'])?></td>
          </tr>
       </tbody>
       <?php endforeach; ?>
    </table>
</div>

</div>