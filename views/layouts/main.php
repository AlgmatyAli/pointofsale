<?php

use app\assets\AppAsset;
use app\models\CompanyInfo;
use yii\bootstrap5\Modal;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var string $content */

// للإبقاء على السلوك القديم. الأفضل لاحقاً: $this->layout = 'main-login'; داخل الكنترولر.
if (Yii::$app->controller->action->id === 'login') {
    echo $this->render('main-login', ['content' => $content]);
    return;
}

AppAsset::register($this);

// من الكاش بدل استعلام في كل طلب (يُمسح عند حفظ CompanyInfo)
$company = CompanyInfo::currentArray() ?? [];
$companyName = $company['name'] ?? Yii::$app->name;

// لون القائمة الجانبية من عمود skin (يُختار من قائمة ألوان Bootstrap في بيانات الشركة)
$sidebarColor = CompanyInfo::skinColor($company['skin'] ?? null);
$sidebarTheme = 'dark';
if ($sidebarColor !== '') {
    // نص أسود على الخلفية الفاتحة وأبيض على الداكنة
    [$r, $g, $b] = sscanf($sidebarColor, '#%02x%02x%02x');
    $sidebarTheme = ($r * 299 + $g * 587 + $b * 114) / 1000 > 150 ? 'light' : 'dark';
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" dir="rtl" data-bs-theme="light">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title ? $this->title . ' | ' . $companyName : $companyName) ?></title>
    <?php $this->head() ?>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <?php $this->beginBody() ?>

    <div class="app-wrapper">

        <?= $this->render('header', ['companyName' => $companyName]) ?>

        <?= $this->render('left', [
            'companyName' => $companyName,
            'sidebarTheme' => $sidebarTheme,
            'sidebarColor' => $sidebarColor,
        ]) ?>

        <?= $this->render('content', ['content' => $content]) ?>

    </div>

    <?php
    Modal::begin([
        'id' => 'modal',
        'title' => '<b>' . Yii::t('app', 'منظومة المبيعات') . '</b>',
        'headerOptions' => ['id' => 'modalHeader'],
        'size' => Modal::SIZE_EXTRA_LARGE,
        // static: لا يُغلق بالنقر خارج النافذة، ويُغلق بزر ESC أو بزر الإغلاق
        'clientOptions' => ['backdrop' => 'static', 'keyboard' => true],
    ]);
    echo '<div id="modalContent"></div>';
    Modal::end();
    ?>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>