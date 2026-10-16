<?php

use app\models\AuthItem;
use app\models\Client;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->username;

$none = '<span class="text-body-secondary">—</span>';
$row = function (string $label, $value, bool $raw = false) use ($none) {
    $v = ($value === null || $value === '') ? $none : ($raw ? $value : Html::encode((string) $value));
    return '<div class="d-flex justify-content-between align-items-center py-2 border-bottom gap-3">'
        . '<span class="text-body-secondary">' . Html::encode($label) . '</span>'
        . '<span class="fw-semibold text-end">' . $v . '</span></div>';
};

$active = ($model->isActive === 'active');
$accent = $active ? 'success' : 'secondary';
$statusBadge = '<span class="badge text-bg-' . $accent . '">' . ($active ? 'نشط' : 'معطّل') . '</span>';
$initial = mb_substr(trim((string) $model->username), 0, 1);

// نوع المستخدم: القيمة المخزّنة هي type من جدول AuthItem
$typeName = null;
if ($model->permission !== null && $model->permission !== '') {
    $typeName = AuthItem::find()->where(['type' => $model->permission])->one()?->name;
}

// العملاء المرتبطون (قد تكون القيمة نصاً مفصولاً بفواصل أو مصفوفة)
$clientIds = is_array($model->client) ? $model->client : array_filter(explode(',', (string) $model->client));
$clientNames = $clientIds ? Client::find()->select('name')->where(['in', 'id', $clientIds])->column() : [];

// الصلاحيات الإضافية
$flags = [
    'seeCostPrice' => 'رؤية سعر التكلفة وهامش الربح',
    'editSalePrice' => 'تعديل سعر البيع في الفاتورة',
    'makeDiscount' => 'منح خصم على الفاتورة',
    'printPurtchaseInvoice' => 'طباعة فواتير المشتريات',
    'seeOtherBranchQ' => 'رؤية كميات الفروع الأخرى',
];
?>
<div class="user-view">

    <div class="d-flex flex-wrap gap-2 mb-3">
        <?= Html::a('<i class="bi bi-pencil-square"></i> ' . Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="bi bi-arrow-right"></i> ' . Yii::t('app', 'Back'), Yii::$app->request->referrer ?: ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <!-- بطاقة المستخدم -->
    <div class="card border-<?= $accent ?> mb-3">
        <div class="card-body d-flex flex-wrap align-items-center gap-3">
            <?php if (!empty($model->path)): ?>
                <img src="<?= Html::encode($model->path) ?>" alt="" class="rounded-circle border" style="width:64px;height:64px;object-fit:cover">
            <?php else: ?>
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-<?= $accent ?>-subtle text-<?= $accent ?> fw-bold fs-3"
                      style="width:64px;height:64px"><?= $initial !== '' ? Html::encode($initial) : '<i class="bi bi-person"></i>' ?></span>
            <?php endif; ?>
            <div class="flex-grow-1">
                <div class="fs-5 fw-bold"><?= Html::encode((string) $model->username) ?></div>
                <div class="small text-body-secondary">
                    <?= $statusBadge ?>
                    <?php if ($typeName): ?><span class="badge text-bg-primary"><?= Html::encode($typeName) ?></span><?php endif; ?>
                    <span class="ms-1">#<?= Html::encode((string) $model->id) ?></span>
                </div>
            </div>
            <div class="small text-body-secondary text-end">
                <?php if (!empty($model->phone)): ?><div><i class="bi bi-telephone"></i> <span dir="ltr"><?= Html::encode((string) $model->phone) ?></span></div><?php endif; ?>
                <?php if (!empty($model->email)): ?><div><i class="bi bi-envelope"></i> <?= Html::encode((string) $model->email) ?></div><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-bold"><i class="bi bi-person-lines-fill"></i> بيانات الحساب</div>
                <div class="card-body pt-2">
                    <?= $row($model->getAttributeLabel('id'), $model->id) ?>
                    <?= $row($model->getAttributeLabel('username'), $model->username) ?>
                    <?= $row($model->getAttributeLabel('phone'), $model->phone) ?>
                    <?= $row($model->getAttributeLabel('email'), $model->email) ?>
                    <?= $row($model->getAttributeLabel('createedDate'), $model->createedDate) ?>
                    <?= $row($model->getAttributeLabel('isActive'), $statusBadge, true) ?>
                    <?= $row($model->getAttributeLabel('permission'), $typeName) ?>
                    <div class="pt-2">
                        <div class="text-body-secondary mb-1"><?= Html::encode($model->getAttributeLabel('client')) ?></div>
                        <div>
                            <?php if ($clientNames): ?>
                                <?php foreach ($clientNames as $n): ?><span class="badge text-bg-light border fw-normal me-1"><?= Html::encode((string) $n) ?></span><?php endforeach; ?>
                            <?php else: ?><?= $none ?><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100 border-primary">
                <div class="card-header fw-bold bg-primary-subtle"><i class="bi bi-shield-check"></i> الصلاحيات الإضافية</div>
                <div class="card-body pt-2">
                    <?php foreach ($flags as $attr => $hint):
                        $on = ((int) $model->$attr === 1); ?>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom gap-3">
                            <span><?= Html::encode($hint) ?></span>
                            <?= $on
                                ? '<span class="badge text-bg-success"><i class="bi bi-check-lg"></i> مفعّل</span>'
                                : '<span class="badge text-bg-secondary"><i class="bi bi-x-lg"></i> غير مفعّل</span>' ?>
                        </div>
                    <?php endforeach; ?>
                    <div class="pt-2">
                        <?= $row($model->getAttributeLabel('maxDiscount'), $model->maxDiscount) ?>
                        <?= $row($model->getAttributeLabel('maxExpenses'), $model->maxExpenses) ?>
                        <?= $row($model->getAttributeLabel('maxReceipt'), $model->maxReceipt) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
