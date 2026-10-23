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

  <table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
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
      foreach ($models as $model):
      ?>
        <tr>
          <td><?= $coun++ ?></td>
          <td><?= Html::encode($model["id"]) ?></td>
          <?php if (($model["post_paid"] ?? 0) == 0): ?>
            <td style="color: red; text-decoration: underline;"><?= Html::encode($model["clientName"]) ?></td>
          <?php else: ?>
            <td><?= Html::encode($model["clientName"]) ?></td>
          <?php endif; ?>
          <td><?= Html::encode($model["billId"]) ?></td>
          <td style="color: green;"><?= number_format((float)($model["total"] - $model["disscount"] - $model["paid"]), 3) ?></td>
          <td style="color: blue;"><?= number_format((float)$model["credt"], 3) ?></td>
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
          <td class="no-print d-print-none">
            <?= Html::a(
              '<i class="bi bi-slash-circle"></i>',
              ['sales/stop-credit', 'id' => $model["client"]],
              [
                'class' => 'btn btn-danger btn-sm',
                'title' => Yii::t('app', 'Stop Credit'),
                'data' => ['method' => 'post', 'confirm' => 'هل تريد إيقاف التعامل بالدين مع هذا العميل؟'],
              ]
            ) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>