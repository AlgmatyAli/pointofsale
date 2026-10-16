<?php

use app\assets\SaleAsset;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var string $content */

// ملاحظة: SaleAsset غير موجود في الملفات المرسلة. على الأرجح يعتمد على
// yii\bootstrap\BootstrapAsset (Bootstrap 3) ويحتاج نفس تعديل AppAsset.
SaleAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" dir="rtl" data-bs-theme="light">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
