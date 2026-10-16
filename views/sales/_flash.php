<?php

use yii\helpers\Html;

/**
 * رسائل الجلسة لصفحات الطباعة (تخطيط invoice لا يعرضها بنفسه).
 * تُعرض كلها بأنواعها وتُحذف من الجلسة بعد العرض، ولا تظهر في الطباعة.
 */
foreach (Yii::$app->session->getAllFlashes(true) as $flashType => $flashMessages) {
    $flashClass = ($flashType === 'error' || $flashType === 'danger') ? 'danger'
        : (($flashType === 'success') ? 'success' : (($flashType === 'info') ? 'info' : 'warning'));
    foreach ((array) $flashMessages as $flashMessage) {
        echo '<div class="alert alert-' . $flashClass . ' d-print-none no-print mb-3" role="alert">'
            . '<i class="bi bi-exclamation-triangle"></i> ' . Html::encode($flashMessage) . '</div>';
    }
}
