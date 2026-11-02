<?php

use kartik\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var int $all */

$this->title = 'تعديلات الفواتير بعد الحفظ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-audit-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-body-secondary">كل تغيير في طريقة الدفع أو الزبون أو الإجمالي أو الخصم أو المدفوع بعد حفظ الفاتورة.</p>

    <div class="d-flex flex-wrap gap-2 mb-3">
        <?php if (!$all): ?>
            <?= Html::a('<i class="bi bi-check2-all"></i> تمت مراجعة الكل', ['review-all'], [
                'class' => 'btn btn-success',
                'data' => ['method' => 'post', 'confirm' => 'تأكيد مراجعة كل التعديلات المعروضة؟'],
            ]) ?>
            <?= Html::a('عرض السجل كاملاً', ['index', 'all' => 1], ['class' => 'btn btn-outline-secondary']) ?>
        <?php else: ?>
            <?= Html::a('غير المراجَع فقط', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        <?php endif; ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($row) {
            // التحويل إلى آجل أوضح بلون مختلف
            return ($row->attribute === 'payWay' && (string) $row->oldValue === '0') ? ['class' => 'table-danger'] : [];
        },
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'label' => 'رقم الفاتورة',
                'format' => 'raw',
                'value' => fn($row) => Html::a(Html::encode((string) $row->billId), ['sales/print', 'id' => $row->salesId]),
            ],
            ['label' => 'الحقل', 'value' => fn($row) => app\models\SalesAudit::LABELS[$row->attribute] ?? $row->attribute],
            ['label' => 'قبل', 'value' => fn($row) => $row->display($row->oldValue)],
            ['label' => 'بعد', 'value' => fn($row) => $row->display($row->newValue)],
            ['label' => 'المستخدم', 'value' => fn($row) => $row->user?->username],
            ['label' => 'الوقت', 'value' => 'created_at'],
            [
                'label' => '',
                'format' => 'raw',
                'value' => fn($row) => $row->reviewed
                    ? '<span class="text-success"><i class="bi bi-check2"></i> مراجَع</span>'
                    : Html::a('تمت المراجعة', ['review', 'salesId' => $row->salesId], [
                        'class' => 'btn btn-sm btn-outline-success',
                        'data' => ['method' => 'post'],
                        'title' => 'يعلّم كل تعديلات هذه الفاتورة كمراجَعة',
                    ]),
            ],
        ],
    ]) ?>

</div>
