<?php

use app\models\CompanyInfo;
/* @var $this yii\web\View */
/* @var $model app\models\Purchases */
?>

<div class="sales-view">
    <?php
    foreach ($models as $value) {
      $model = $value;
    }
    ?>
     <?php $title = CompanyInfo::currentArray();?>
     <img src=<?php //echo $title["path"]?> class="img-circle logo" alt="Cinque Terre">
     <h3><?php
      echo  \yii\helpers\Html::encode($title['name']);
      echo '<br>';
      echo  \yii\helpers\Html::encode($title['work']);
      echo '<br>';
      ?> </h3>
    <center>
     <h3><?php if ($model["type"] == '1') {
        echo 'فاتورة مبيعات';
     }elseif($model["type"] == '2'){
         echo 'فاتورة مسترجع مبيعات';
     }
     ?> </h3>
    </center>
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
                     <td><?=\yii\helpers\Html::encode($model["billId"])?></td>
                     <td><?=\yii\helpers\Html::encode($model["client"])?></td>
                     <td><?=\yii\helpers\Html::encode($model["at"])?></td>
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
<!-- ======= -->
    <table class="table table-bordered tbl">
      <thead>
       <tr>
        <th>#</th>
        <th><h5>البيـــــان</h5></th>
        <th><h5>الشركة المصنعة</h5></th>
        <th><h5>الكمية</h5></th>
        <th><h5>سعر البيع</h5></th>
        <th><h5>الإجمـالي</h5></th>
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
           <td><?= \yii\helpers\Html::encode($data["category"]) ?></td>
           <td><?= \yii\helpers\Html::encode($data["company"]) ?></td>
           <td><?= \yii\helpers\Html::encode($data["quantity"]) ?></td>
           <td><?= \yii\helpers\Html::encode($data["salePrice"]) ?></td>
           <td><?= number_format($data["quantity"]*$data["salePrice"], 3)."\n";  ?></td>
           <?php $sumquantity++ ?>
          </tr>
       </tbody>
       <?php endforeach; ?>
 </table>
<!-- ======= -->
         <table style="border: 0px solid black;">
            <tr>
              <td style="width:70%;"></td>
              <th>الاجمـــالي</th>
              <td><?php echo  \yii\helpers\Html::encode($model['total'])?></td>
            </tr>
            
            <tr>
              <td style="width:70%;"></td>
              <th>المدفـوع</th>
              <td><?php echo \yii\helpers\Html::encode($model['paid']); ?></td>
            </tr>
            
            <tr>
            <td style="width:70%;"></td>
            <th><?= yii::t('app', 'Disscount') ?></th>
              <td><?php echo \yii\helpers\Html::encode($model['disscount']) ?> </td>
            </tr> 
            
            <tr>
            <td style="width:70%;"></td>
            <th>الصـــافي</th>
              <td><?php echo number_format($model['total'] - $model['paid'] - $model['disscount'] , 3) . "\n"; ?></td>
            </tr> 
            
            <tr>
            <td style="width:70%;"></td>
            <th>الرصيد</th>
              <td><?php echo number_format($balance, 3) . "\n"; ?></td>
            </tr> 
          </table>