<?php
use yii\helpers\Html;
use app\models\CompanyInfo;
use yii\grid\GridView;
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
          
          <span class="clearfix"></span>

          </section>
        
      </div>

      <div class="invoice-body">
        <section id="items">
          
        <?php 

echo GridView::widget([
  'summary'=>'',
  'dataProvider' => $dataProvider,
  
  'columns' => [

      [
        'label' => Yii::t('app', 'Name'),
        'headerOptions' => ['style' => 'width:60%'],
        'value' => function ($data)
          {
            return $data->category0->name;
          }
      ],

      [
        'label' => Yii::t('app', 'Serial No'),
        'headerOptions' => ['style' => 'width:5px'],
        'value' => function ($data) {
            return $data->category0->serialNo;
        }

      ], 

      [
        'label' => Yii::t('app', 'Company'),
        'headerOptions' => ['style' => 'width:5px'],
        'value' => function ($data) {
            return $data->category0->company;
        }

      ],
    
      [
          'label' => Yii::t('app', 'Quantity'),
          'headerOptions' => ['style' => 'width:10%'],
          'value' => function($data)
          {
            return $data->quantity;
          }
          
      ],  
      
      [
        'label' => Yii::t('app', 'من الفرع'),
        'headerOptions' => ['style' => 'width:5px'],
        'value' => function ($data) {
            return $data->transfer0->fromBranch0->name;
        }

      ],

      [
        'label' => Yii::t('app', 'الى الفرع'),
        'headerOptions' => ['style' => 'width:5px'],
        'value' => function ($data) {
            return $data->transfer0->toBranch0->name;
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


  <script type="text/javascript">
      window.onload = function() { window.print(); }
 </script>


</html>
