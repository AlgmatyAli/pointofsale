<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/site.css',
        // تنبيه: style.css و util.css و main.css تبدو من قالب تسجيل دخول جاهز وقد تتعارض
        // مع القالب الجديد. جرّب تعطيلها واحداً واحداً، أو حمّلها في صفحة الدخول فقط.
        'css/style.css',
        'css/util.css',
        'css/main.css',
        'css/sweet-alert.css',
        'css/bs5-compat.css', // يجب أن يبقى الأخير. مؤقت: احذفه بعد ترحيل كل الصفحات
    ];

    public $js = [
        'js/ajax-modal-popup.js',
        'js/sweet-alert.js',
        // 'js/loadSalesGrid.js',
        'js/tafqeet.js',
    ];

    public $depends = [
        AdminLteAsset::class,
    ];
}
