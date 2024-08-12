<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\models\CompanyInfo;

/* @var $this yii\web\View */
/* @var $model app\models\Salaryroll */
?>
<hr>
<div class="row">

  <div class="col-md-12">

    <p>
      <button class='btn btn-info' onClick="window.print()"><?= Yii::t('app', 'Print') ?></button>`
      <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer, ['class' => 'btn btn-danger btnx']) ?>
    </p>

    <div id='div1' class="site-about">

      <?php $title = CompanyInfo::find()->select(['*'])->asArray()->one(); ?>

      <img src=<?php echo $title["path"] ?> class="img-circle logo" alt="Cinque Terre">
      <h4><?php
          echo '<br><br>';
          echo  $title['name'];
          echo '<br><br>';
          ?> </h4>
      <br><br>
      <center>
        <div>
          <h3>قائمة بالاصناف الراكدة<b><?php // date('Y-m-d') 
                                        ?></b> </h3>
        </div>
      </center>
      <br><br>

      <?php
      $gridColumn = [
        'id',
        'serialNo',
        'name',
        'company',
        'quantity',
        'costPrice',
        'maxPrice'
      ];
      ?>
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'summary' => 'قائمة بالاصناف الراكدة',
        'columns' => $gridColumn,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-prices']],
        'panel' => [
          'type' => GridView::TYPE_PRIMARY,
          'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title),
        ],

      ]); ?>

    </div>

  </div>