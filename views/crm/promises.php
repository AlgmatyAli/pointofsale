<?php

use app\components\WhatsappHelper;
use app\models\CrmPromise;
use kartik\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $status */

$this->title = 'وعود الدفع';
$this->params['breadcrumbs'][] = 'متابعة الزبائن';
$this->params['breadcrumbs'][] = $this->title;
$u = Yii::$app->user;
$tabs = ['0' => 'القائمة', '1' => 'المنفّذة', '2' => 'المنكوثة', '3' => 'الملغاة', '' => 'الكل'];
if ($u->can('crmAlerts')) {
    $tabs = ['alert' => 'منكوثة لم تُراجع (' . CrmPromise::brokenCount() . ')'] + $tabs;
}
?>
<div class="crm-promises">
    <?= $this->render('_nav') ?>

    <div class="btn-group flex-wrap mb-3" role="group" aria-label="حالة الوعد">
        <?php foreach ($tabs as $k => $label): ?>
            <?= Html::a(Html::encode($label), ['promises', 'status' => $k], ['class' => 'btn btn-sm ' . ((string) $status === (string) $k ? 'btn-primary' : 'btn-outline-primary')]) ?>
        <?php endforeach; ?>
    </div>

    <?php if ($status === 'alert'): ?>
        <p class="text-body-secondary">لإيقاف البيع الآجل عن زبون افتح «بيانات الزبون» وألغِ خيار البيع الآجل.</p>
    <?php endif; ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => fn($p) => (int) $p->status === CrmPromise::OPEN && $p->promiseDate < date('Y-m-d') ? ['class' => 'table-warning'] : [],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],
            [
                'label' => 'الزبون',
                'format' => 'raw',
                'value' => fn($p) => $u->can('crmCardView') ? Html::a(Html::encode($p->client->name ?? ''), ['card', 'id' => $p->clientId]) : Html::encode($p->client->name ?? ''),
            ],
            ['attribute' => 'amount', 'value' => fn($p) => WhatsappHelper::money($p->amount)],
            'promiseDate',
            ['attribute' => 'paidSince', 'value' => fn($p) => WhatsappHelper::money($p->paidSince)],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => fn($p) => '<span class="badge text-bg-' . CrmPromise::COLORS[(int) $p->status] . '">' . CrmPromise::STATUSES[(int) $p->status] . '</span>',
            ],
            'note',
            ['label' => 'سجّله', 'value' => fn($p) => $p->createdBy->username ?? ''],
            [
                'label' => '',
                'format' => 'raw',
                'value' => function ($p) use ($u) {
                    $b = [];
                    if ((int) $p->status === CrmPromise::OPEN) {
                        if ($u->can('crmWhatsapp')) {
                            $b[] = Html::a('<i class="bi bi-whatsapp"></i> تذكير بالوعد', ['promise-whatsapp', 'id' => $p->id], ['class' => 'btn btn-sm btn-success', 'target' => '_blank', 'rel' => 'noopener']);
                        }
                        if ($u->can('crmPromiseEdit')) {
                            $b[] = Html::a('تعديل', ['promise-update', 'id' => $p->id], ['class' => 'btn btn-sm btn-outline-primary']);
                        }
                        if ($u->can('crmPromiseCancel')) {
                            $b[] = Html::a('إلغاء', ['promise-cancel', 'id' => $p->id], ['class' => 'btn btn-sm btn-outline-danger',
                                'data' => ['method' => 'post', 'confirm' => 'إلغاء وعد ' . ($p->client->name ?? '') . '؟']]);
                        }
                    }
                    if ((int) $p->status === CrmPromise::BROKEN) {
                        if ($u->can('crmWhatsapp')) {
                            $b[] = Html::a('<i class="bi bi-whatsapp"></i> واتساب', ['promise-whatsapp', 'id' => $p->id], ['class' => 'btn btn-sm btn-success', 'target' => '_blank', 'rel' => 'noopener', 'title' => 'تذكير الزبون بالوعد الذي لم يُنفَّذ']);
                        }
                        if (!$p->reviewed && $u->can('crmAlerts')) {
                            $b[] = Html::a('تمت المراجعة', ['promise-review', 'id' => $p->id], ['class' => 'btn btn-sm btn-outline-success', 'data' => ['method' => 'post']]);
                        }
                        if ($u->can('updatClient')) {
                            $b[] = Html::a('إيقاف الآجل', ['/client/update', 'id' => $p->clientId], ['class' => 'btn btn-sm btn-outline-secondary', 'title' => 'يفتح بيانات الزبون']);
                        }
                    }
                    return '<div class="d-flex gap-1 flex-wrap">' . implode('', $b) . '</div>';
                },
            ],
        ],
    ]) ?>
</div>
