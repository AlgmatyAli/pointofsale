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

    <?= Html::a('<i class="fa fa-fw fa-print"></i>' . ' ' . Yii::t('app', 'Print without price'), ['noprice', 'id' => $model->id], ['class' => 'btn btn-secondary']) ?>

    <?= Html::a('<i class="fa fa-fw fa-print"></i>' . ' ' . Yii::t('app', 'Print without price'), ['print-no-price', 'id' => $model->id], ['class' => 'btn btn-success']) ?>

    <?php if ($model->type == 1 || $model->type == 4) {
      echo Html::a(
        '<i class="fa fa-fw fa-edit"></i>' . ' ' . Yii::t('app', 'Update'),
        ['update', 'id' => $model->id],
        ['class' => 'btn btn-primary']
      );
    } else {
      echo Html::a(
        '<i class="fa fa-fw fa-edit"></i>' . ' ' . Yii::t('app', 'Update'),
        ['update', 'id' => $model->id],
        ['class' => 'btn btn-primary']
      );
    }

    ?>

    <?php //echo Html::button('<i class="fa fa-fw fa-copy"></i>' . ' ' . Yii::t('app', 'SaveAsNew'), ['value' => Url::to(['sales/save-as-new', 'oldId' => $model->id]), 'class' => 'btn btn-info popup']); 
    ?>

    <?= Html::a('<i class="fa fa-fw fa-envelope"></i>' . ' ' . Yii::t('app', 'Send By Email'), ['pdf', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>

    <?= Html::a(
      Yii::t('app', 'Create Client Histrans'),
      ['histrans', 'client' => $model->clinet, 'allData' => 1, 'type' => $model->c->type],
      ['class' => 'btn btn-info']
    ) ?>

    <?= Html::a('<i class="fa fa-fw fa-print"></i>' . ' ' . Yii::t('app', 'Save AS PDF'), ['save-pdf', 'id' => $model->id], ['class' => 'btn btn-success']) ?>

    <?php
    if ($model->deleviried == 0) {
      echo Html::a(Yii::t('app', 'تغيير حالة الفاتورة'), ['delev', 'id' => $model->id], ['class' => 'btn btn-warning']);
    }
    ?>

    <?php
      echo Html::a(Yii::t('app', 'ايقاف الدين على الزبون'), ['stop-credit', 'id' => $model->clinet], ['class' => 'btn btn-primary']);
    ?>

    <?= Html::a('<i class="fa fa-fw fa-trash "></i>' . ' ' . Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
      'class' => 'btn btn-danger',
      'data' => [
        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
        'method' => 'post',
      ],
    ])
    ?>

  </div>
  <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-warning pull-left']) ?>
  </p>

  <div>
    <div id="container">
      <div class="invoice-top">

        <section id="memo" class="text-center">
          <div class="pull-left">
            <img src=<?php
                      echo $company->path
                      ?> class="logo" alt="Cinque Terre">
          </div>
          <br><br>

          <div class="company-info">
            <span class="company-name"><?php echo $company->name ?> </span>

            <span class="spacer"></span>
            <span class="company-name1"><?php echo $company->work ?> </span>

            <span class="spacer"></span>
            <div></div>
            <div><?php echo $company->address ?> </div>

            <span class="clearfix"></span>

            <div><?php echo $company->phone1 ?> |</div>
            <div><?php echo $company->phone2 ?> </div>
          </div>

        </section>

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
                'headerOptions' => ['style' => 'width:25%'],

                'value' => function ($data) {
                  return $data->cat->company;
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
                'label' => Yii::t('app', 'Price'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'headerOptions' => ['style' => 'width:20%'],
                'format' => ['decimal', 3],

                'value' => function ($data) {
                  return $data->salePrice;
                }

              ],

              [
                'label' => Yii::t('app', 'Total'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'headerOptions' => ['style' => 'width:20%'],
                'format' => ['decimal', 3],
                'value' => function ($data) {
                  return $data->salePrice * $data->quantity;
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
              <td><?php echo
                  number_format((float) $totalInvoice, 3);
                  //number_format($totalInvoice, 3) . "\n"; //$model->total
                  ?></td>
              <td></td>
            </tr>

            <tr>
              <th>المدفـوع</th>
              <td><?php echo number_format($model->paid, 3) . "\n"; ?></td>
              <td></td>
            </tr>
            <tr>
              <th><?= yii::t('app', 'Disscount') ?></th>
              <td><?php echo number_format($model->disscount, 3) . "\n"; ?> </td>
              <td></td>
            </tr>
            <tr>
              <th>الصـــافي</th>
              <td><?php echo number_format($totalInvoice - $model->paid - $model->disscount, 3) . "\n"; ?></td>
              <td></td>
            </tr>
            <th>الرصيد</th>
            <td><?php echo number_format($balance, 3) . "\n"; ?></td>
            <td></td>
            </tr>
          </table>

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
          <div class="row">
            <div class="col-md-12">
              <span>الشروط والأحكام:</span>
              <?= $company->terms ?>
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