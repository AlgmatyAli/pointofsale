<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\CompanyInfo;
?> 

<div class="row">
<button class='btn btn-info' onClick="window.print()"><?= Yii::t('app', 'Print')?></button>`

<div class="col-md-2"></div>

<div class="col-md-8">
     <?php $title = CompanyInfo::find()->select(['*'])->asArray()->one();?>

     <img src=<?php echo $title["path"]?> class="img-circle logo" alt="Cinque Terre">
     <h4><?php
      echo  $title['name'];
      echo '<br>';
      ?> </h4>
      <center>
         
      </center>
     <table class="table">
      <thead>
      <tr><td><b> التاريخ:</b></td>
          <td><b>  <?php if($model->type == 1){
            echo ' <b>ايصال قبض رقم</b>';
          }else{
            echo ' <b>ايصال صرف رقم</b>';
          }
          ?></b></td>
          <td><b> المبلغ:</h4></b></tr>
      </thead>
       <tbody>
          <tr><td><?php echo $model->at?></td>
          <td><?php echo $model->rId?></td>
          <td><?php echo number_format($model->value, 3)."\n" ?><b> <?=$model->currancy0->name?></b></td></tr>
       </tbody>
    </table>

      <table class="table">
      <thead>
      
      </thead>
       <tbody>
          <tr><td><b> استلمت انا:  </b><?php echo $model->c->name?></td></tr>
          <tr><td><b> مبلغ وقـدره:  </b> فقط <?php echo $model->value ?> <?=$model->currancy0->name?>
           لاغير </td></tr>
          <tr><td><b> ودلك مقابـل:  </b><?php echo $model->why?></td></tr>
       </tbody>
    </table>

    <table class="table">
       <tbody>
          <tr><td><b> اعتماد:</b>..........................</td>
          <td></td>
          <td></td>
          <td><h4> الرصيـد: <?php echo number_format($balance, 3)."\n"; ?>  </h4></td>
       </tbody>
    </table>
</div>
</div>

<div class="col-md-2">
</div>
<br>
<hr>
<br>
<div class="row">

<div class="col-md-2"></div>

<div class="col-md-8">
     <?php $title = CompanyInfo::find()->select(['*'])->asArray()->one();?>

     <img src=<?php echo $title["path"]?> class="img-circle logo" alt="Cinque Terre">
     <h4><?php
      echo  $title['name'];
      echo '<br>';
      ?> </h4>
      <center>
         
      </center>
     <table class="table">
      <thead>
      <tr><td><b> التاريخ:</b></td>
          <td><b>  <?php if($model->type == 1){
            echo ' <b>ايصال قبض رقم</b>';
          }else{
            echo ' <b>ايصال صرف رقم</b>';
          }
          ?></b></td>
          <td><b> المبلغ:</h4></b></tr>
      </thead>
       <tbody>
          <tr><td><?php echo $model->at?></td>
          <td><?php echo $model->rId?></td>
          <td><?php echo number_format($model->value, 3)."\n" ?><b> <?=$model->currancy0->name?></b></td></tr>
       </tbody>
    </table>

      <table class="table">
      <thead>
      </thead>
       <tbody>
          <tr><td><b> استلمت انا:  </b><?php echo $model->c->name?></td></tr>
          <tr><td><b> مبلغ وقـدره:  </b> فقط <?php echo $model->value ?> <?=$model->currancy0->name?>
           لاغير </td></tr>
          <tr><td><b> ودلك مقابـل:  </b><?php echo $model->why?></td></tr>
       </tbody>
    </table>

    <table class="table">
       <tbody>
          <tr><td><b> اعتماد:</b>..........................</td>
          <td></td>
          <td></td>
          <td><h4> الرصيـد: <?php echo number_format($balance, 3)."\n"; ?>  </h4></td>
       </tbody>
    </table>
</div>
</div>

<div class="col-md-2">
</div>
</div>
