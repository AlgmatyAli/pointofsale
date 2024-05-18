<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\models\Dept;
use app\models\CompanyInfo;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;
use app\models\HistransClient;

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

  <div class="col-md-1">
  </div>

  <div class="col-md-10">
    <br><br>
    <p>
      <button class='btn btn-info' onClick="window.print()"><?= Yii::t('app', 'Print') ?></button>`
      <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-danger btnx']) ?>
      <br>
      <br>
    </p>

    <?php
    $sumQuantity = 0;
    foreach ($lastBalance as $model) :
      $sumQuantity = (float) $model["quantity"];
    ?>
    <?php endforeach; ?>

    <?php $title = CompanyInfo::find()->select(['*'])->asArray()->one(); ?>

    <img src=<?php echo $title["path"] ?> class="img-circle logo" alt="Cinque Terre">
    <br><br>
    <h4>
      <?php
      echo  $title['name'];
      echo '<br><br><br><br><br>';
      ?>
    </h4>

    <center>
      <div>
        <h3>كشف حساب صنف من الفترة : <b><?= $min_date ?></b> حتى تاريخ <b><?= $max_date ?></b></h3>
      </div>
    </center>
    <br>
    <div>
      <h3>اسـم الصنف: <b><?= $id ?></b> - <b><?= $name ?></b></h3>
    </div>
    <br><br>

    <table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
      <thead class="kv-table-header w0">
        <tr>
          <th></th>
          <th>
            <h5> </h5>
          </th>
          <th>
            <h5> </h5>
          </th>
          <th>
            <h5> </h5>
          </th>
          <th>
            <h5> </h5>
          </th>
          <th>
            <h5> </h5>
          </th>
          <th>
            <h4> رصيد سابق : <?php echo number_format($sumQuantity, 3) . "\n"; ?></h4>
          </th>
        </tr>
        <tr>
          <th>#</th>
          <th>
            <h5> تاريخ الحركة</h5>
          </th>
          <th>
            <h5> الفــــــرع</h5>
          </th>
          <th>
            <h5> البيــــــان</h5>
          </th>
          <th>
            <h5 style="color:green;"> دائــــــــن</h5>
          </th>
          <th>
            <h5 style="color:red;"> مديــــــــن</h5>
          </th>
          <th>
            <h5 style="color:blue;"> الرصيــــــد</h5>
          </th>
          <th>
            <h5></h5>
          </th>
        </tr>
      </thead>

      <?php
      $sum = 0;
      $coun = 1;
      foreach ($models as $model) :
        if ($model["quantity"] > 0) {
          $sumwared = $sumwared + $model["quantity"];
        }

        if ($model["quantity"] < 0) {
          $sumsader = $sumsader + ($model["quantity"] * -1);
        }
        $count = $count + 1;

        if ($model["kind_id"] == 2) {
          $sumPurchase = $sumPurchase + $model["quantity"];
        }
        if ($model["kind_id"] == 3) {
          $sumBackPurchase = $sumBackPurchase + $model["quantity"];
        }
        if ($model["kind_id"] == 4) {
          $sumSales = $sumSales + $model["quantity"];
        }
        if ($model["kind_id"] == 5) {
          $sumBackSales = $sumBackSales + $model["quantity"];
        }
      ?>
        <tbody>
          <tr>
            <td><?= $coun++ ?></td>
            <td><?= $model["trandate"] ?></td>
            <td><?= $model["branch"] ?></td>
            <td><?= $model["kind"] . ' - ' . $model["billId"] . ' - ' . $model["client"] ?></td>
            <td><?php
                if ($model["quantity"] >= 0) {
                  echo number_format($model["quantity"], 3) . "\n";
                } else {
                  echo number_format(0, 3) . "\n";
                }
                ?></td>
            <td><?php
                if ($model["quantity"] <= 0) {
                  echo number_format($model["quantity"] *-1, 3) . "\n";
                } else {
                  echo number_format(0, 3) . "\n";
                }
                ?></td>
            <td style="color:blue;"><?php 
                $sumQuantity = $sumQuantity + $model["quantity"] ;
                echo number_format($sumQuantity, 3) . "\n"; 
              
              ?></td>
            <td>
              <?php
              if ($model["kind"] == 'فاتورة مشتريات رقم' || $model["kind"] == 'فاتورة مسترجع مشتريات رقم ') {
                echo Html::a('<i class="fa fa-folder-open"></i>', ['purchases/view', 'id' => $model["printId"]], [
                  'title' => Yii::t('yii', 'View'),
                  'class' => 'btn btn-warning'
                ]);
              }

              if ($model["kind"] == 'فاتورة مبيعات رقم ' || $model["kind"] == 'فاتورة مسترجع مبيعات رقم') {
                echo Html::a('<i class="fa fa-folder-open"></i>', ['sales/print', 'id' => $model["printId"]], [
                  'title' => Yii::t('yii', 'View'),
                  'class' => 'btn btn-info'
                ]);
              }

              if ($model["kind"] == 'نقل إلى الفرع' || $model["kind"] == 'نقل من الفرع') {
                echo Html::a('<i class="fa fa-folder-open"></i>', ['transfer-items/view', 'id' => $model["printId"]], [
                  'title' => Yii::t('yii', 'View'),
                  'class' => 'btn btn-primary'
                ]);
              }

              if ($model["kind"] == 'تسوية جرد') {
                echo Html::a('<i class="fa fa-folder-open"></i>', ['arrangement/view', 'id' => $model["printId"]], [
                  'title' => Yii::t('yii', 'View'),
                  'class' => 'btn btn-danger'
                ]);
              }

              ?>
            </td>
            <td>
              <?php
              if ($model["deleviried"] == 0) {
                echo Html::a('<i class="fa fa-circle opacity2"></i>', ['#'], [
                  'title' => Yii::t('yii', 'View'),
                  'class' => ''
                ]);
              }
              ?>
            </td>
          </tr>
        </tbody>
      <?php endforeach; ?>

      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><?php echo number_format($sumwared, 3) . "\n"; ?> </td>
        <td><?php echo number_format($sumsader, 3) . "\n"; ?> </td>
        <td>الرصيـد : <?php echo number_format($sumQuantity, 3) . "\n"; ?></td>
        <td></td>
        <td></td>
      </tr>
    </table>
  </div>

  <div class='row'>
    <div class='col-md-1'></div>
    <div class='col-md-10'>
      <table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
      <thead class="kv-table-header w0">
        <tr>
          <th></th>
          <th>
            <h5> </h5>
          </th>
          <th>
            <h5> </h5>
          </th>
          <th>
            <h5> </h5>
          </th>
          <th>
            <h5> </h5>
          </th>
          <th>
            <h5> </h5>
          </th>
          <th>
          </th>
        </tr>
        <tr>
          <th>
            <h5> اجمالي المبيعات</h5>
          </th>
          <th>
            <h5> اجمالي مسترجع المبيعات</h5>
          </th>
          <th>
            <h5> اجمالي المشتريات</h5>
          </th>
          <th>
            <h5> اجمالي مسترجع المشتريات</h5>
          </th>
          <th>
            <h5> اجمالي تسوية رصيد الجرد اضافة</h5>
          </th>
          <th>
          <h5> اجمالي تسوية رصيد الجرد انقاص</h5>
          </th>
        </tr>
      </thead>
      <tbody>
          <tr>
            <td style="color:red;"><?php echo number_format(($sumSales*-1), 3) . "\n"; ?></td>
            <td style="color:red;"><?php echo number_format($sumBackSales, 3) . "\n"; ?></td>
            <td style="color:red;"><?php echo number_format($sumPurchase, 3) . "\n"; ?></td>
            <td style="color:red;"><?php echo number_format($sumBackPurchase, 3) . "\n"; ?></td>
            <td style="color:red;"><?php echo number_format(0, 3) . "\n"; ?></td>
            <td style="color:red;"><?php echo number_format(0, 3) . "\n"; ?></td>
          </tr>
      </tbody>
      </table>
    </div>
  </div>

 

</div>