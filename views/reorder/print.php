<?php
use yii\helpers\Html;
use app\models\CompanyInfo;
use kartik\grid\GridView;
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
        $gridColumn = [
        // ['class' => 'yii\grid\SerialColumn'],

         [
          'label' => Yii::t('app', 'Name'),
          'contentOptions' => ['style' => 'font-size:14px;'],
          'headerOptions' => ['style' => 'width:40%'],
          'value' => function ($data) {
            return $data->category0->name;
          }
        ],

        [
          'label' => Yii::t('app', 'Company'),
          'value' => function ($data) {
            return $data->category0->company;
          }
        ],

        [
            'label' => Yii::t('app', 'Serial No'),
            'value' => function ($data)
            {
              return $data->category0->serialNo;
            }
            
        ],  

        [
          'label' => Yii::t('app', 'Comm Code'),
          'value' => function ($data)
          {
            return $data->category0->commCode;
          }
      ],  

        [
            'label' => Yii::t('app', 'quantity'),
            'value' => function ($data)
            {
              return $data->quantity;
            }
            
        ], 
    ]; 
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
       // 'filterModel' => $searchModel,
        'columns' => $gridColumn,
        'summary'=>'',
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-inventory']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
           // 'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title),
        ],
        // your toolbar can include the additional full export menu
        
    ]); ?>
        
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
