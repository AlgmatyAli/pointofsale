<?php
use yii\helpers\Html;
use app\models\CompanyInfo;
?>
<?php $title = CompanyInfo::find()->select(['*'])->asArray()->one();?>

<!DOCTYPE html>
<html lang="ar">
  <head>
    <meta charset="utf-8">
</head>
  <body>
      <p>
       <button class='btn btn-info' onClick="window.print()"><?= Yii::t('app', 'Print')?></button>`
       <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer , ['class'=> 'btn btn-danger btnx']) ?>
      </p>
    <div id='div1'>
    <div id="container">
      <div class="invoice-top">
      
        <section id="memo">
          <div class="logo">
            <img src=<?php echo $title["path"]?> class="logo" alt="Cinque Terre">
          </div>
          <br><br>
          
          <div class="company-info">
            <span class="company-name"><?php echo $title["name"]?> </span>

            <span class="spacer"></span>
            <span class="company-name1"><?php echo $title["work"]?> </span>
            
            <span class="spacer"></span>
            <div></div>
            <div><?php echo $title["address"]?> </div>
            
            <span class="clearfix"></span>

            <div><?php echo $title["phone1"]?> |</div>
            <div><?php echo $title["phone2"]?> </div>
          </div>

        </section>
        
        <section id="invoice-info">
          <div>
            <span><?= $model->billId?></span>
            <span><?=$model->at?></span>
            <span><?php
                        if ($model->payWay == '0') {
                           echo 'نقدا';
                        }elseif($model->payWay == '1'){
                            echo 'آجل';
                        }
                        elseif($model->payWay == '2'){
                            echo 'دفعة على الحساب';
                        }
                     ?></span>
            <span><?=$model->deleviryAt?></span>
            <span></span>
          </div>
          
          <div>
            <span>رقـم الفاتورة:</span>
            <span>تاريخ الفاتورة:</span>
            <span>طريقة الدفع:</span>
            <span>موعد التسليم</span>
            <span></span>
          </div>

          <span class="clearfix"></span>

          </section>
        
        <section id="client-info">
          <span>تفاصيل الزبون</span>
          <div>
            <span class="bold"><?=$model->c->name?></span>
          </div>
          
          <div>
            <span><?=$model->c->phone?></span>
          </div>
          
          <div>
            <span><?=$model->c->email?></span>
          </div>
              
        </section>
 
        <div class="clearfix"></div>
      </div>

      <div class="clearfix"></div>

      <div class="invoice-body">
        <section id="items">
          
          <table cellpadding="0" cellspacing="0">
          
          <thead>
            <tr>
             <th>#</th>
             <th><h5>البيـــــان</h5></th>
             <th><h5>الكمية</h5></th>
             <th><h5>السعر</h5></th>
             <th><h5>الاجمـــالي</h5></th>
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
                <td><?= $data["name"] ?></td>
                <td><?= $data["quantity"] ?></td> 
                <td><?= $data["price"] ?></td>
                <td><?= $data["quantity"]*$data["price"] ?></td>
                <?php $sumquantity++ ?>
               </tr>
          </tbody>
       <?php endforeach; ?>      
          </table>
          
        </section>
        
        <section id="sums">
        
          <table cellpadding="0" cellspacing="0">
            <tr>
              <th>الاجمـــالي</th>
              <td><?php echo $model->total?></td>
              <td></td>
            </tr>
            
            <tr>
              <th>المدفـوع</th>
              <td><?php echo $model->paid?></td>
              <td></td>
            </tr>

            <tr>
            <th>الصـــافي</th>
              <td><?php echo $model->total - $model->paid?></td>
              <td></td>
            </tr> 
          </table>
          
        </section>

        <!-- <div class="clearfix"></div> -->
        <br><br><br>
        <section id="terms">

          <span>الشروط والأحكام:</span>
          <br>
          <div>
           <li> السعر يشمل النقل والتركيب للدور الارضي داخل طرابلس فقط على أن لا تقل قيمة الفاتورة عن 10,000 دينار</li>
           <li> لا تتحمل الشركة مسؤولية تخزين البضاعة لدينا عن مدة تزيد عن 20 يوم من تاريخ إنتهاء موعد التسليم تحت طائلة المسؤولية</li>
           <li> لن يتم تسليم البضاعة منعا باتاً إلا بعد تسكير الحساب</li>
          </div>

        </section>

      </div>
        
    </div>
    </div>
  </body>
  <script type="text/javascript">
      window.onload = function() { window.print(); }
 </script>
</html>
