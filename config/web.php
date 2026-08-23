<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

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
            'class' => '\kartik\grid\Module'
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
                'secure' => !YII_ENV_DEV,
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
                'secure' => !YII_ENV_DEV,
                'sameSite' => yii\web\Cookie::SAME_SITE_LAX,
            ],
        ],

        'session' => [
            'name' => 'JUPITERSESSID',
            'cookieParams' => [
                'httpOnly' => true,
                'secure' => !YII_ENV_DEV,
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

    ],

    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
