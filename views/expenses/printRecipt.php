<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\CompanyInfo;
?> 

<div class="row">

<div class="col-md-4"></div>

<div class="col-md-8">
     <?php $title = CompanyInfo::find()->select(['*'])->asArray()->one();?>
    <div id='div1' class="site-about">
    <center>
     <img src=<?php echo $title["path"]?> class="img-circle logo" alt="Cinque Terre">
     <h3><?php
      echo  $title['work'];
      echo '<br><br>';
      echo  $title['name'];
     ?> </h3>
    </center>
     <br><br><br><br>
     <table class="table">
      <thead>
      <tr><td><h4> التاريخ:</h4></td>
          <td><h4> ايصال رقـم:</h4></td>
          <td><h4> المبلغ:</h4></td></tr>
      </thead>
       <tbody>
          <tr><td><h4><?php echo $model->at?></h4></td>
          <td><h4><?php echo $model->id?></h4></td>
          <td><h4><?php echo $model->value?> دينار</h4></td></tr>
       </tbody>
    </table>
  
     <br>
     <font size="4">
      <table class="table">
      <thead>
      
      </thead>
       <tbody>
          <tr><td> استلمت انـا: <?php echo $model->expenseTo?></td></tr>
          <tr><td> مبلغ وقـدره: <?php echo $model->value?>دينار</td></tr>
          <tr><td> ودلك مقابـل: <?php echo $model->why?></td></tr>
       </tbody>
    </table>
    </font>
    <br><br>
    <table class="table">
       <tbody>
          <tr><td><h4> اعتماد:.......................... </h4></td>
       </tbody>
    </table>
</div>
</div>

<div class="col-md-2">
</div>

</div>
