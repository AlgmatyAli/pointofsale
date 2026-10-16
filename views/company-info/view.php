<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\CompanyInfo $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Company Infos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// اسم الحقل يحوي الحرف (ـ) U+0640 بين criteria و value
$criteriaAttr = "criteria\u{0640}value";

$logo = (string) $model->path;
if ($logo !== '' && !preg_match('~^(https?:)?//|^/~', $logo)) {
    $logo = Yii::getAlias('@web/') . ltrim($logo, '/');
}

$none = '<span class="text-body-secondary">—</span>';

/** صف تفصيل: عنوان + قيمة */
$item = function (string $label, $value, bool $raw = false, bool $ltr = false) use ($none) {
    $empty = ($value === null || $value === '');
    $v = $empty ? $none : ($raw ? $value : Html::encode((string) $value));
    $dir = ($ltr && !$empty) ? ' dir="ltr"' : '';
    return '<div class="col-sm-6 col-lg-4"><div class="text-body-secondary small">' . Html::encode($label) . '</div>'
        . '<div class="fw-semibold text-break"' . $dir . '>' . $v . '</div></div>';
};

// حالة مفتاح: شارة تشغيل/إيقاف
$flag = function (string $attribute) use ($model) {
    $on = (int) $model->$attribute === 1;
    return '<div class="list-group-item d-flex justify-content-between align-items-center">'
        . '<span>' . Html::encode($model->getAttributeLabel($attribute)) . '</span>'
        . '<span class="badge ' . ($on ? 'text-bg-success' : 'text-bg-secondary') . '">' . ($on ? 'مفعّل' : 'غير مفعّل') . '</span></div>';
};
?>

<div class="company-info-view">

    <!-- شريط الإجراءات -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <?= Html::a(
            '<i class="bi bi-pencil-square"></i> ' . Yii::t('app', 'Update'),
            ['update', 'id' => $model->id],
            ['class' => 'btn btn-success']
        ) ?>
        <?= Html::a(
            '<i class="bi bi-arrow-right-circle"></i> ' . Yii::t('app', 'Cancel'),
            ['/company-info/index'],
            ['class' => 'btn btn-outline-secondary']
        ) ?>
        <?= Html::a(
            '<i class="bi bi-trash"></i> ' . Yii::t('app', 'Delete'),
            ['delete', 'id' => $model->id],
            [
                'class' => 'btn btn-outline-danger ms-auto',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]
        ) ?>
    </div>

    <!-- بطاقة الهوية -->
    <div class="card mb-3">
        <div class="card-body d-flex flex-wrap align-items-center gap-3">
            <?php if ($logo !== ''): ?>
                <img src="<?= Html::encode($logo) ?>" alt="<?= Html::encode($model->name) ?>"
                     class="rounded border p-1 bg-white" style="width:96px;height:96px;object-fit:contain">
            <?php else: ?>
                <div class="rounded border d-flex align-items-center justify-content-center text-body-secondary"
                     style="width:96px;height:96px"><i class="bi bi-building fs-1"></i></div>
            <?php endif; ?>
            <div>
                <h3 class="mb-1"><?= Html::encode($model->name) ?></h3>
                <div class="text-body-secondary"><?= Html::encode((string) $model->work) ?></div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">

            <div class="card mb-3">
                <div class="card-header fw-semibold"><i class="bi bi-telephone"></i> بيانات الاتصال</div>
                <div class="card-body">
                    <div class="row g-3">
                        <?= $item($model->getAttributeLabel('phone1'), $model->phone1, false, true) ?>
                        <?= $item($model->getAttributeLabel('phone2'), $model->phone2, false, true) ?>
                        <?= $item($model->getAttributeLabel('phone3'), $model->phone3, false, true) ?>
                        <?= $item($model->getAttributeLabel('fax'), $model->fax, false, true) ?>
                        <?= $item(
                            $model->getAttributeLabel('email'),
                            $model->email ? Html::mailto(Html::encode($model->email), $model->email) : null,
                            true,
                            true
                        ) ?>
                        <div class="col-12">
                            <div class="text-body-secondary small"><?= Html::encode($model->getAttributeLabel('address')) ?></div>
                            <div class="fw-semibold"><?= $model->address ? nl2br(Html::encode($model->address)) : $none ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header fw-semibold"><i class="bi bi-sliders"></i> إعدادات عامة</div>
                <div class="card-body">
                    <div class="row g-3">
                        <?= $item('نسبة  الزيادة', $model->rate) ?>
                        <?= $item('قيمة البحث', $model->$criteriaAttr) ?>
                        <?= $item($model->getAttributeLabel('skin'), $model->skin) ?>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header fw-semibold"><i class="bi bi-file-text"></i> الشروط والأحكام</div>
                <div class="card-body">
                    <?php /* يُعرض كما هو (HTML) كما في الملف الأصلي وكما يظهر في الفواتير */ ?>
                    <?= $model->terms ? $model->terms : $none ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header fw-semibold"><i class="bi bi-toggles"></i> خيارات التشغيل</div>
                <div class="list-group list-group-flush">
                    <?= $flag('searchById') ?>
                    <?= $flag('repeatCategory') ?>
                    <?= $flag('payWayCash') ?>
                    <?= $flag('invoiceState') ?>
                    <?= $flag('waitQnty') ?>
                    <?= $flag('zeroQnty') ?>
                </div>
            </div>
        </div>
    </div>
</div>
