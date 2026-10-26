<?php

use app\models\CompanyInfo;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $models */
/** @var array $lastBalance */
/** @var string $min_date */
/** @var string $max_date */

// تقرير بتخطيط مستقل (يعرض الشريط الجانبي والعلوي، ولا يدخلان في الطباعة)، ويُطبع داخل إطار معزول (printReport في layouts/report.php)
$this->context->layout = 'report';
$this->title = $this->title ?: 'كشف حساب عميل';

// بيانات الشركة والشعار
$company = CompanyInfo::currentArray() ?: [];
$logo = (string) ($company['path'] ?? '');
if ($logo !== '' && !preg_match('~^(https?:)?//|^/~', $logo)) {
  $logo = Yii::getAlias('@web/') . ltrim($logo, '/');
}

// اسم العميل ورقم التعريف
$name = '';
$id = '';
if (!empty($models)) {
  $firstModel = reset($models);
  $name = $firstModel['name'] ?? '';
  $id = $firstModel['id'] ?? '';
}

// حساب الرصيد السابق
$balance = 0;
$sumwaredLast = 0;
$sumsaderLast = 0;

if (!empty($lastBalance)) {
  foreach ($lastBalance as $lb) {
    $sumwaredLast += (float) ($lb['wared'] ?? 0);
    $sumsaderLast += (float) ($lb['sader'] ?? 0);
    if (isset($lb['type'])) {
      if ($lb['type'] == 0 || $lb['type'] == 2) {
        $balance = $sumsaderLast - $sumwaredLast;
      } elseif ($lb['type'] == 1) {
        $balance = $sumsaderLast - $sumwaredLast;
      }
    }
  }
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
      <h1 class="report-title">كشف حساب عميل</h1>
      <div class="report-period">
        من الفترة: <b><?= Html::encode((string) $min_date) ?></b>
        حتى تاريخ: <b><?= Html::encode((string) $max_date) ?></b>
      </div>
    </div>

    <div class="report-meta">
      تاريخ الطباعة: <?= date('Y-m-d H:i') ?><br>
      أعدّه: <?= Html::encode((string) Yii::$app->user->identity?->username) ?>
    </div>
  </header>

  <?php if (!empty($name) || !empty($id)): ?>
    <section class="report-item">
      <span class="rp-label">اسـم العميل:</span>
      <b><?= Html::encode((string) $id) ?></b> - <b><?= Html::encode((string) $name) ?></b>
    </section>
  <?php endif; ?>

  <table class="report-table">
    <thead>
      <tr>
        <th class="rp-idx">#</th>
        <th>تاريخ الحركة</th>
        <th>البيــــــان</th>
        <th class="rp-cr">دائــــــــن</th>
        <th class="rp-dr">مديــــــــن</th>
        <th class="rp-bal">الرصيــــــد</th>
        <th class="rp-actions no-print"></th>
      </tr>
    </thead>

    <tbody>
      <!-- الرصيد السابق -->
      <tr class="rp-open">
        <td colspan="5">رصيد سابق</td>
        <td class="rp-num rp-bal"><?= $fmt($balance) ?></td>
        <td class="rp-actions no-print"></td>
      </tr>

      <?php
      $coun = 1;
      $sumwared = 0;
      $sumsader = 0;
      $deleviried = 0;
      $lastModelType = 0;

      if (!empty($models)):
        foreach ($models as $model):
          $waredVal = (float) ($model['wared'] ?? 0);
          $saderVal = (float) ($model['sader'] ?? 0);
          $sumwared += $waredVal;
          $sumsader += $saderVal;
          $lastModelType = $model['type'] ?? 0;

          if (($model['type'] ?? 0) != 1) {
            if (($model['deleviried'] ?? 0) == 0) {
              $deleviried += $saderVal;
            }
          }

          // حساب الرصيد التراكمي
          if (($model['type'] ?? 0) == 0 || ($model['type'] ?? 0) == 2) {
            $currentBalance = ($sumsader - $sumwared) + $balance;
          } elseif (($model['type'] ?? 0) == 1) {
            $currentBalance = ($sumwared - $sumsader) + $balance;
          } else {
            $currentBalance = 0;
          }
      ?>
          <tr>
            <td class="rp-idx"><?= $coun++ ?></td>
            <td><?= Html::encode((string) ($model['trandate'] ?? '')) ?></td>
            <td>
              <?= Html::encode(($model['kind'] ?? '') . ' ' . ($model['billId'] ?? '')) ?>
              <?php if (($model['type'] ?? 0) != 1 && ($model['deleviried'] ?? 0) == 0): ?>
                <span class="rp-undelivered">غير مسلّمة</span>
              <?php endif; ?>
            </td>
            <td class="rp-num rp-cr<?= $waredVal == 0 ? ' rp-zero' : '' ?>"><?= $fmt($waredVal) ?></td>
            <td class="rp-num rp-dr<?= $saderVal == 0 ? ' rp-zero' : '' ?>"><?= $fmt($saderVal) ?></td>
            <td class="rp-num rp-bal"><?= $fmt($currentBalance) ?></td>
            <td class="rp-actions no-print">
              <?php
              $kind = $model['kind'] ?? '';
              $printId = $model['printId'] ?? null;

              if (in_array($kind, ['مشتريات قاتورة رقم - ', 'ترجيع مشتريات فاتورة رقـم - ', 'فاتورة مشتريات رقـم - '])) {
                echo Html::a('<i class="bi bi-folder2-open"></i>', ['purchases/view', 'id' => $printId], [
                  'title' => Yii::t('yii', 'View'),
                  'class' => 'btn btn-sm btn-success'
                ]);
              } elseif (in_array($kind, ['مبيعات فاتورة رقم - ', 'ترجيع مبيعات فاتورة رقم - '])) {
                echo Html::a('<i class="bi bi-folder2-open"></i>', ['sales/print', 'id' => $printId], [
                  'title' => Yii::t('yii', 'View'),
                  'class' => 'btn btn-sm btn-primary'
                ]);
              } elseif ($kind == 'تحصيل ايصال رقـم - ') {
                echo Html::a('<i class="bi bi-folder2-open"></i>', ['receipt/view', 'id' => $printId], [
                  'title' => Yii::t('yii', 'View'),
                  'class' => 'btn btn-sm btn-danger'
                ]);
              } elseif ($kind == 'ايصال صرف رقـم - ') {
                echo Html::a('<i class="bi bi-folder2-open"></i>', ['receipt/view', 'id' => $printId], [
                  'title' => Yii::t('yii', 'View'),
                  'class' => 'btn btn-sm btn-warning'
                ]);
              }
              ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="7" class="text-center text-muted">
            <b>لا توجد حركات مسجلة خلال الفترة المحددة.</b>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>

    <tfoot>
      <tr>
        <td colspan="3">
          الإجمالي (رصيد قبل: <?= Html::encode((string) $min_date) ?>: <?= $fmt($balance) ?>)
        </td>
        <td class="rp-num rp-cr"><?= $fmt($sumwared) ?></td>
        <td class="rp-num rp-dr"><?= $fmt($sumsader) ?></td>
        <td class="rp-num rp-bal">
          الرصيـد :
          <?php
          if ($lastModelType == 1) {
            echo $fmt(($sumwared - $sumsader) + $balance);
          } else {
            echo $fmt(($sumsader - $sumwared) + $balance);
          }
          ?>
        </td>
        <td class="rp-actions no-print"></td>
      </tr>
    </tfoot>
  </table>

  <!-- ملخص التقرير -->
  <section class="report-summary">
    <div class="report-stat">
      <div class="rp-k">إجمالي الدائن</div>
      <div class="rp-v"><?= $fmt($sumwared) ?></div>
    </div>
    <div class="report-stat">
      <div class="rp-k">إجمالي المدين</div>
      <div class="rp-v"><?= $fmt($sumsader) ?></div>
    </div>
    <div class="report-stat">
      <div class="rp-k">إجمالي الغير مستلمة</div>
      <div class="rp-v"><?= $fmt($deleviried) ?></div>
    </div>
  </section>

</div>