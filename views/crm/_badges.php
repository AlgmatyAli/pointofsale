<?php

use yii\helpers\Html;

/** شارات المرحلة والحالة لزبون. @var app\models\CrmClient|null $crm @var bool $promise */
$promise = $promise ?? false;
if ($promise) {
    echo '<span class="badge text-bg-primary">وعد بالدفع</span> ';
}
if ($crm !== null && (int) $crm->state !== app\models\CrmClient::STATE_NORMAL) {
    echo '<span class="badge text-bg-' . ((int) $crm->state === app\models\CrmClient::STATE_DISPUTED ? 'secondary' : 'dark') . '">'
        . Html::encode(app\models\CrmClient::STATES[(int) $crm->state]) . '</span> ';
}
if ($crm !== null && $crm->level !== null) {
    echo '<span class="badge text-bg-' . Html::encode($crm->level->color) . '">' . Html::encode($crm->level->name) . '</span> ';
} elseif ($crm !== null && (float) $crm->balance > 0) {
    echo '<span class="badge text-bg-success">منتظم</span> ';
}
