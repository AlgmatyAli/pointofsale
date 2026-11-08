<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var int $clientId */
/** @var float $balance */
$uid = 'p' . $clientId;
?>
<form method="post" action="<?= Url::to(['promise-create']) ?>" class="row g-2 mt-1">
    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
    <?= Html::hiddenInput('CrmPromise[clientId]', $clientId) ?>
    <div class="col-md-4">
        <label class="form-label small" for="<?= $uid ?>-amount">المبلغ</label>
        <input type="number" step="0.001" min="0.001" name="CrmPromise[amount]" id="<?= $uid ?>-amount" class="form-control" required
               value="<?= $balance > 0 ? Html::encode(round($balance, 3)) : '' ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label small" for="<?= $uid ?>-date">تاريخ الدفع</label>
        <input type="date" name="CrmPromise[promiseDate]" id="<?= $uid ?>-date" class="form-control" required min="<?= date('Y-m-d') ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label small" for="<?= $uid ?>-note">ملاحظة</label>
        <input type="text" name="CrmPromise[note]" id="<?= $uid ?>-note" class="form-control" maxlength="255">
    </div>
    <div class="col-12">
        <button class="btn btn-outline-primary"><i class="bi bi-hand-thumbs-up"></i> تسجيل الوعد</button>
        <small class="text-body-secondary ms-2">تُغلق المهام المفتوحة، ويُفحص الوعد تلقائياً عند موعده.</small>
    </div>
</form>
