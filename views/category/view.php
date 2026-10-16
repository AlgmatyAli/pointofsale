<?php

use barcode\barcode\BarcodeGenerator as BarcodeGenerator;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Category */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$seeCost = (Yii::$app->user->identity->seeCostPrice == 1);
$fmt = fn($v) => ($v === null || $v === '') ? null : Yii::$app->formatter->asDecimal((float) $v, 3);

// نفس منطق الكود القديم: status 1 = موقوف، 0 = نشط
$stopped = ((int) $model->status === 1);
$accent = $stopped ? 'secondary' : 'success';

$none = '<span class="text-body-secondary">—</span>';
$row = function (string $label, $value, bool $raw = false) use ($none) {
    $v = ($value === null || $value === '') ? $none : ($raw ? $value : Html::encode((string) $value));
    return '<div class="d-flex justify-content-between align-items-center py-2 border-bottom gap-3">'
        . '<span class="text-body-secondary">' . Html::encode($label) . '</span>'
        . '<span class="fw-semibold text-end">' . $v . '</span></div>';
};
$yesNo = fn($v) => ((int) $v === 1)
    ? '<span class="badge text-bg-success">نعم</span>'
    : '<span class="badge text-bg-secondary">لا</span>';

$lowStock = ($model->quantity !== null && $model->minimum !== null && (float) $model->quantity < (float) $model->minimum);

// بطاقات الأرقام: [العنوان، القيمة، اللون]
$stats = [
    [Yii::t('app', 'Quantity'), $fmt($model->quantity), $lowStock ? 'danger' : 'primary'],
    [Yii::t('app', 'Minimum'), $fmt($model->minimum), 'secondary'],
    [Yii::t('app', 'Price'), $fmt($model->price), 'success'],
];
if ($seeCost) {
    // سعر التكلفة لا يظهر إلا لمن يملك صلاحية seeCostPrice
    $stats[] = [Yii::t('app', 'Cost'), $fmt($model->cost), 'warning'];
}
?>
<div class="category-view">

    <!-- شريط الإجراءات -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        <?= Html::a('<i class="bi bi-pencil-square"></i> ' . Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(
            '<i class="bi bi-arrow-repeat"></i> ' . Yii::t('app', 'Change Status'),
            ['state', 'id' => $model->id],
            ['class' => 'btn btn-' . ($stopped ? 'success' : 'warning'), 'data-confirm' => Yii::t('app', 'Change Status') . '?']
        ) ?>
        <?= Html::a('<i class="bi bi-arrow-right"></i> ' . Yii::t('app', 'Cancel'), Yii::$app->request->referrer ?: ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php if ($stopped): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2 py-2" role="alert">
            <i class="bi bi-info-circle-fill"></i>
            <div><strong>Note:</strong> <?= Html::encode(Yii::t('app', 'this item is unActive?')) ?></div>
        </div>
    <?php endif; ?>

    <!-- بطاقة الصنف -->
    <div class="card border-<?= $accent ?> mb-3">
        <div class="card-body d-flex flex-wrap align-items-center gap-3">
            <?php if (!empty($model->path)): ?>
                <img src="<?= Html::encode($model->path) ?>" alt="" class="rounded border" style="width:64px;height:64px;object-fit:cover">
            <?php else: ?>
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-<?= $accent ?>-subtle text-<?= $accent ?>"
                      style="width:64px;height:64px"><i class="bi bi-box-seam fs-3"></i></span>
            <?php endif; ?>
            <div class="flex-grow-1">
                <div class="fs-5 fw-bold"><?= Html::encode((string) $model->name) ?></div>
                <div class="small text-body-secondary">
                    <span class="badge text-bg-<?= $accent ?>"><?= $stopped ? 'موقوف' : 'نشط' ?></span>
                    <?php if ($lowStock): ?><span class="badge text-bg-danger"><i class="bi bi-exclamation-triangle"></i> أقل من الحد الأدنى</span><?php endif; ?>
                    <?php if (!empty($model->serialNo)): ?><span class="ms-1"><?= Html::encode((string) $model->serialNo) ?></span><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- الأرقام الأساسية -->
    <div class="row g-2 mb-3">
        <?php foreach ($stats as [$label, $value, $color]): ?>
            <div class="col-6 col-md">
                <div class="rounded-3 px-3 py-2 bg-<?= $color ?>-subtle text-<?= $color ?>-emphasis h-100">
                    <div class="small opacity-75"><?= Html::encode((string) $label) ?></div>
                    <div class="fs-5 fw-bold" dir="ltr"><?= $value === null ? '—' : Html::encode($value) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header fw-bold"><i class="bi bi-card-list"></i> تفاصيل الصنف</div>
                <div class="card-body pt-2">
                    <?= $row($model->getAttributeLabel('name'), $model->name) ?>
                    <?= $row($model->getAttributeLabel('class'), $model->class) ?>
                    <?= $row($model->getAttributeLabel('unit'), $model->unit) ?>
                    <?= $row($model->getAttributeLabel('box'), $model->box) ?>
                    <?= $row($model->getAttributeLabel('commCode'), $model->commCode) ?>
                    <?= $row(Yii::t('app', 'Q Show'), $yesNo($model->qShow), true) ?>
                    <?= $row(Yii::t('app', 'Ending'), $yesNo($model->ending), true) ?>
                    <?= $row(Yii::t('app', 'Status'), '<span class="badge text-bg-' . $accent . '">' . ($stopped ? 'موقوف' : 'نشط') . '</span>', true) ?>
                    <?= $row(Yii::t('app', 'User Insert'), $model->userInsert?->username) ?>
                    <?= $row($model->getAttributeLabel('created_at'), $model->created_at) ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header fw-bold"><i class="bi bi-image"></i> الصورة</div>
                <div class="card-body text-center">
                    <?php if (!empty($model->path)): ?>
                        <img src="<?= Html::encode($model->path) ?>" alt="<?= Html::encode((string) $model->name) ?>" class="img-fluid rounded img-prev" style="max-height:260px">
                    <?php else: ?>
                        <div class="text-body-secondary py-4"><i class="bi bi-image fs-1 d-block"></i>لا توجد صورة</div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($model->serialNo)): ?>
                <div class="card">
                    <div class="card-header fw-bold"><i class="bi bi-upc-scan"></i> الباركود</div>
                    <!-- حاوية واحدة فقط: كان الكود القديم يكرر المعرّف showBarcode مرتين فيظهر الباركود في الحاوية الأولى الفارغة -->
                    <div class="card-body text-center overflow-auto">
                        <div id="showBarcode">
                            <?= BarcodeGenerator::widget([
                                'elementId' => 'showBarcode',
                                'value' => $model->serialNo,
                                'type' => 'code39', // الأنواع المدعومة: ean8, ean13, upc, std25, int25, code11, code39, code93, code128, codabar, msi, datamatrix
                            ]) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
