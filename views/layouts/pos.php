<?php

use app\assets\AdminLteAsset;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var string $content */

// تخطيط شاشة نقطة البيع: بلا شريط جانبي ولا ترويسة ولا تذييل، وبلا أي ملفات من الإنترنت
AdminLteAsset::register($this);
$this->registerCssFile('@web/css/retail.css', ['depends' => [AdminLteAsset::class]]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" dir="rtl" data-bs-theme="light">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title ?: 'نقطة البيع') ?></title>
    <?php $this->head() ?>
</head>
<body class="pos-body">
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
