<?php

use yii\helpers\Html;
use app\models\CompanyInfo;

/** @var yii\web\View $this */
/** @var array $model */
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
        <h3>تقرير صافي المبيعات من تاريخ : <b><?= \yii\helpers\Html::encode($model['min_date']) ?></b> حتى تاريخ <b><?= \yii\helpers\Html::encode($model['max_date']) ?></b></h3>
        <br>
        <h3>للموظف : <b><?= \yii\helpers\Html::encode($model['userInsert']['username']) ?></b> </h3>
    </div>

    <div class="clearfix"></div>
    <br>

    <table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
        <thead class="kv-table-header w1">
            <tr>
                <th>#</th>
                <th>
                    <h5> بيان الحركة</h5>
                </th>
                <th>
                    <h5> الإجمالي</h5>
                </th>
            </tr>
        </thead>

        <?php
        $coun = 1;
        foreach ($models as $model):
            $count = $count + 1;
            $total += $model["NET"];
        ?>

            <tbody>
                <tr>
                    <td><?= $coun++ ?></td>
                    <td><?= \yii\helpers\Html::encode($model["DESCRIBTION"]) ?></td>
                    <td><?= number_format($model["total"], 3) . "\n"; ?></td>
                </tr>
            </tbody>
        <?php endforeach; ?>
        <tr>
            <td></td>
            <td>الصافي</td>
            <td><?php echo number_format($total, 3) . "\n"; ?></td>
        </tr>
    </table>
</div>



</div>