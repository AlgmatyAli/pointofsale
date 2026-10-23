<?php

use app\widgets\SidebarMenu;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var string $companyName */
/** @var string $sidebarTheme light|dark */
/** @var string $sidebarColor كود لون #rrggbb من عمود skin، أو فارغ للون الافتراضي */
$sidebarColor = $sidebarColor ?? '';
?>
<?= Html::beginTag('aside', [
    'class' => ['app-sidebar', 'shadow', $sidebarColor ? 'sidebar-custom' : 'bg-body-secondary'],
    'data-bs-theme' => $sidebarTheme,
    'style' => $sidebarColor ? ['--sidebar-bg' => $sidebarColor] : [],
]) ?>
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