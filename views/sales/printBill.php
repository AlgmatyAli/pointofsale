<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Sales $model */
/** @var app\models\CompanyInfo $company */
/** @var yii\data\ActiveDataProvider $providerSalesDetails */

// عرض الفاتورة بتخطيط مستقل (يعرض الشريط الجانبي والعلوي، ولا يدخلان في الطباعة) (يحمّل template.css تلقائياً)
$this->context->layout = 'invoice';

// مسار الشعار: إن كان نسبياً نجعله نسبةً إلى مجلد web
$logo = (string) $company->path;
if ($logo !== '' && !preg_match('~^(https?:)?//|^/~', $logo)) {
  $logo = Yii::getAlias('@web/') . ltrim($logo, '/');
}

$client = $model->c; // قد يكون null

$payWayText = ['0' => 'نقدا', '1' => 'آجل', '2' => 'دفعة على الحساب'][(string) $model->payWay] ?? '';
?>

<!-- شريط الأزرار: لا يُطبع -->
<?= $this->render('_flash') ?>
<div class="no-print d-print-none mb-3 d-flex flex-wrap justify-content-between gap-2">
  <div class="d-flex flex-wrap gap-2">
    <button type="button" class="btn btn-info" onclick="printInvoice()">
      <i class="bi bi-printer"></i> <?= Yii::t('app', 'Print') ?>
    </button>

    <?= Html::a(
      '<i class="bi bi-pencil-square"></i> ' . Yii::t('app', 'Update'),
      ['update', 'id' => $model->id],
      ['class' => 'btn btn-primary']
    ) ?>

    <?= Html::a(
      '<i class="bi bi-trash"></i> ' . Yii::t('app', 'Delete'),
      ['delete', 'id' => $model->id],
      [
        'class' => 'btn btn-warning',
        'data' => [
          'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
          'method' => 'post',
        ],
      ]
    ) ?>
  </div>

  <?= Html::a(
    Yii::t('app', 'Cancel'),
    ['/site/index'], // الخروج من الطباعة يرجع للرئيسية دائماً
    ['class' => 'btn btn-danger']
  ) ?>
</div>

<!-- الفاتورة بدون أسعار -->
<div id="div1">
  <div id="container">

    <div class="invoice-top">

      <section id="memo">
        <div style="float:left">
          <?php if ($logo !== ''): ?>
            <img src="<?= Html::encode($logo) ?>" class="logo" alt="<?= Html::encode($company->name) ?>">
          <?php endif; ?>
        </div>
        <br><br>

        <div class="company-info">
          <span class="company-name"><?= Html::encode($company->name) ?> </span>

          <span class="spacer"></span>
          <span class="company-name1"><?= Html::encode($company->work) ?> </span>

          <span class="spacer"></span>
          <div></div>
          <div><?= Html::encode($company->address) ?> </div>

          <span class="clearfix"></span>

          <div><?= Html::encode($company->phone1) ?> |</div>
          <div><?= Html::encode($company->phone2) ?> </div>
        </div>
      </section>

      <section id="invoice-info">
        <div>
          <span><?= Html::encode((string) $model->billId) ?></span>
          <span><?= Html::encode((string) $model->at) ?></span>
          <span><?= Html::encode($payWayText) ?></span>
          <span></span>
        </div>

        <div>
          <span>رقـم الفاتورة:</span>
          <span>تاريخ الفاتورة:</span>
          <span>طريقة الدفع:</span>
          <span></span>
        </div>
      </section>

      <section id="client-info">
        <span>تفاصيل الزبون</span>
        <div><span class="bold"><?= Html::encode((string) $client?->name) ?></span></div>
        <div><span><?= Html::encode((string) $client?->phone) ?></span></div>
        <div><span><?= Html::encode((string) $client?->email) ?></span></div>
      </section>

      <div class="clearfix"></div>
    </div>

    <div class="invoice-body">
      <section id="items">
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
              'headerOptions' => ['style' => 'width:15%'],

              'value' => function ($data) {
                return $data->cat->company;
              }
            ],

            [
              'label' => Yii::t('app', 'Serial No'),
              'contentOptions' => ['style' => 'font-size:12px;'],
              'headerOptions' => ['style' => 'width:15%'],

              'value' => function ($data) {
                return $data->cat->serialNo;
              }
            ],

            [
              'label' => Yii::t('app', 'Comm Code'),
              'contentOptions' => ['style' => 'font-size:12px;'],
              'headerOptions' => ['style' => 'width:15%'],

              'value' => function ($data) {
                return $data->cat->commCode;
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
              'label' => Yii::t('app', 'الكمية المتبقية'),
              'contentOptions' => ['style' => 'font-size:12px;'],
              'headerOptions' => ['style' => 'width:15%'],
              'value' => function ($data) {
                return $data->Qtotalinventory;
              }
            ],

            [
              'label' => Yii::t('app', 'الكمية المتبقية بالمخازن'),
              'contentOptions' => ['style' => 'font-size:12px;'],
              'headerOptions' => ['style' => 'width:15%'],
              'value' => function ($data) {
                return $data->otherQtotalinventory;
              }
            ],

            [
              'label' => Yii::t('app', 'الكمية المحجوزة'),
              'contentOptions' => ['style' => 'font-size:12px;'],
              'headerOptions' => ['style' => 'width:15%'],
              'value' => function ($data) {
                return $data->reservation;
              }
            ],

            [
              'label' => Yii::t('app', 'المخزن'),
              'contentOptions' => ['style' => 'font-size:12px;'],
              'headerOptions' => ['style' => 'width:15%'],
              'value' => function ($data) {
                return $data->branchName;
              }
            ],

            [
              'label' => Yii::t('app', 'مكان الصنف'),
              'contentOptions' => ['style' => 'font-size:12px;', 'text-align: center'],
              'headerOptions' => ['style' => 'width:30%'],
              'value' => function ($data) {
                return $data->cat->place;
              }

            ],
          ],
        ]);
        ?>

      </section>
      <!-- <div class="clearfix"></div> -->
      <br><br><br>
      <section id="terms">
        <div class="row">
          <div class="col-md-12">
            <span class="noprint"> ملاحظـات:
              <?= Html::encode((string) $model->notes) ?>
            </span>
          </div>

        </div>
    </div>

    </section>

  </div>

</div>
</div>