<?php

use app\models\CompanyInfo;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $models */

// تقرير بتخطيط مستقل بلا الشريط الجانبي والعلوي، ويُطبع داخل إطار معزول (printReport في layouts/report.php)
$this->context->layout = 'report';
$this->title = $this->title ?: 'تقرير إجمالي ديون العملاء';

// بيانات الشركة والشعار
$company = CompanyInfo::currentArray() ?: [];
$logo = (string) ($company['path'] ?? '');
if ($logo !== '' && !preg_match('~^(https?:)?//|^/~', $logo)) {
  $logo = Yii::getAlias('@web/') . ltrim($logo, '/');
}

$fmt = static fn($value): string => number_format((float) $value, 3);
?>

<!-- شريط الأزرار: لا يُطبع -->
<div class="no-print d-print-none mb-3 d-flex flex-wrap justify-content-between gap-2">
  <button type="button" class="btn btn-primary" onclick="printReport()">
    <i class="bi bi-printer"></i> <?= Yii::t('app', 'Print') ?>
  </button>

  <?= Html::a(
    Yii::t('app', 'Cancel'),
    Yii::$app->request->referrer ?: ['index'],
    ['class' => 'btn btn-danger']
  ) ?>
</div>

<div id="report" class="report">

  <header class="report-header">
    <div class="report-brand">
      <?php if ($logo !== ''): ?>
        <img src="<?= Html::encode($logo) ?>" class="report-logo" alt="<?= Html::encode($company['name'] ?? '') ?>">
      <?php endif; ?>
      <div class="report-company"><?= Html::encode($company['name'] ?? '') ?></div>
    </div>

    <div class="report-heading">
      <h1 class="report-title">تقرير إجمالي ديون العملاء</h1>
      <div class="report-period">
        حتى تاريخ: <b><?= date('Y/m/d') ?></b>
      </div>
    </div>

    <div class="report-meta">
      تاريخ الطباعة: <?= date('Y-m-d H:i') ?><br>
      أعدّه: <?= Html::encode((string) Yii::$app->user->identity?->username) ?>
    </div>
  </header>

  <table class="report-table">
    <thead>
      <tr>
        <th class="rp-idx">#</th>
        <th>رقم الحساب</th>
        <th>اسم العميل</th>
        <th>رقم الهاتف</th>
        <th class="rp-bal">الرصيـد</th>
        <th class="rp-actions no-print"></th>
      </tr>
    </thead>

    <tbody>
      <?php
      $coun = 1;
      $credt = 0; // إجمالي المدين (الأرصدة بالسالب)
      $dept = 0;  // إجمالي الدائن (الأرصدة بالموجب)

      if (!empty($models)):
        foreach ($models as $model):
          $val = (float) ($model['credt'] ?? 0);
          if ($val < 0) {
            $credt += $val;
          } else {
            $dept += $val;
          }
      ?>
          <tr>
            <td class="rp-idx"><?= $coun++ ?></td>
            <td><?= Html::encode((string) ($model['id'] ?? '')) ?></td>
            <td>
              <?php if (($model['post_paid'] ?? 1) == 0): ?>
                <span class="text-danger text-decoration-underline">
                  <?= Html::encode((string) ($model['name'] ?? '')) ?>
                </span>
              <?php else: ?>
                <?= Html::encode((string) ($model['name'] ?? '')) ?>
              <?php endif; ?>

              <?php if (($model['type'] ?? 0) != 1 && !empty($model['deserving'])): ?>
                <span class="rp-undelivered ms-1">مستحق</span>
              <?php endif; ?>
            </td>
            <td><?= Html::encode((string) ($model['phone'] ?? '')) ?></td>
            <td class="rp-num rp-bal"><?= $fmt($val) ?></td>
            <td class="rp-actions no-print">
              <?= Html::a(
                '<i class="bi bi-folder2-open"></i>',
                [
                  'sales/histrans',
                  'client' => $model['id'] ?? '',
                  'allData' => 1,
                  'type' => $model['type'] ?? ''
                ],
                [
                  'title' => Yii::t('yii', 'View'),
                  'class' => 'btn btn-sm btn-success'
                ]
              ) ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" class="text-center text-muted">
            <b>لا توجد بيانات مسجلة.</b>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>

    <tfoot>
      <tr>
        <td colspan="4">الإجمالي</td>
        <td class="rp-num rp-bal">
          <div>إجمالي المدين: <b><?= $fmt($credt) ?></b></div>
          <div>إجمالي الدائن: <b><?= $fmt($dept) ?></b></div>
        </td>
        <td class="rp-actions no-print"></td>
      </tr>
    </tfoot>
  </table>

  <!-- ملخص التقرير -->
  <section class="report-summary">
    <div class="report-stat">
      <div class="rp-k">إجمالي المدين</div>
      <div class="rp-v text-danger"><?= $fmt($credt) ?></div>
    </div>
    <div class="report-stat">
      <div class="rp-k">إجمالي الدائن</div>
      <div class="rp-v text-success"><?= $fmt($dept) ?></div>
    </div>
    <div class="report-stat">
      <div class="rp-k">صافي الديون</div>
      <div class="rp-v"><?= $fmt($dept + $credt) ?></div>
    </div>
  </section>

</div>