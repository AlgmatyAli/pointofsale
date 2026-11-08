<?php

use app\models\CrmLevel;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\CrmTag[] $tags */
/** @var array $counts */
/** @var app\models\CrmTag $model */

$this->title = 'مجموعات الزبائن';
$this->params['breadcrumbs'][] = 'متابعة الزبائن';
$this->params['breadcrumbs'][] = $this->title;
$u = Yii::$app->user;
$csrf = Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken);
?>
<div class="crm-groups" style="max-width: 900px">
    <?= $this->render('_nav') ?>
    <p class="text-body-secondary">المجموعة طريقة لتقسيم الزبائن، مثل «زبائن الجملة» أو «زبائن أحمد». يمكن تعيين مسؤول لكل أعضاء المجموعة دفعة واحدة، وتصفية لوحة التحصيل بها.</p>

    <?php if ($u->can('crmGroupEdit')): ?>
    <form method="post" action="<?= Url::to(['group-save']) ?>" class="d-flex flex-wrap gap-2 align-items-end mb-3">
        <?= $csrf ?>
        <div>
            <label class="form-label small mb-1" for="g-name">اسم المجموعة</label>
            <input type="text" name="CrmTag[name]" id="g-name" class="form-control" maxlength="100" required>
        </div>
        <div>
            <label class="form-label small mb-1" for="g-color">اللون</label>
            <?= Html::dropDownList('CrmTag[color]', 'primary', CrmLevel::COLORS, ['id' => 'g-color', 'class' => 'form-select']) ?>
        </div>
        <button class="btn btn-primary"><i class="bi bi-plus-lg"></i> إنشاء المجموعة</button>
    </form>
    <?php endif; ?>

    <?php if (!$tags): ?>
        <div class="text-center text-body-secondary py-5 border rounded">لا توجد مجموعات بعد.</div>
    <?php else: ?>
    <div class="table-responsive">
    <table class="table align-middle">
        <thead><tr><th>المجموعة</th><th>عدد الزبائن</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($tags as $t): ?>
            <tr>
                <td>
                    <?php if ($u->can('crmGroupEdit')): ?>
                        <form method="post" action="<?= Url::to(['group-save', 'id' => $t->id]) ?>" class="d-flex gap-2">
                            <?= $csrf ?>
                            <input type="text" name="CrmTag[name]" value="<?= Html::encode($t->name) ?>" class="form-control form-control-sm" maxlength="100" required aria-label="اسم المجموعة">
                            <?= Html::dropDownList('CrmTag[color]', $t->color, CrmLevel::COLORS, ['class' => 'form-select form-select-sm', 'aria-label' => 'اللون']) ?>
                            <button class="btn btn-sm btn-outline-secondary">حفظ</button>
                        </form>
                    <?php else: ?>
                        <span class="badge text-bg-<?= Html::encode($t->color) ?>"><?= Html::encode($t->name) ?></span>
                    <?php endif; ?>
                </td>
                <td><?= (int) ($counts[$t->id] ?? 0) ?></td>
                <td class="text-end text-nowrap">
                    <?= Html::a('الأعضاء', ['group', 'id' => $t->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                    <?php if ($u->can('crmGroupEdit')): ?>
                        <?= Html::a('حذف', ['group-delete', 'id' => $t->id], ['class' => 'btn btn-sm btn-outline-danger',
                            'data' => ['method' => 'post', 'confirm' => 'حذف مجموعة «' . $t->name . '»؟ الزبائن لن يُحذفوا.']]) ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>
