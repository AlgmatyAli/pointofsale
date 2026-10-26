<?php

use app\models\CompanyInfo;

/**
 * الشريط العلوي والجانبي والمحتوى (نفس main.php) لتخطيطي invoice و report.
 * الطباعة لا تتأثر: printInvoice() و printReport() تطبعان الفاتورة/التقرير وحده داخل إطار معزول،
 * و print.css يخفي الشريطين عند الطباعة المباشرة (Ctrl+P).
 *
 * @var \yii\web\View $this
 * @var string $content
 */

$company = CompanyInfo::currentArray() ?? [];
$companyName = $company['name'] ?? Yii::$app->name;

// لون القائمة الجانبية من عمود skin، ونص أسود على الخلفية الفاتحة وأبيض على الداكنة
$sidebarColor = CompanyInfo::skinColor($company['skin'] ?? null);
$sidebarTheme = 'dark';
if ($sidebarColor !== '') {
    [$r, $g, $b] = sscanf($sidebarColor, '#%02x%02x%02x');
    $sidebarTheme = ($r * 299 + $g * 587 + $b * 114) / 1000 > 150 ? 'light' : 'dark';
}

$this->registerCssFile('@web/css/print.css', ['depends' => [\app\assets\AppAsset::class]]);
?>
<div class="app-wrapper">

    <?= $this->render('header', ['companyName' => $companyName]) ?>

    <?= $this->render('left', [
        'companyName' => $companyName,
        'sidebarTheme' => $sidebarTheme,
        'sidebarColor' => $sidebarColor,
    ]) ?>

    <?= $this->render('content', ['content' => $content]) ?>

</div>
