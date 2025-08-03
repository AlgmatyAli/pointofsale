<?php

use yii\helpers\Html;
use app\models\CompanyInfo;
use yii\helpers\Url;
use yii\grid\GridView;
?>

<!DOCTYPE html>
<html lang="ar">

<head>
  <meta charset="utf-8">
</head>

<body id='div1'>
  <p>
  <div class="btn-group">
    <button class='btn btn-primary' onClick="window.print()"><?= Yii::t('app', 'Print') ?></button>`

  </div>
  <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-warning pull-left']) ?>
  </p>

  <div>
    <div id="container">
      <div class="invoice-top">

        <?php
        if ($model->type == 1) { ?>
          <h2 class="text-center white"><?= Yii::t('app', 'Sales Invoice'); ?></h2>

        <?php
        } elseif ($model->type == 2) { ?>
          <h2 class="text-center white"><?= Yii::t('app', 'Back Sales Invoice'); ?></h2>

        <?php
        } elseif ($model->type == 4) { ?>
          <h2 class="text-center white"><?= Yii::t('app', 'Proforma Invoice'); ?></h2>

        <?php
        } elseif ($model->type == 3) { ?>
          <h2 class="text-center white"><?= Yii::t('app', 'Reservation Invoice'); ?></h2>

        <?php
        }; ?>

        <section id="invoice-info">
          <div>
            <span><?= $model->billId ?></span>
            <span><?= $model->at ?></span>
            <span><?= Yii::$app->formatter->asTime($model->created_at) ?></span>
            <span><?php
                  if ($model->deleviryAt == null) {
                    echo '';
                  } else {
                    echo $model->deleviryAt;
                  }
                  ?></span>
            <span><?php
                  if ($model->type == 1 || $model->type == 3 || $model->type == 4) {
                    if ($model->payWay == '0') {
                      echo 'نقدا';
                    } elseif ($model->payWay == '1') {
                      echo 'آجل';
                    } elseif ($model->payWay == '2') {
                      echo 'دفعة على الحساب';
                    }
                  }
                  if ($model->type == 2) {
                    if ($model->payWay == '1') {
                      echo 'نقدا';
                    } elseif ($model->payWay == '2') {
                      echo 'آجل';
                    }
                  }
                  ?></span>
            <span></span>
          </div>

          <div>
            <span>رقـم الفاتورة:</span>
            <span>تاريخ الفاتورة:</span>
            <span>توقيت الفاتورة:</span>
            <span>تاريخ التسليم:</span>
            <span>طريقة الدفع:</span>
            <span></span>
          </div>

          <!-- <span class="clearfix"></span> -->

        </section>

        <section id="client-info">
          <span>تفاصيل الزبون</span>
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

        <!-- <div class="clearfix"></div> -->
      </div>

      <div class="invoice-body">
        <section id="items">



          <?php

          echo GridView::widget([
            'summary' => '',
            'dataProvider' => $providerSalesDetails,
            'layout' => "{items}",
            'options' => ['style' => 'font-size:12px;'],

            'columns' => [
              ['class' => 'yii\grid\SerialColumn'],

              [
                'label' => Yii::t('app', 'Name'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'headerOptions' => ['style' => 'width:50%'],

                'value' => function ($data) {
                  return $data->cat->name;
                }
              ],

              [
                'label' => Yii::t('app', 'Company'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'headerOptions' => ['style' => 'width:15%'],

                'value' => function ($data) {
                  return $data->cat->company;
                }
              ],

              [
                'label' => Yii::t('app', 'Serial No'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'headerOptions' => ['style' => 'width:15%'],

                'value' => function ($data) {
                  return $data->cat->serialNo;
                }
              ],

              [
                'label' => Yii::t('app', 'Comm Code'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'headerOptions' => ['style' => 'width:15%'],

                'value' => function ($data) {
                  return $data->cat->commCode;
                }
              ],

              [
                'label' => Yii::t('app', 'quantity'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'headerOptions' => ['style' => 'width:15%'],
                'value' => function ($data) {
                  return $data->quantity;
                }
              ],

              [
                'label' => Yii::t('app', 'الكمية المتبقية'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'headerOptions' => ['style' => 'width:15%'],
                'value' => function ($data) {
                  return $data->Qtotalinventory;
                }
              ],

              [
                'label' => Yii::t('app', 'الكمية المتبقية بالمخازن'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'headerOptions' => ['style' => 'width:15%'],
                'value' => function ($data) {
                  return $data->otherQtotalinventory;
                }
              ],

              [
                'label' => Yii::t('app', 'الكمية المحجوزة'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'headerOptions' => ['style' => 'width:15%'],
                'value' => function ($data) {
                  return $data->reservation;
                }
              ],

              [
                'label' => Yii::t('app', 'مكان الصنف'),
                'contentOptions' => ['style' => 'font-size:12px;', 'text-align: center'],
                'headerOptions' => ['style' => 'width:30%'],
                'value' => function ($data) {
                  return $data->cat->place;
                }

              ],
            ],
          ]);
          ?>

        </section>
        <!-- <div class="clearfix"></div> -->
        <br><br><br>
        <section id="terms">
          <div class="row">
            <div class="col-md-12">
              <span class="noprint"> ملاحظـات:
                <?= $model->notes ?>
              </span>
            </div>

          </div>
      </div>

      </section>

    </div>

  </div>
  </div>
</body>

<?php $this->registerCssFile("@web/css/template.css"); ?>

<!-- <script type="text/javascript">
    window.onload = function(printContent) {
    window.print();
  }
</script> -->

</html>