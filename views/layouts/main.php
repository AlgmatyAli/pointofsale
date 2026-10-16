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

// استعلام واحد مخزّن مؤقتاً بدل استعلامين في كل طلب (يُحذف المفتاح عند حفظ CompanyInfo)
$company = Yii::$app->cache->getOrSet(
    'company-info',
    fn() => CompanyInfo::find()->select(['skin', 'name'])->asArray()->one() ?: [],
    3600
);
$companyName = $company['name'] ?? Yii::$app->name;

// قيم skin القديمة في AdminLTE 2 مثل skin-blue-light => شريط جانبي فاتح، وغيرها => داكن
$sidebarTheme = str_ends_with((string) ($company['skin'] ?? ''), '-light') ? 'light' : 'dark';
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

        <?= $this->render('left', ['companyName' => $companyName, 'sidebarTheme' => $sidebarTheme]) ?>

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