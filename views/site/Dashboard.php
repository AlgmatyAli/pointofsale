<?php

use yii\helpers\Html;
use yii\widgets\DetailView;


/* @var $this yii\web\View */
/* @var $model app\models\Sales */

\yii\web\YiiAsset::register($this);
?>

<div class="sales-report">

<div class="row">
        <div class="col-md-6">
           <?= Html::a(Yii::t('app',  'بيانات الشركة'),  ['/company-info'], ['class' => 'btn btn-default btn-block']) ?>
           <?= Html::a(Yii::t('app',  'بيانات المستخدمين'),  ['/user'], ['class' => 'btn btn-default btn-block']) ?>
           <?= Html::a(Yii::t('app',  'تعريف الصلاحيات والادونات'), ['auth-item/index'], ['class' => 'btn btn-default btn-block']) ?>
           <?= Html::a(Yii::t('app',  'تسجيل الفروع '),['/branches'], ['class' => 'btn btn-default btn-block']) ?>
           <?= Html::a(Yii::t('app',  'تسجيل بنود المصاريف'),  ['/items'], ['class' => 'btn btn-default btn-block']) ?>
           <?= Html::a(Yii::t('app',  'Agents'),  ['/agent'], ['class' => 'btn btn-default btn-block']) ?>
           <?= Html::a(Yii::t('app',  'Prices Update'),  ['/prices/create1'], ['class' => 'btn btn-default btn-block']) ?>
                </div>
       <div class="col-md-6">
           <?= Html::a(Yii::t('app',  'Customs Declarations'),  ['/customs-declaration'], ['class' => 'btn btn-default btn-block']) ?>
           <?= Html::a(Yii::t('app',  'Shipment Registeration'),  ['/shipment-data'], ['class' => 'btn btn-default btn-block']) ?>
           <?= Html::a(Yii::t('app',  'Customs Office Registeration'),  ['/customs-office'], ['class' => 'btn btn-default btn-block']) ?>
           <?= Html::a(Yii::t('app',  'Customs Office Histrans'),  ['/customs-office/histrans'], ['class' => 'btn btn-default btn-block']) ?>

      </div>               

</div>


</div>
