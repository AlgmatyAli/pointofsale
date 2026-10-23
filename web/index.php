<?php

// وضع التطوير يعرض تفاصيل الأخطاء والكود وأدوات debug/gii، لذلك هو مغلق افتراضياً.
// لتشغيله أثناء البرمجة فقط: أنشئ ملفاً فارغاً باسم DEV_MODE بجانب هذا الملف (web/DEV_MODE)،
// واحذفه قبل استخدام النظام في المحل أو على الشبكة.
$devMode = is_file(__DIR__ . '/DEV_MODE');
defined('YII_DEBUG') or define('YII_DEBUG', $devMode);
defined('YII_ENV') or define('YII_ENV', $devMode ? 'dev' : 'prod');

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';
(new yii\web\Application($config))->run();
