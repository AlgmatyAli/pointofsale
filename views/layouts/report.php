<?php

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/** @var \yii\web\View $this */
/** @var string $content */

AppAsset::register($this);

// report.css يجب أن يُحمَّل بعد AppAsset حتى لا تغلبه أنماط Bootstrap/AdminLTE
$this->registerCssFile('@web/css/report.css', ['depends' => [AppAsset::class]]);

// طباعة التقرير داخل إطار مستقل لا يحمّل إلا report.css، فلا تتداخل معه أنماط الموقع.
// printReport() تطبع العنصر #report، و printReport('id') تطبع عنصراً آخر.
$printJs = <<<'JS'
window.printReport = function (id) {
    var report = document.getElementById(id || 'report');
    if (!report) { window.print(); return; }

    var old = document.getElementById('report-print-frame');
    if (old) { old.remove(); }

    var frame = document.createElement('iframe');
    frame.id = 'report-print-frame';
    frame.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;';
    document.body.appendChild(frame);

    var win = frame.contentWindow;
    var doc = win.document;
    doc.open();
    doc.write(
        '<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="utf-8">' +
        '<title>' + (document.title || 'تقرير') + '</title>' +
        '<link rel="stylesheet" href="__REPORT_CSS__">' +
        '<style>body{margin:0;background:#fff}</style>' +
        '</head><body>' + report.outerHTML + '</body></html>'
    );
    doc.close();

    var go = function () {
        win.focus();
        win.print();
        setTimeout(function () { frame.remove(); }, 1500);
    };
    // ننتظر تحميل التنسيق والشعار قبل فتح نافذة الطباعة
    if (doc.readyState === 'complete') {
        setTimeout(go, 300);
    } else {
        win.onload = function () { setTimeout(go, 300); };
    }
};
JS;
$this->registerJs(
    str_replace('__REPORT_CSS__', Url::to('@web/css/report.css'), $printJs),
    View::POS_HEAD,
    'report-print-frame'
);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" dir="rtl" data-bs-theme="light">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title ?: Yii::$app->name) ?></title>
    <?php $this->head() ?>
</head>
<body class="bg-white">
<?php $this->beginBody() ?>

<div class="container-fluid py-3">
    <?= $content ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
