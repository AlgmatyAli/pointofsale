<?php
use yii\helpers\Html;
use app\models\CompanyInfo;
use yii\helpers\Url;
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

          <!-- </section> -->
        
      </div>

      <div class="invoice-body">
        <section id="items">
        <?php 
    $gridColumn = [
       // ['class' => 'yii\grid\SerialColumn'],
        ['attribute' => 'id', 'visible' => false],

        [
           // 'class'=>'kartik\grid\EditableColumn',
            'attribute' => Yii::t('app', 'category'),
            'label'=>Yii::t('app', 'ID'),
            'contentOptions' => ['style' => 'font-size:14px;'],
            'headerOptions' => ['style' => 'width:10%'],
        ],

        [
            'label' => Yii::t('app', 'Name'),
            'contentOptions' => ['style' => 'font-size:12px;'],
            'value' => function ($data)
              {
                return $data->category0->name;
              }
              
          ],

        'category0.company',

        [
            'label' => Yii::t('app', 'Sale Price'),
            'format' => 'raw',
            'value' => function ($data) {
                return $data->salePrice;
            }
        ],
    
    ];  
    ?>
    <br>
   <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{items}{pager}',
        'summary'=>false,
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' =>[

            'neverTimeout'=>true,
    
            'options'=>[
    
                    'id'=>'w0',
                ]
            ],  
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-temp-invoice']],
        //'showPageSummary' => true,
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
           // 'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title),
        ],
        // your toolbar can include the additional full export menu
        
    ]); ?>
         
          
        </section>
        <
        
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
