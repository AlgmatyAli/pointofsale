<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

// الكوكيز تُعلَّم Secure فقط عند الاتصال عبر HTTPS؛ على HTTP العادي (مثل http://192.168.x.x)
// لو كانت Secure لما أرسلها المتصفح ولتعطّل تسجيل الدخول.
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['SERVER_PORT'] ?? null) == 443);

$config = [
    'id' => 'basic',
    'name' => 'نظام نقاط البيع - Jupiter',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'language' => 'ar-AR',
    'sourceLanguage' => 'en-US',
    'modules' => [
        'gridview' => [
            'class' => '\kartik\grid\Module',
            'bsVersion' => '5.x',
        ]
    ],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'request' => [
            'cookieValidationKey' => '21232f297a57a5a743894a0e4a801fc3',
            'enableCsrfValidation' => true,
            'csrfParam' => '_csrf-frontend',
            'csrfCookie' => [
                'httpOnly' => true,
                'secure' => $isHttps,
                'sameSite' => yii\web\Cookie::SAME_SITE_LAX,
            ],
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        // Duration of schema cache.
        'schemaCacheDuration' => '3600',

        // Name of the cache component used to store schema information
        'schemaCache' => 'cache',

        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'identityCookie' => [
                'name' => '_identity',
                'httpOnly' => true,
                'secure' => $isHttps,
                'sameSite' => yii\web\Cookie::SAME_SITE_LAX,
            ],
        ],

        'session' => [
            'name' => 'JUPITERSESSID',
            'cookieParams' => [
                'httpOnly' => true,
                'secure' => $isHttps,
                'sameSite' => yii\web\Cookie::SAME_SITE_LAX,
            ],
        ],

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,

        'formatter' => [
            'currencyCode' => 'IDR',
            'decimalSeparator' => '.',
            'locale' => 'id',
            'thousandSeparator' => ',',
        ],

        'assetManager' => [
            'bundles' => [
                'yii\bootstrap\BootstrapAsset'       => ['css' => []],
                'yii\bootstrap\BootstrapPluginAsset' => ['js'  => []],
                'yii\bootstrap5\BootstrapAsset'      => ['css' => []],
            ],
        ],

    ],

    'params' => $params,

    // حماية عامة: أي صفحة في النظام تتطلب تسجيل الدخول، ما عدا صفحات الدخول والأخطاء
    // ورابط الفاتورة العام الموقَّع (sales/invoice-pdf) الذي يتحقق من التوقيع بنفسه.
    // الصلاحيات التفصيلية (RBAC) تبقى في behaviors() داخل كل Controller.
    'as access' => [
        'class' => \yii\filters\AccessControl::class,
        'except' => ['site/login', 'site/error', 'site/captcha', 'sales/invoice-pdf', 'debug/*', 'gii/*'],
        'rules' => [
            ['allow' => true, 'roles' => ['@']],
        ],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // مسموح فقط من نفس الجهاز
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // مسموح فقط من نفس الجهاز
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
