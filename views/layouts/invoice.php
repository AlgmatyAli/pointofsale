<?php

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var \yii\web\View $this */
/** @var string $content */

AppAsset::register($this);

// template.css (قالب الفاتورة) يجب أن يُحمَّل بعد AppAsset، وإلا تغلبه أنماط Bootstrap/AdminLTE
$this->registerCssFile('@web/css/template.css', ['depends' => [AppAsset::class]]);
// الفاتورة على خلفية بيضاء داخل الصفحة (خلفية المنظومة رمادية)
$this->registerCss('.app-main #container { background: #fff; padding: 10px; }');

// طباعة الفاتورة داخل إطار مستقل لا يحمّل إلا template.css.
// هكذا لا تتداخل معها أنماط Bootstrap وAdminLTE وutil.css وmain.css التي تغيّر عرض الصفحة عند الطباعة.
$printJs = <<<'JS'
// printInvoice() تطبع #container (الفاتورة)، و printInvoice('div1') تطبع عنصراً آخر مثل قائمة الفواتير المستحقة
window.printInvoice = function (id) {
    var invoice = document.getElementById(id || 'container');
    if (!invoice) { window.print(); return; }

    var old = document.getElementById('invoice-print-frame');
    if (old) { old.remove(); }

    var frame = document.createElement('iframe');
    frame.id = 'invoice-print-frame';
    frame.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;';
    document.body.appendChild(frame);

    var win = frame.contentWindow;
    var doc = win.document;
    doc.open();
    doc.write(
        '<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="utf-8">' +
        '<title>فاتورة</title>' +
        '<link rel="stylesheet" href="__TEMPLATE_CSS__">' +
        '<style>' +
        '@page{margin:10mm}' +
        'body{margin:0;background:#fff;font-family:Tahoma,Arial,sans-serif}' +
        '.text-center{text-align:center}.row{display:block}' +
        '.noprint{display:none!important}' +
        '#container{min-height:0!important}' +
        '.no-print,.d-print-none{display:none!important}' +
        'table.table{width:100%;border-collapse:collapse}' +
        'table.table th,table.table td{border:1px solid #000;padding:3px 6px;text-align:right;font-size:12px}' +
        'img.logo{width:150px;height:150px;object-fit:contain}' +
        '.mb-4{margin-bottom:1.5rem}.my-3{margin:1rem 0}h3,h5{margin:0}' +
        '*{-webkit-print-color-adjust:exact;print-color-adjust:exact}' +
        '</style></head><body>' + invoice.outerHTML + '</body></html>'
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
    str_replace('__TEMPLATE_CSS__', Url::to('@web/css/template.css'), $printJs),
    \yii\web\View::POS_HEAD,
    'invoice-print-frame'
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
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<?php $this->beginBody() ?>

<?= $this->render('_shell', ['content' => $content]) ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
