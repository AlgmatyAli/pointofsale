<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\AuthItem $model */
/** @var array $userPermission  [اسم الصلاحية => وصفها] */
/** @var array|null $giving     الصلاحيات الممنوحة حالياً */

// الصلاحيات الممنوحة حالياً (نفس منطق الملف الأصلي)
if ($giving != null) {
    $result = [];
    foreach ($giving as $key => $value) {
        $result[] = $value;
    }
    $model->permission = $result;
}
$total = count($userPermission);
?>

<div class="auth-item-form">
    <?php $form = ActiveForm::begin(['id' => 'auth-item-form']); ?>

    <div class="card mb-3">
        <div class="card-header fw-semibold"><i class="bi bi-shield-lock"></i> بيانات الدور</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autofocus' => true]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header d-flex flex-wrap align-items-center gap-2">
            <span class="fw-semibold me-auto"><i class="bi bi-key"></i> الصلاحيات</span>
            <span class="badge text-bg-primary fs-6" id="perm-counter" data-total="<?= (int) $total ?>">0 / <?= (int) $total ?></span>
        </div>

        <!-- شريط الأدوات: بحث + تحديد الكل -->
        <div class="card-body border-bottom bg-body-tertiary">
            <div class="row g-2 align-items-center">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="search" id="perm-filter" class="form-control" placeholder="ابحث في الصلاحيات ..." autocomplete="off">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-check form-switch mb-0">
                        <?= Html::checkbox(null, false, [
                            'id' => 'perm-check-all',
                            'class' => 'form-check-input check-all',
                            'role' => 'switch',
                        ]) ?>
                        <label class="form-check-label fw-semibold" for="perm-check-all"><?= Yii::t('app', 'Check All') ?></label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <?= $form->field($model, 'permission')->checkboxList($userPermission, [
                'tag' => 'div',
                'class' => 'row g-2',
                'id' => 'perm-list',
                'item' => function ($index, $label, $name, $checked, $value) {
                    $id = 'perm-' . $index;
                    return '<div class="col-12 col-sm-6 col-xl-4 perm-item">'
                        . '<label class="perm-card d-flex align-items-start gap-2 border rounded p-2 h-100' . ($checked ? ' is-checked' : '') . '" for="' . $id . '">'
                        . '<input type="checkbox" class="form-check-input mt-1 flex-shrink-0" id="' . $id . '" name="' . Html::encode($name) . '" value="' . Html::encode($value) . '"' . ($checked ? ' checked' : '') . '>'
                        . '<span>' . Html::encode($label) . '</span></label></div>';
                },
            ])->label(false) ?>
            <div id="perm-empty" class="text-center text-body-secondary py-4 d-none">لا توجد صلاحيات مطابقة للبحث</div>
        </div>
    </div>

    <!-- شريط الحفظ: يبقى ظاهراً أسفل الشاشة على الهاتف -->
    <div class="position-sticky bottom-0 bg-body border-top py-2 d-flex gap-2" style="z-index:5">
        <?= Html::submitButton(
            '<i class="bi bi-check-lg"></i> ' . Yii::t('app', 'Save'),
            ['class' => 'btn btn-success flex-fill flex-md-grow-0 px-md-5']
        ) ?>
        <?= Html::a(
            '<i class="bi bi-x-lg"></i> ' . Yii::t('app', 'Cancel'),
            ['/auth-item/index'],
            ['class' => 'btn btn-outline-secondary flex-fill flex-md-grow-0']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerCss(<<<'CSS'
.perm-card { cursor: pointer; transition: background-color .12s, border-color .12s; }
.perm-card:hover { background-color: var(--bs-tertiary-bg); }
.perm-card.is-checked {
    background-color: var(--bs-primary-bg-subtle);
    border-color: var(--bs-primary-border-subtle) !important;
    color: var(--bs-primary-text-emphasis);
}
CSS);

$this->registerJs(<<<'JS'
(function () {
    var $list = $('#perm-list'),
        $counter = $('#perm-counter'),
        $all = $('#perm-check-all'),
        $empty = $('#perm-empty');

    function boxes() { return $list.find('input[type=checkbox]'); }

    function refresh() {
        var $b = boxes(), n = $b.filter(':checked').length;
        $b.each(function () { $(this).closest('.perm-card').toggleClass('is-checked', this.checked); });
        $counter.text(n + ' / ' + $counter.data('total'));
        $all.prop('checked', $b.length > 0 && n === $b.length);
    }

    // تحديد/إلغاء الكل (يطال الصلاحيات الظاهرة فقط عند وجود بحث)
    $all.on('change', function () {
        var on = this.checked;
        $list.find('.perm-item:not(.d-none) input[type=checkbox]').prop('checked', on);
        refresh();
    });

    $list.on('change', 'input[type=checkbox]', refresh);

    // بحث فوري
    $('#perm-filter').on('input', function () {
        var q = $.trim(this.value).toLowerCase(), shown = 0;
        $list.find('.perm-item').each(function () {
            var ok = q === '' || $(this).text().toLowerCase().indexOf(q) !== -1;
            $(this).toggleClass('d-none', !ok);
            if (ok) { shown++; }
        });
        $empty.toggleClass('d-none', shown > 0);
    });

    refresh();
})();
JS);
?>
