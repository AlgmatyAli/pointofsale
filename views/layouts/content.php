<?php

use yii\bootstrap5\Alert;
use yii\bootstrap5\Breadcrumbs;

/** @var \yii\web\View $this */
/** @var string $content */
?>
<main class="app-main">

    <?php if (!empty($this->params['breadcrumbs'])): ?>
        <div class="app-content-header">
            <div class="container-fluid">
                <?= Breadcrumbs::widget([
                    'links' => $this->params['breadcrumbs'],
                    'options' => ['class' => 'breadcrumb mb-0'],
                ]) ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="app-content">
        <div class="container-fluid">
            <?php
            // بديل dmstr\widgets\Alert: يعرض كل رسائل الـ flash (success, error, warning, info...)
            foreach (Yii::$app->session->getAllFlashes() as $type => $messages) {
                $type = $type === 'error' ? 'danger' : $type;
                foreach ((array) $messages as $message) {
                    echo Alert::widget([
                        'body' => $message,
                        'options' => ['class' => "alert-$type"],
                    ]);
                }
            }
            ?>
            <?= $content ?>
        </div>
    </div>

</main>

<footer class="app-footer">
    <div class="float-end d-none d-sm-inline"><b>الإصدار</b> 1.0</div>
    <strong>&copy; <?= date('Y') ?> Jupiter.</strong> جميع الحقوق محفوظة.
</footer>
