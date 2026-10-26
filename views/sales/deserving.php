<?php

use yii\helpers\Html;
use app\models\CompanyInfo;

/** @var yii\web\View $this */
/** @var array $models */

// تفعيل تخطيط الطباعة المستقل
$this->context->layout = 'invoice';

$title = CompanyInfo::currentArray();
$logo = (string) ($title['path'] ?? '');
if ($logo !== '' && !preg_match('~^(https?:)?//|^/~', $logo)) {
  $logo = Yii::getAlias('@web/') . ltrim($logo, '/');
}
?>

<!-- شريط الأزرار: لا يُطبع -->
<div class="no-print d-print-none mb-3 d-flex justify-content-between align-items-center">
  <div>
    <button type="button" class="btn btn-primary" onclick="printInvoice('div1')">
      <i class="bi bi-printer"></i> <?= Yii::t('app', 'Print') ?>
    </button>
  </div>
  <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer ?: ['index'], ['class' => 'btn btn-danger']) ?>
</div>

<div id="div1" class="site-about">

  <section id="memo" class="text-center mb-4">
    <div style="float:left">
      <?php if ($logo !== ''): ?>
        <img src="<?= Html::encode($logo) ?>" class="logo rounded-circle" alt="<?= Html::encode($title['name'] ?? '') ?>">
      <?php endif; ?>
    </div>
    <br><br>

    <div class="company-info">
      <span class="company-name"><?= Html::encode($title['name'] ?? '') ?></span>
    </div>
  </section>

  <div class="text-center my-3">
    <h3>قائمة بالفواتير التي قارب موعد استحقاقها</h3>
  </div>

  <div class="clearfix"></div>
  <br>

  <?php
  // تجميع الفواتير حسب الزبون: صف لكل زبون، والضغط عليه يُظهر فواتيره أسفله (مثل sales/index)
  $groups = [];
  foreach ($models as $model) {
    $key = (string) $model['client'];
    if (!isset($groups[$key])) {
      $groups[$key] = ['row' => $model, 'invoices' => [], 'remaining' => 0.0, 'deserving' => $model['deserving']];
    }
    $groups[$key]['invoices'][] = $model;
    $groups[$key]['remaining'] += (float) ($model['total'] - $model['disscount'] - $model['paid']);
    if ((string) $model['deserving'] < (string) $groups[$key]['deserving']) {
      $groups[$key]['deserving'] = $model['deserving'];
    }
  }
  // الأقرب استحقاقاً أولاً
  uasort($groups, fn($a, $b) => strcmp((string) $a['deserving'], (string) $b['deserving']));

  // صفوف الفواتير مخفية في الصفحة فقط؛ الطباعة (printInvoice) لا تحمّل هذا التنسيق فتُطبع كل الفواتير
  $this->registerCss('
    .deserving-invoice.is-hidden { display: none; }
    .deserving-client { cursor: pointer; font-weight: bold; }
    .deserving-client:hover td { background: #e9f2ff; }
    .deserving-invoice td:first-child { border-right: 3px solid #0d6efd; }
  ');
  $this->registerJs('
    $(document).on("click", ".deserving-client", function (e) {
      if ($(e.target).closest("a, button").length) { return; }
      var key = $(this).data("client");
      var rows = $(".deserving-invoice[data-client=\"" + key + "\"]");
      var open = rows.first().hasClass("is-hidden");
      rows.toggleClass("is-hidden", !open);
      $(this).find(".toggle-icon").toggleClass("bi-chevron-left", !open).toggleClass("bi-chevron-down", open);
    });
  ');
  ?>

  <table class="kv-grid-table table table-bordered kv-table-wrap">
    <thead class="kv-table-header w1">
      <tr>
        <th>#</th>
        <th>
          <h5>رقم الحساب</h5>
        </th>
        <th>
          <h5>اسم العميل</h5>
        </th>
        <th>
          <h5>رقم الفاتورة</h5>
        </th>
        <th>
          <h5>قيمة الفاتورة</h5>
        </th>
        <th>
          <h5>اجمالي الدين</h5>
        </th>
        <th>
          <h5>تاريخ الفاتورة</h5>
        </th>
        <th>
          <h5>تاريخ الاستحقاق</h5>
        </th>
        <th class="no-print d-print-none"></th>
        <th class="no-print d-print-none"></th>
        <th class="no-print d-print-none"></th>
        <th class="no-print d-print-none"></th>
      </tr>
    </thead>
    <tbody>
      <?php
      $coun = 1;
      foreach ($groups as $key => $group):
        $first = $group['row'];
        $clientStyle = (($first['post_paid'] ?? 0) == 0) ? 'color: red; text-decoration: underline;' : '';
      ?>
        <tr class="deserving-client table-light" data-client="<?= Html::encode($key) ?>">
          <td><?= $coun++ ?></td>
          <td><?= Html::encode($first['client']) ?></td>
          <td>
            <i class="bi bi-chevron-left toggle-icon no-print"></i>
            <span style="<?= $clientStyle ?>"><?= Html::encode($first['clientName']) ?></span>
          </td>
          <td><?= count($group['invoices']) ?> فاتورة</td>
          <td style="color: green;"><?= number_format($group['remaining'], 3) ?></td>
          <td style="color: blue;"><?= number_format((float) $first['credt'], 3) ?></td>
          <td></td>
          <td><?= Html::encode($group['deserving']) ?></td>
          <td class="no-print d-print-none"></td>
          <td class="no-print d-print-none"></td>
          <td class="no-print d-print-none"></td>
          <td class="no-print d-print-none">
            <?= Html::a(
              '<i class="bi bi-slash-circle"></i>',
              ['sales/stop-credit', 'id' => $first['client']],
              [
                'class' => 'btn btn-danger btn-sm',
                'title' => Yii::t('app', 'Stop Credit'),
                'data' => ['method' => 'post', 'confirm' => 'هل تريد إيقاف التعامل بالدين مع هذا العميل؟'],
              ]
            ) ?>
          </td>
        </tr>
        <?php foreach ($group['invoices'] as $model): ?>
          <tr class="deserving-invoice is-hidden" data-client="<?= Html::encode($key) ?>">
            <td></td>
            <td></td>
            <td></td>
            <td><?= Html::encode($model["billId"]) ?></td>
            <td style="color: green;"><?= number_format((float)($model["total"] - $model["disscount"] - $model["paid"]), 3) ?></td>
            <td></td>
            <td><?= Html::encode($model["at"]) ?></td>
            <td><?= Html::encode($model["deserving"]) ?></td>
            <td class="no-print d-print-none">
              <?= Html::a(
                '<i class="bi bi-folder2-open"></i>',
                ['sales/print', 'id' => $model["id"]],
                ['class' => 'btn btn-success btn-sm', 'title' => Yii::t('app', 'View')]
              ) ?>
            </td>
            <td class="no-print d-print-none">
              <?= Html::a(
                '<i class="bi bi-send"></i>',
                ['sales/done', 'id' => $model["id"]],
                ['class' => 'btn btn-secondary btn-sm', 'title' => Yii::t('app', 'Done')]
              ) ?>
            </td>
            <td class="no-print d-print-none">
              <?= Html::a(
                '<i class="bi bi-whatsapp"></i>',
                ['sales/whatsapp', 'id' => $model["id"], 'reminder' => 1],
                ['class' => 'btn btn-success btn-sm', 'title' => 'تذكير عبر واتساب', 'target' => '_blank', 'rel' => 'noopener']
              ) ?>
            </td>
            <td class="no-print d-print-none"></td>
          </tr>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>