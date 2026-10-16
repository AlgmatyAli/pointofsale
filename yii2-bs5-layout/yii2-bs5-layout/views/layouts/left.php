<?php

use app\widgets\SidebarMenu;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var string $companyName */
/** @var string $sidebarTheme light|dark */
?>
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="<?= Html::encode($sidebarTheme) ?>">

    <div class="sidebar-brand">
        <?= Html::a(
            '<span class="brand-text fw-light">' . Html::encode($companyName) . '</span>',
            Yii::$app->homeUrl,
            ['class' => 'brand-link']
        ) ?>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <?= SidebarMenu::widget([
                'items' => require Yii::getAlias('@app/config/sidebar-menu.php'),
            ]) ?>
        </nav>
    </div>

</aside>
