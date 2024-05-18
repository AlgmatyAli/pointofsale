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
          <div> 
              <p>

                <button class='btn btn-info' onClick="window.print()"><?= Yii::t('app', 'Print') ?></button>`
                        
                <?= Html::a('<i class="fa fa-fw fa-print"></i>' . ' ' . Yii::t('app', 'Print without price'), ['noprice', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

                <?= Html::a('<i class="fa fa-fw fa-print"></i>' . ' ' . Yii::t('app', 'Print without price'), ['print-no-price', 'id' => $model->id], ['class' => 'btn btn-dark']) ?>

                <?php if ($model->type == 1 || $model->type == 4) {
                        echo Html::a(
                          '<i class="fa fa-fw fa-edit"></i>' . ' ' . Yii::t('app', 'Update'),
                          ['update', 'id' => $model->id],
                          ['class' => 'btn btn-info']
                        );
                      } else {
                        echo Html::a(
                          '<i class="fa fa-fw fa-edit"></i>' . ' ' . Yii::t('app', 'Update'),
                          ['back-sale-update', 'id' => $model->id],
                          ['class' => 'btn btn-info']
                        );
                      }

                      ?>
                        
                        <?= Html::a('<i class="fa fa-fw fa-trash "></i>' . ' ' . Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                          'class' => 'btn btn-warning pull-left',
                          'data' => [
                            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                          ],
                        ])
                  ?>  
                          
                  <?php echo Html::button('<i class="fa fa-fw fa-copy"></i>' . ' ' . Yii::t('app', 'SaveAsNew'), ['value' => Url::to(['sales/save-as-new', 'oldId' => $model->id]), 'class' => 'btn btn-success popup']); ?>
                      
                  <?= Html::a('<i class="fa fa-fw fa-envelope"></i>'.' '.Yii::t('app', 'Send By Email'), ['pdf', 'id'=> $model->id], ['class' => 'btn btn-warning']) ?>

                  <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-danger btnx']) ?>
              </p>
          </div>
   
          <section class="invoice">
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
          <img  src=<?php echo $company->path  ?> class="logo" alt="Logo"><?= $company->name?>
            <small class="pull-left"><?= $model->at?>
            <b> <?php 
                                      if ($model->type == 1) { ?>
                                        <h4 class="text-center white"><?= Yii::t('app', 'Sales Invoice'); ?></h4> 

                                        <?php 
                                      } elseif ($model->type == 2) { ?>
                                        <h4 class="text-center white"><?= Yii::t('app', 'Back Sales Invoice'); ?></h4> 
                                    
                                      <?php 
                                      } elseif ($model->type == 4) { ?>
                                      <h4 class="text-center white"><?= Yii::t('app', 'Proforma Invoice'); ?></h4> 

                                      <?php 
                                      } elseif ($model->type == 3) { ?>
                                      <h4 class="text-center white"><?= Yii::t('app', 'Reservation Invoice'); ?></h4> 
                                      
                                      <?php 
                                      }; ?>
                                      </b>
          </small>
          </h2>
        </div>
        <!-- /.col -->
      </div>
      <!-- info row -->
      <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
         من  
          <address>
            <strong><?=$company->name?></strong><br>
          
            <?=$company->address?><br>
            <?=$company->phone1?><br>
            <?= $company->email?>
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          الي
          <address>
            <strong><?=$model->c->name?></strong><br>
            <?=$model->c->address?><br>
            <?=$model->c->phone?><br>
            <?=$model->c->mobile?><br>
            <?=$model->c->email?>
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          
          <b>فاتورة رقم :</b> <?=$model->id?><br>
          <b> التاريخ:</b> <?= $model->at?><br>
          <b>طريقة الدفع:</b> <?php
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
                      ?>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
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
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <div class="row">
        <!-- accepted payments column -->
        <div class="col-xs-6">
         
          
       
          <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
          <br>
          <?=$company->terms?>   
          <br>
          </p>
        </div>
        <!-- /.col -->
        <div class="col-xs-6">
         

          <div class="table-responsive">
            <table class="table">
              <tbody><tr>
                <th style="width:50%">إجمالي الفاتورة:</th>
                <td><?php echo number_format($totalInvoice, 3) . "\n"; //$model->total?></td>
              </tr>
              <tr>
                <th>المدفوع</th>
                <td><?php echo number_format($model->paid, 3) . "\n"; ?></td>
              </tr>
              <tr>
              <th> <?= yii::t('app', 'Disscount') ?></th>
                <td><?php echo number_format($model->disscount, 3) . "\n"; ?> </td>
               
               
              </tr>
              <tr>
              <th>الصـــافي</th>
                <td><?php echo number_format($totalInvoice - $model->paid - $model->disscount, 3) . "\n"; ?></td>
              </tr>
              <tr>
              <th>الرصيد</th>
                <td> <?php echo number_format($balance, 3) . "\n"; ?></td>
              </tr>
            </tbody></table>
          </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- this row will not appear when printing -->
      
    </section>
      
  </body>

<?php $this->registerCssFile("@web/css/template.css"); ?>

  <script type="text/javascript">
      window.onload = function(printContent) { window.print(); }
 </script>
</html>
