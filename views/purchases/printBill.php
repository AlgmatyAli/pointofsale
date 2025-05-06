<?php

use yii\helpers\Html;
use app\models\CompanyInfo;
use yii\grid\GridView;

?>
<?php $title = CompanyInfo::find()->select(['*'])->asArray()->one(); ?>

<!DOCTYPE html>
<html lang="ar">

<head>
  <title></title>
  <meta charset="utf-8">
</head>

<body>
  <p>
    <button class='btn btn-info' onClick="window.print()"><?= Yii::t('app', 'Print') ?></button>`
    <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-danger btnx']) ?>
  </p>
  <div id='div1'>
    <div id="container">

      <div class="invoice-top">

        <section id="memo">
          <div class="logo">
            <img src=<?php echo $title["path"] ?> class="logo" alt="Cinque Terre">
          </div>
          <br><br>

          <div class="company-info">
            <span class="company-name"><?php echo $title["name"] ?> </span>

            <span class="spacer"></span>
            <span class="company-name1"><?php echo $title["work"] ?> </span>

            <span class="spacer"></span>
            <div></div>
            <div><?php echo $title["address"] ?> </div>

            <span class="clearfix"></span>

            <div><?php echo $title["phone1"] ?> |</div>
            <div><?php echo $title["phone2"] ?> </div>
          </div>

        </section>
        <h2 class="text-center white"><?= Yii::t('app', 'Purchases Invoice'); ?></h2>
        <section id="invoice-info">
          <div>
            <span><?= $model->billId ?></span>
            <span><?= $model->clientBill ?></span>
            <span><?= $model->at ?></span>
            <span><?php
                  if ($model->payWay == '0') {
                    echo 'نقدا';
                  } elseif ($model->payWay == '1') {
                    echo 'آجل';
                  } elseif ($model->payWay == '2') {
                    echo 'دفعة على الحساب';
                  }
                  ?></span>
            <span></span>
          </div>

          <div>
            <span>رقـم الفاتورة:</span>
            <span>رقـم فاتورة المورد:</span>
            <span>تاريخ الفاتورة:</span>
            <span>طريقة الدفع:</span>
            <span></span>
          </div>
        </section>
        <section id="client-info">
          <span>تفاصيل العميل</span>
          <div>
            <span class="bold"><?= $model->c->name ?></span>
          </div>

          <div>
            <span><?= $model->c->phone ?></span>
          </div>

          <div>
            <span><?= $model->c->email ?></span>
          </div>

        </section>
      </div>
      <div class="invoice-body">
        <section id="items">

          <?php

          echo GridView::widget([
            'summary' => '',
            'dataProvider' => $dataProvider,
            'layout' => "{items}",

            'options' => ['style' => 'font-size:12px;'],
            'columns' => [
              ['class' => 'yii\grid\SerialColumn'],

              [
                'label' => Yii::t('app', 'Name'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'value' => function ($data) {
                  return $data->category0->name;
                }

              ],

              [
                'label' => Yii::t('app', 'Serial No'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'value' => function ($data) {
                  return $data->category0->serialNo;
                }
              ],



              [
                'label' => Yii::t('app', 'quantity'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'value' => function ($data) {
                  return $data->quantity;
                }

              ],

              [
                'label' => Yii::t('app', 'Cost Price'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'format' => ['decimal', 3],
                'value' => function ($data) {
                  return $data->costPrice;
                }

              ],

              [
                'label' => Yii::t('app', 'Total'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'format' => ['decimal', 3],
                'value' => function ($data) {
                  return $data->costPrice * $data->quantity;
                }

              ],
            ],
          ]);
          ?>

        </section>

        <section id="sums">

          <table cellpadding="0" cellspacing="0">
            <tr>
              <th>الاجمـــالي</th>
              <td><?php echo number_format($model->total, 3) . "\n" ?></td>
              <td></td>
            </tr>

            <tr>
              <th>المدفـوع</th>
              <td><?php echo number_format($model->paid, 3) . "\n" ?></td>
              <td></td>
            </tr>

            <tr>
              <th>الصـــافي</th>
              <td><?php echo number_format(($model->total) - $model->paid, 3) . "\n" ?></td>
              <td></td>
            </tr>

            <tr>
              <th>الرصيد المتبقي</th>
              <td><?php echo number_format($balance, 3) . "\n" ?></td>
              <td></td>
            </tr>
          </table>
        </section>
      </div>
    </div>
  </div>
</body>
<?php $this->registerCssFile("@web/css/template.css"); ?>

<script type="text/javascript">
  window.onload = function() {
    window.print();
  }
</script>

</html>