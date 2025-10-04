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
    <button class='btn btn-info' onClick="window.print()"><?= Yii::t('app', 'Print') ?></button>`
    <?php
    if ($model->type == 1) {
      echo Html::a(
        '<i class="fa fa-fw fa-edit"></i>' . ' ' . Yii::t('app', 'Update'),
        ['update', 'id' => $model->id],
        ['class' => 'btn btn-primary btn-sm']
      );
    } else {
      echo  Html::a(
        '<i class="fa fa-fw fa-edit"></i>' . ' ' . Yii::t('app', 'Update'),
        ['back-sale-update', 'id' => $model->id],
        ['class' => 'btn btn-primary btn-sm']
      );
    }

    ?>
    <?=
    Html::a('<i class="fa fa-fw fa-trash"></i>' . ' ' . Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
      'class' => 'btn btn-warning btn-sm',
      'data' => [
        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
        'method' => 'post',
      ],
    ])
    ?>

    <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-danger btnx  btn-sm']) ?>

  </p>
  <div>
    <div id="container">
      <div class="invoice-top">

        <section id="memo">
          <div class="logo">
            <img src=<?php echo $company->path ?> class="logo" alt="Cinque Terre">
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

        <section id="invoice-info">
          <div>
            <span><?= $model->billId ?></span>
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
            <span>تاريخ الفاتورة:</span>
            <span>طريقة الدفع:</span>
            <span></span>
          </div>

          <span class="clearfix"></span>

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

        <div class="clearfix"></div>
      </div>

      <div class="invoice-body">
        <section id="items">

          <?php

          echo GridView::widget([
            'summary' => '',
            'tableOptions' => ['id' => 'example'],
            'class' => 'table table-striped table-bordered',
            'dataProvider' => $providerSalesDetails,
            'layout' => "{items}",
            'options' => ['style' => 'font-size:12px;'],
            'columns' => [
              //  ['class' => 'yii\grid\SerialColumn'],

              [
                'label' => Yii::t('app', 'Name'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'headerOptions' => ['style' => 'width:30%'],

                'value' => function ($data) {
                  return $data->cat->name;
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
                'value' => function ($data) {
                  return $data->category0->commCode;
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
                'label' => Yii::t('app', 'quantity'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'headerOptions' => ['style' => 'width:5%'],
                'value' => function ($data) {
                  return $data->quantity;
                }

              ],

              [
                'label' => Yii::t('app', 'Place'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'headerOptions' => ['style' => 'width:10%'],

                'value' => function ($data) {
                  return $data->cat->place;
                }
              ],

              [
                'label' => Yii::t('app', 'Packing'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'headerOptions' => ['style' => 'width:15%'],
                // 'format' => ['decimal', 3],
                'value' => function ($data) {
                  return $data->packing;
                }
              ],

              [
                'label' => Yii::t('app', 'المخزن'),
                'contentOptions' => ['style' => 'font-size:12px;'],
                'headerOptions' => ['style' => 'width:15%'],
                // 'format' => ['decimal', 3],
                'value' => function ($data) {
                  return $data->branch0->name;
                }
              ],
            ],
          ]);
          ?>

        </section>

      </div>

    </div>
  </div>
</body>

<?php $this->registerCssFile("@web/css/template.css"); ?>

<!-- <script type="text/javascript">
      window.onload = function(printContent) { window.print(); }
 </script> -->

</html>