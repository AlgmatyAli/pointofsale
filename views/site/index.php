<?php

use yii\helpers\Html;
use dosamigos\chartjs\ChartJs;
use kartik\ipinfo\IpInfo;
use kartik\popover\PopoverX;

/* @var $this yii\web\View */

//$this->title = Yii::t('app', 'App');

?>
<?php $this->title = Yii::t('app', 'مرحبا بـك ') . Yii::$app->user->identity->username . '!'; ?>

<div class="site-index">

  <section class="content">

    <div class="row">
      <div class="col-md-4">
        <h3><?= Html::encode($this->title); ?></h3>
      </div>
      <div class="col-md-4"></div>
      <div class="col-md-4">
        
        <h3> السنة- <?php echo date('Y') ?> 
        <?php 
       echo IpInfo::widget([
        'showFlag' => true,
        'showPopover' => true,
        'template' => ['inlineContent'=>'{flag} {city} {countryCode}'],
    ]);
        ?>
        </h3>
      </div>
    </div>
    <hr>
    <!-- Apply any bg-* class to to the info-box to color it -->
    <div class='row'>
      <div class="col-md-4">
        <div class="info-box bg-red">
          <span class="info-box-icon"><i class="fa fa-bell-o"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">اصناف وصلت للحد الأدنى</span>
            <span class="info-box-number"><?php echo  $reorder ?></span>
            <!-- The progress section is optional -->
            <div class="progress">
              <div class="progress-bar" style="width: 70%"></div>
            </div>
            <span class="progress-description">
              <a href="?r=reorderitems/index" style="color:white"><b> لعرض الاصناف اضغط هنا...</a>
            </span>
          </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
      </div>
      <div class="col-md-4">
        <div class="info-box bg-green">
          <span class="info-box-icon"><i class="fa fa-folder-open-o"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">فواتير مشتريات لم يتم اعتمادها</span>
            <span class="info-box-number"><?php echo $tempPurchase ?></span>
            <!-- The progress section is optional -->
            <div class="progress">
              <div class="progress-bar" style="width: 70%"></div>
            </div>
            <span class="progress-description">
              <a href="?r=temp-invoice-purchase%2Fcreate" style="color:white"><b> لعرض الفواتير اضغط هنا...</a>
            </span>
          </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
      </div>
      <div class="col-md-4">
        <div class="info-box bg-aqua">
          <span class="info-box-icon"><i class="fa fa-eye"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">فواتير مبيعات لم يتم اعتمادها</span>
            <span class="info-box-number"><?php echo $tempSales ?></span>
            <!-- The progress section is optional -->
            <div class="progress">
              <div class="progress-bar" style="width: 70%"></div>
            </div>
            <span class="progress-description">
              <a href="?r=temp-invoice%2Fcreate&type=1" style="color:white"><b> لعرض الفواتير اضغط هنا...</a>
            </span>
          </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
      </div>
    </div>

    <div class='row'>
      <div class="col-md-4">
        <div class="info-box bg-yellow">
          <span class="info-box-icon"><i class="fa fa-heart"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">الأصناف الأكثر رواجا، قاربت على الانتهاء</span>
            <span class="info-box-number"><?php echo  $request ?></span>
            <!-- The progress section is optional -->
            <div class="progress">
              <div class="progress-bar" style="width: 70%"></div>
            </div>
            <span class="progress-description">
              <a href="?r=category/more-request" style="color:white"><b> لعرض الاصناف اضغط هنا...</a>
            </span>
          </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
      </div>

      <div class="col-md-4">
        <div class="info-box bg-blue">
          <span class="info-box-icon"><i class="fa fa-heart"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">فواتير لم يتم تسليمها</span>
            <span class="info-box-number"><?php echo  $deleviried ?></span>
            <!-- The progress section is optional -->
            <div class="progress">
              <div class="progress-bar" style="width: 70%"></div>
            </div>
            <span class="progress-description">
              <a href="?r=sales%2Findex&SalesSearch%5Bdeleviried%5D=0" style="color:white"><b> لعرض الفواتير اضغط هنا...</a>
            </span>
          </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
      </div>
      <div class="col-md-4">
        <div class="info-box bg-purple">
          <span class="info-box-icon"><i class="fa fa-bell-o"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">فواتير مستحقة حان موعد تسديدها</span>
            <span class="info-box-number"><?php echo  $amount ?></span>
            <!-- The progress section is optional -->
            <div class="progress">
              <div class="progress-bar" style="width: 70%"></div>
            </div>
            <span class="progress-description">
              <a href="?r=sales/deserving" style="color:white"><b> لعرض الفواتير اضغط هنا...</a>
            </span>
          </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
      </div>
      <div class="col-md-4">
        <div class="info-box bg-purple">
          <span class="info-box-icon"><i class="fa fa-bell-o"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">فواتير مشتريات حان موعد وصولها </span>
            <span class="info-box-number"><?php echo  $dateOfArrival['dateOfArrival'] ?></span>
            <!-- The progress section is optional -->
            <div class="progress">
              <div class="progress-bar" style="width: 70%"></div>
            </div>
            <span class="progress-description">
              <a href="?r=purchases/date-of-arrival" style="color:white"><b> لعرض الفواتير اضغط هنا...</a>
            </span>
          </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
      </div>
      <div class="col-md-4">
        <div class="info-box bg-aqua">
          <span class="info-box-icon"><i class="fa fa-bell-o"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">اصناف سعر البيع فيها أقل من سعر التكلفة</span>
            <span class="info-box-number"><?php echo $compare ?></span>
            <!-- The progress section is optional -->
            <div class="progress">
              <div class="progress-bar" style="width: 70%"></div>
            </div>
            <span class="progress-description">
              <a href="?r=prices/compare" style="color:white"><b> لعرض الفواتير اضغط هنا...</a>
            </span>
          </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
      </div>
      <div class="col-md-4">
        <div class="info-box bg-red">
          <span class="info-box-icon"><i class="fa fa-bell-o"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">اصناف كمياتها أقل من الصفر</span>
            <span class="info-box-number"><?php echo $zeroQ ?></span>
            <!-- The progress section is optional -->
            <div class="progress">
              <div class="progress-bar" style="width: 70%"></div>
            </div>
            <span class="progress-description">
              <a href="?r=stocks/zero-q" style="color:white"><b> لعرض الفواتير اضغط هنا...</a>
            </span>
          </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
      </div>
    </div>
    <hr>
    <div class="row">
      <div class="col-sm-6">
        <?php
        foreach ($infos as $info) :
          $bestCustomer[] = $info["cleintName"];
          $count[] = $info['countt'];
        ?>
        <?php endforeach; ?>

        <?php
        if (Yii::$app->user->identity->client == null) {
          echo ChartJs::widget([
            'options' => [
              'height' => 300,
              'width' => 400,
            ],
            'type' => 'doughnut',
            'clientOptions' => [
              'legend' => [
                'display' => true,
                'position' => 'bottom',
                'labels' => [
                  'fontSize' => 12,
                  'fontColor' => "#425062",
                ]
              ],
              'tooltips' => [
                'enabled' => true,
                'intersect' => true
              ],
              'hover' => [
                'mode' => true
              ],
              'maintainAspectRatio' => true,
              'scales' => [
                'yAxes' => [
                  [
                    'ticks' => [
                      'beginAtZero' => true,
                      'precision' => '0'
                    ]
                  ]
                ]
              ]
            ],
            'data' => [
              'labels' => $bestCustomer,
              'datasets' => [
                [
                  'data' => $count,
                  'label' => Yii::t('app', 'Number Of Bill'),
                  'backgroundColor' => [
                    '#1d96f6',
                    '#631a78',
                    '#10bad1',
                    '#de5c79',
                    '#e907c4',
                    '#74107d',
                    '#f15932',
                    '#c4520b',
                    '#ef409f',
                    '#b5d6dc',
                    '#fa9c45',
                    '#631a78',
                    '#1d96f6',
                  ],
                  'borderColor' => [
                    '#fff',
                    '#fff',
                    '#fff'
                  ],
                  'borderWidth' => 2,
                  'hoverBorderColor' => ["#999", "#999", "#999"],
                ]
              ]
            ]
          ]);
        }
        ?>
      </div>

      <div class="col-sm-6">
        <?php
        foreach ($details as $detail) :
          $bestCategory[] = $detail["categoryName"];
          $counts[] = $detail['counts'];
        ?>
        <?php endforeach; ?>
        <?=
        ChartJs::widget([
          'options' => [
            'height' => 300,
            'width' => 400,
          ],
          'type' => 'bar',
          'clientOptions' => [
            'legend' => [
              'display' => true,
              'position' => 'bottom',
              'labels' => [
                'fontSize' => 12,
                'fontColor' => "#425062",
              ]
            ],
            'tooltips' => [
              'enabled' => true,
              'intersect' => true
            ],
            'hover' => [
              'mode' => true
            ],
            'maintainAspectRatio' => true,
            'scales' => [
              'yAxes' => [
                [
                  'ticks' => [
                    'beginAtZero' => true,
                    'precision' => '0'
                  ]
                ]
              ]
            ]
          ],
          'data' => [
            'labels' => $bestCategory,
            'datasets' => [
              [
                'data' => $counts,
                'label' => Yii::t('app', 'Number Of Category'),
                'backgroundColor' => [
                  '#adc3fe',
                  '#10bad1',
                  '#de5c79',
                  '#e907c4',
                  '#74107d',
                  '#f15932',
                  '#c4520b',
                  '#ef409f',
                  '#b5d6dc',
                  '#fa9c45',
                  '#631a78',
                  '#1d96f6',
                ],
                'borderColor' => [
                  '#fff',
                  '#fff',
                  '#fff'
                ],
                'borderWidth' => 2,
                'hoverBorderColor' => ["#999", "#999", "#999"],
              ]
            ]
          ]
        ]);
        ?>
      </div>
    </div>
    <hr>
    <div class="row">
      <div class="col-sm-4">
        <?php
        /* echo
     \yii2fullcalendar\yii2fullcalendar::widget(array( 
       'options' => [
        'lang' => 'ar-LY',
        //... more options to be defined here!
      ],
      'events'=> $events,
     ));
     */
        ?>
      </div>

    </div>
</div>