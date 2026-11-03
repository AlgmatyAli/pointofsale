<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var bool $dirFound */
/** @var array $files */
/** @var string|null $lastDeploy */
/** @var string|null $deployFailed */
/** @var string|null $backupFailed */

$this->title = 'النسخ الاحتياطية لقاعدة البيانات';
$this->params['breadcrumbs'][] = $this->title;

$size = function (int $bytes): string {
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    $n = (float) $bytes;
    while ($n >= 1024 && $i < count($units) - 1) {
        $n /= 1024;
        $i++;
    }
    return number_format($n, $i ? 1 : 0) . ' ' . $units[$i];
};
?>
<div class="backup-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-body-secondary">
        نسخة واحدة باسم <code>backup_DB</code> تُحدَّث تلقائياً كل ساعة،
        إضافة إلى نسخة قبل كل تحديث فيه تعديل على قاعدة البيانات.
    </p>

    <?php if ($backupFailed !== null): ?>
        <div class="alert alert-danger">
            <strong>فشلت آخر محاولة للنسخ الاحتياطي:</strong><br>
            <?= nl2br(Html::encode($backupFailed)) ?>
            <div class="small mt-1">الملف المعروض أدناه هو آخر نسخة ناجحة.</div>
        </div>
    <?php endif; ?>

    <?php if ($deployFailed !== null): ?>
        <div class="alert alert-danger">
            <strong>التحديث التلقائي متوقف على هذا السيرفر:</strong><br>
            <?= nl2br(Html::encode($deployFailed)) ?>
        </div>
    <?php endif; ?>

    <?php if (!$dirFound || !$files): ?>
        <div class="alert alert-warning">
            لا توجد نسخ احتياطية بعد. تظهر هنا بعد أول تشغيل لمهمة النسخ الاحتياطي (Cron) على السيرفر.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle bg-white">
                <thead class="table-light">
                    <tr>
                        <th>النسخة</th>
                        <th>التاريخ والوقت</th>
                        <th>الحجم</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($files as $f): ?>
                        <tr<?= $f['daily'] ? ' class="table-success"' : '' ?>>
                            <td>
                                <?php if ($f['daily']): ?>
                                    <strong>آخر نسخة</strong> <span class="text-body-secondary">(backup_DB، تُحدَّث كل ساعة)</span>
                                <?php else: ?>
                                    قبل تحديث قاعدة البيانات
                                    <div class="small text-body-secondary"><bdi><?= Html::encode($f['name']) ?></bdi></div>
                                <?php endif; ?>
                            </td>
                            <td><bdi><?= date('Y-m-d H:i', $f['time']) ?></bdi></td>
                            <td><bdi><?= $size($f['size']) ?></bdi></td>
                            <td class="text-center">
                                <?= Html::a('<i class="bi bi-download"></i> تحميل', ['download', 'name' => $f['name']], [
                                    'class' => 'btn btn-sm btn-primary',
                                    'data-pjax' => 0,
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <p class="small text-body-secondary mb-0">
        آخر تحديث ناجح للبرنامج على هذا السيرفر: <bdi><?= Html::encode($lastDeploy ?? 'غير معروف') ?></bdi>.
        للاسترجاع: phpMyAdmin ← Import واختر الملف كما هو (.sql.gz).
    </p>
</div>
