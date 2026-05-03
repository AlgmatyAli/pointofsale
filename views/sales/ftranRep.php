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
         <button class='btn btn-info' onClick="window.print()"><?= Yii::t('app', 'Print') ?></button>`
         <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-danger btnx']) ?>
      </p>

      <div id='div1' class="site-about">
         <?php
         $sum = 0;
         $coun = 1;
         $sumwared = 0;
         $sumsader = 0;
         $balance = 0;
         if (!empty($lastBalance) && is_array($lastBalance)) {
            foreach ($lastBalance as $model) {
               $sumwared +=  (float) $model["wared"];
               $sumsader +=  (float) $model["sader"];
               $balance =  $sumwared - $sumsader;
            }
         }
         ?>

         <?php $title = CompanyInfo::find()->select(['*'])->asArray()->one(); ?>

         <img src=<?php echo $title["path"] ?> class="img-circle logo" alt="Cinque Terre">
         <h4><?php
               echo  $title['name'];
               echo '<br>';
               ?> </h4>
         <br>
         <center>
            <div>
               <h3>تقرير الحركة اليومية من تاريخ : <b><?= $min_date ?></b> حتى تاريخ <b><?= $max_date ?></b></h3>
            </div>
         </center>
         <br><br>

         <table class="table table-hover">
            <thead>
               <tr>
                  <th>#</th>
                  <th>
                     <h5> بيان الحركة</h5>
                  </th>
                  <th>
                     <h5> التاريخ</h5>
                  </th>
                  <th>
                     <h5 style="float: right;"> كيفية السداد</h5>
                  </th>
                  <th>
                     <h5> الصادر</h5>
                  </th>
                  <th>
                     <h5> الوارد</h5>
                  </th>
               </tr>
            </thead>

            <?php
            $sader = 0;
            $wared = 0;
            $coun = 1;
            foreach ($models as $model):
               $sader = $sader + $model["sader"];
               $wared = $wared + $model["wared"];
               $disscount = $disscount + $model["disscount"];
               $count = $count + 1;
            ?>

               <tbody>
                  <tr>
                     <td><?= $coun++ ?></td>
                     <td><?= $model["description"] ?></td>
                     <td><?= $model["date_"] ?></td>
                     <td><?= $model["paymentType"] ?></td>
                     <td><?= number_format($model["sader"], 3) . "\n"; ?></td>
                     <td><?= number_format($model["wared"], 3) . "\n"; ?></td>
                  </tr>
               </tbody>
            <?php endforeach; ?>

            <tr>
               <td><b>اجمالي التخفيض:</b> <?php echo number_format($disscount, 3) . "\n"; ?></td>
               <td><b>رصيد قبل:</b> <?= $min_date . '  (' . number_format($balance, 3) . "\n)"; ?></td>
               <td></td>
               <td><?php echo number_format($sader, 3) . "\n"; ?></td>
               <td><?php echo number_format($wared, 3) . "\n"; ?></td>
               <td>الرصيـد : <?php echo number_format($wared - $sader, 3) . "\n"; ?></td>
               <td></td>
            </tr>
         </table>
         <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
               <br><br>
               <h4> ملخص حسب نوع السداد </h4>
               <br>
               <table class="table table-bordered table-hover">
                  <thead>
                     <tr>
                        <th style="text-align:right">نوع السداد</th>
                        <th style="text-align:right">الصادر</th>
                        <th style="text-align:right">الوارد</th>
                        <th style="text-align:right">الصافي</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                     foreach ($sumSql as $sumRow) {
                        $ptype = isset($sumRow['max(payment_types.name)']) ? $sumRow['max(payment_types.name)'] : (isset($sumRow['name']) ? $sumRow['name'] : '');
                        $s = isset($sumRow['sum(sader)']) ? $sumRow['sum(sader)'] : 0;
                        $w = isset($sumRow['sum(wared)']) ? $sumRow['sum(wared)'] : 0;
                        echo "<tr>";
                        echo "<td style='vertical-align:middle;text-align:right;'><b>" . htmlspecialchars($ptype) . "</b></td>";
                        echo "<td style='vertical-align:middle;text-align:right;'>" . number_format($s, 3) . "</td>";
                        echo "<td style='vertical-align:middle;text-align:right;'>" . number_format($w, 3) . "</td>";
                        echo "<td style='vertical-align:middle;text-align:right;'>" . number_format($w - $s, 3) . "</td>";
                        echo "</tr>";
                     }
                     ?>
                  </tbody>
               </table>
            </div>
            <div class="col-md-3"></div>
         </div>
      </div>
   </div>