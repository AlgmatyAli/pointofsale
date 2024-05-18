<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\CompanyInfo;
/* @var $this yii\web\View */
/* @var $model app\models\Purchases */
?>

<div class="sales-view">
     <p>
          <button class='btn btn-info' onClick="window.print()"><?= Yii::t('app', 'Print')?></button>`
          <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer , ['class'=> 'btn btn-danger btnx']) ?>
     </p>
    <?php
    foreach ($models as $value) {
      $model = $value;
    }
   // die(var_dump($model));
    ?>
     <?php $title = CompanyInfo::find()->select(['*'])->asArray()->one();?>
     <img src=<?php echo $title["path"]?> class="img-circle logo" alt="Cinque Terre">
     <h3><?php
      echo  $title['name'];
      echo '<br><br>';
      ?> </h3>
    <center>
     <h3><?php if ($model["type"] == '1') {
        echo 'فاتورة مبيعات';
     }elseif($model["type"] == '2'){
         echo 'فاتورة مسترجع مبيعات';
     }
     ?> </h3>
    </center>
     <br>
    <div class="row">
        <div class = "col-sm-6">
             <table class="table table-bordered table-dark">
               <thead>
                 <tr>
                   <th>رقم الفاتورة </th>
                   <th>اسم الزبون </th>
                   <th>تاريخ الفاتورة</th>
                   <th>طريقة الدفع</th>
                   <th>كيفية التسليم</th>
                 </tr>
               </thead>                

                 <tbody>
                    <tr>
                     <td><?=$model["billId"]?></td>
                     <td><?=$model["client"]?></td>
                     <td><?=$model["at"]?></td>
                     <td><?php
                        if ($model["payWay"] == '0') {
                           echo 'نقدا';
                        }elseif($model["payWay"] == '1'){
                            echo 'آجل';
                        }
                        elseif($model["payWay"] == '2'){
                            echo 'دفعة على الحساب';
                        }
                     ?></td>
                     <td><?php
                        if ($model["deleviried"] == '0') {
                           echo 'لم يتم تسليمها';
                        }elseif($model["deleviried"] == '1'){
                            echo 'تم التسليم';
                        }
                     ?></td>
                    </tr>
                 </tbody>
             </table>
        </div>
     </div>
     <br>
<!-- ======= -->
    <table class="table table-bordered tbl">
      <thead>
       <tr>
        <th>#</th>
        <th><h5>البيـــــان</h5></th>
        <th><h5>رقم القطعة</h5></th>
        <th><h5>رقم القطعة التجاري</h5></th>
        <th><h5>الكمية</h5></th>
        <th><h5>الشركة المصنعة</h5></th>
        <th><h5>الكمية المتبقية</h5></th>
        <th><h5>الكمية المحجوزة</h5></th>
       </tr>
      </thead>
       
       <?php 
         $coun=1;
         $sumquantity=0;
         foreach ($infos as $data):
         $id = $data["id"];
       ?>
       <tbody>
          <tr>
           <td><?= $coun++?></td>
           <td><?= $data["category"] ?></td>
           <td><?= $data["serialNo"] ?></td> 
           <td><?= $data["commCode"] ?></td> 
           <td><?= $data["quantity"] ?></td>
           <td><?= $data["company"] ?></td>
           <td><?= $data["Qtotalinventory"] ?></td>
           <td><?= $data["reservation"] ?></td>
           <?php $sumquantity++ ?>
          </tr>
       </tbody>
       <?php endforeach; ?>
 </table>
 <br>
<!-- ======= -->
<div class="row">
    <div class = "col-md-6">
      <table class="table">
      <thead>
       <tr>
        <th>ملاحظـــات:</th>
       </tr>
      </thead>
         <tbody>
          <tr>
            <td>  <?php echo $model["notes"]?></td>
          </tr>
         </tbody>
      </table> 
    </div>
    </div>
    <!-- ======= -->
</div>
  
<!-- <script type="text/javascript">
      window.onload = function() { window.print(); }
 </script> -->