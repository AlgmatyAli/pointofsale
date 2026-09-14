<?php

use yii\helpers\Html;
use app\models\CompanyInfo;

/* @var $this yii\web\View */
/* @var $model app\models\Salaryroll */

$name = '';
$id = '';
if (!empty($models)) {
  $firstModel = reset($models);
  $name = $firstModel["name"] ?? '';
  $id = $firstModel["id"] ?? '';
}

// 2. حساب الرصيد السابق
$balance = 0;
$sumwaredLast = 0;
$sumsaderLast = 0;

if (!empty($lastBalance)) {
  foreach ($lastBalance as $lb) {
    $sumwaredLast += (float) ($lb["wared"] ?? 0);
    $sumsaderLast += (float) ($lb["sader"] ?? 0);
    if (isset($lb['type'])) {
      if ($lb['type'] == 0 || $lb['type'] == 2) {
        $balance = $sumsaderLast - $sumwaredLast;
      } elseif ($lb['type'] == 1) {
        $balance = $sumsaderLast - $sumwaredLast;
      }
    }
  }
}

$title = CompanyInfo::find()->select(['*'])->asArray()->one();
?>

<div class="row">
  <div class="col-md-1"></div>
  <div class="col-md-10">
    <br><br>
    <p>
      <button class='btn btn-info' onClick="window.print()"><?= Yii::t('app', 'Print') ?></button>
      <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-danger btnx']) ?>
    </p>
    <br><br><br>

    <?php if (!empty($title["path"])): ?>
      <img src="<?= $title["path"] ?>" class="img-circle logo" alt="Company Logo">
    <?php endif; ?>

    <h4><br><?= Html::encode($title['name'] ?? '') ?><br><br><br></h4>

    <center>
      <div>
        <h3>كشف حساب عميل من الفترة : <b><?= Html::encode($min_date) ?></b> حتى تاريخ <b><?= Html::encode($max_date) ?></b></h3>
      </div>
    </center>
    <br><br>

    <?php if (!empty($name) || !empty($id)): ?>
      <div>
        <h3>اسـم العميل: <b><?= Html::encode($id) ?></b> - <b><?= Html::encode($name) ?></b></h3>
      </div>
    <?php endif; ?>

    <br><br>

    <table class="table table-hover">
      <thead class="thead-dark">
        <tr>
          <th colspan="5"></th>
          <th>
            <h4>رصيد سابق : <?= number_format($balance, 3); ?></h4>
          </th>
        </tr>
        <tr>
          <th>#</th>
          <th>
            <h5>تاريخ الحركة</h5>
          </th>
          <th>
            <h5>البيــــــان</h5>
          </th>
          <th>
            <h5>دائــــــــن</h5>
          </th>
          <th>
            <h5>مديــــــــن</h5>
          </th>
          <th>
            <h5>الرصيــــــد</h5>
          </th>
          <th></th>
          <th></th>
        </tr>
      </thead>

      <tbody>
        <?php
        $coun = 1;
        $sumwared = 0;
        $sumsader = 0;
        $deleviried = 0;
        $lastModelType = 0;

        if (!empty($models)):
          foreach ($models as $model):
            $waredVal = (float) $model["wared"];
            $saderVal = (float) $model["sader"];
            $sumwared += $waredVal;
            $sumsader += $saderVal;
            $lastModelType = $model['type'];
            // die(var_dump($model["billId"]));
            if ($model['type'] != 1) {
              if ($model["deleviried"] == 0) {
                $deleviried += $saderVal;
              }
            }
        ?>
            <tr>
              <td><?= $coun++ ?></td>
              <td><?= Html::encode($model["trandate"]) ?></td>
              <td><?= Html::encode($model["kind"] . ' ' . $model["billId"]) ?></td>
              <td><?= number_format($waredVal, 3) ?></td>
              <td><?= number_format($saderVal, 3) ?></td>
              <td>
                <?php
                if ($model['type'] == 0 || $model['type'] == 2) {
                  echo number_format(($sumsader - $sumwared) + $balance, 3);
                } elseif ($model['type'] == 1) {
                  echo number_format(($sumwared - $sumsader) + $balance, 3);
                }
                ?>
              </td>
              <td>
                <?php
                if (in_array($model["kind"], ['مشتريات قاتورة رقم - ', 'ترجيع مشتريات فاتورة رقـم - ', 'فاتورة مشتريات رقـم - '])) {
                  echo Html::a('<i class="fa fa-folder-open"></i>', ['purchases/view', 'id' => $model["printId"]], [
                    'title' => Yii::t('yii', 'View'),
                    'class' => 'btn btn-success'
                  ]);
                }

                if (in_array($model["kind"], ['مبيعات فاتورة رقم - ', 'ترجيع مبيعات فاتورة رقم - '])) {
                  echo Html::a('<i class="fa fa-folder-open"></i>', ['sales/print', 'id' => $model["printId"]], [
                    'title' => Yii::t('yii', 'View'),
                    'class' => 'btn btn-primary'
                  ]);
                }

                if ($model["kind"] == 'تحصيل ايصال رقـم - ') {
                  echo Html::a('<i class="fa fa-folder-open"></i>', ['receipt/view', 'id' => $model["printId"]], [
                    'title' => Yii::t('yii', 'View'),
                    'class' => 'btn btn-danger'
                  ]);
                }

                if ($model["kind"] == 'ايصال صرف رقـم - ') {
                  echo Html::a('<i class="fa fa-folder-open"></i>', ['receipt/view', 'id' => $model["printId"]], [
                    'title' => Yii::t('yii', 'View'),
                    'class' => 'btn btn-warning'
                  ]);
                }
                ?>
              </td>
              <td>
                <?php
                if ($model["type"] != 1 && $model["deleviried"] == 0) {
                  echo Html::a('<i class="fa fa-circle opacity2"></i>', ['#'], [
                    'title' => Yii::t('yii', 'View'),
                    'class' => ''
                  ]);
                }
                ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" class="text-center text-muted">
              <b>لا توجد حركات مسجلة خلال الفترة المحددة.</b>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>

      <tfoot>
        <tr>
          <td></td>
          <td>رصيد قبل: <?= Html::encode($min_date) ?></td>
          <td><?= number_format($balance, 3) ?></td>
          <td><?= number_format($sumwared, 3) ?></td>
          <td><?= number_format($sumsader, 3) ?></td>
          <td>الرصيـد :
            <?php
            if ($lastModelType == 1) {
              echo number_format(($sumwared - $sumsader) + $balance, 3);
            } else {
              echo number_format(($sumsader - $sumwared) + $balance, 3);
            }
            ?>
          </td>
          <td colspan="2">اجمالي الغير مستلمة : <?= number_format($deleviried, 3) ?></td>
        </tr>
      </tfoot>
    </table>
  </div>

  <div class="col-md-1"></div>
</div>