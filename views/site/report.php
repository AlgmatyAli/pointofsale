<?php

use yii\helpers\Html;
use yii\widgets\DetailView;


/* @var $this yii\web\View */
/* @var $model app\models\Sales */

\yii\web\YiiAsset::register($this);
?>

<div class="sales-report">

  <div class="row">
    <div class="col-md-4">
      <?= Html::a(Yii::t('app', 'تقرير عن المبيعات'), ['/sales/index'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'كشف حساب صنف '), ['/category/histrans'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'كشف حساب صنف حسب العميل '), ['/category/histrans-by-client'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'كشف حساب عميل'), ['/client/histrans'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'تقرير باجمالي الديون'),  ['/client/credts', 'type' => '0,1,2'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'جرد الأصناف '), ['/stocks/index'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'تقرير المشتريات'), ['/purchases/index', 'type' => 1], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'Purchses Details Report'), ['/purchases-details/index'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'تقرير عن ايصالات القبض '), ['/receipt/index'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'تقرير عن ايصالات الصرف '), ['/receipt/index_'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'تقرير الخزينة اليومية (الصندوق)'), ['/sales/ftran'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app',  'قائمة الأسعار'), ['/prices'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app',  'قائمة الأسعار بالكميات'), ['/prices/index-with-qnty'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'اصناف رصيد الجرد غير مطابق مع كشف الحساب'), ['/site/q-balance'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'قائمة الانتظار حسب الأصناف'), ['/sales/wait-qnty'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'قائمة الانتظار حسب الزبائن'), ['/sales/wait-qnty-client'], ['class' => 'btn btn-default btn-block']) ?>

      <br>
      <hr>
      <?= Html::a(Yii::t('app', 'Debt report in other currencies'), ['/balance/index'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'Detailed debt report in other currencies'), ['/balance-history/histrans'], ['class' => 'btn btn-default btn-block']) ?>
    </div>

    <div class="col-md-4">
      <?php //Html::a(Yii::t('app', 'تقرير مسترجع المشتريات'), ['/purchases/index', 'type' => 2], ['class' => 'btn btn-default btn-block']) ?>

      <?php //Html::a(Yii::t('app', 'تقرير المشتريات المعلقة'), ['/purchases/index', 'type' => 3], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'تقرير عن الايصالات الملغية '), ['/receipt-arch/index'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'كشف حساب عميل تفصيلي'), ['/client'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'تقرير عن المصروفات'), ['/expenses/index'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', ' الاصناف التي وصلت للحد الادنى'), ['/category/reorder'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'تقرير عن الجرد النهائي'), ['/arrangement/stock-taking'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', ' تقرير الأربـاح'), ['/sales/profit'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', ' تقرير صافي الأرباح'), ['/sales/net-profit'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'تقرير بالأصناف الغير مجرودة'), ['/inventory/stock-taking'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'Ftran'), ['/sales/ftran'], ['class' => 'btn btn-default btn-block']) ?>
    </div>

    <div class="col-md-4">
      <?= Html::a(Yii::t('app', 'تقرير تفصيلي عن طلبيات الشراء '), ['/reorder/index'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'تقرير عن مبيعات صنف'), ['/sales-details/index'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app',  'قائمة الاصناف بالأسعار'),  ['/inventory/list'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'تقرير الخزينة الرئيسية'), ['/safe/index'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'تقرير المقاصة بين الفروع'), ['/transfer/index'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'تقرير عن  نقل  الأصناف بين الفروع'), ['/transfer-items/index'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'تقرير عن تسوية رصيد الجرد'), ['/arrangement/index'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'قائمة الأصناف بأسعار البيع'), ['/inventory/price-list'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'Catalogue'), ['/prices/catalogue'], ['class' => 'btn btn-default btn-block']) ?>

      <?= Html::a(Yii::t('app', 'Stagnant'), ['/category/stagnant'], ['class' => 'btn btn-default btn-block']) ?>
    </div>
  </div>


</div>