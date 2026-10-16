<?php

namespace app\assets;

use yii\bootstrap5\BootstrapPluginAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

/**
 * AdminLTE 4 (Bootstrap 5.3) بنسخة RTL، من ملفات محلية في web/vendor/adminlte
 * (مناسب لمنظومة قد تعمل على شبكة محلية بلا إنترنت).
 * ملف adminlte.rtl.min.css يتضمن Bootstrap CSS نفسه، لذلك نُفرغ css الخاص
 * بـ yii\bootstrap5\BootstrapAsset من إعدادات assetManager (انظر README).
 */
class AdminLteAsset extends AssetBundle
{
    public $basePath = '@webroot/vendor/adminlte';
    public $baseUrl = '@web/vendor/adminlte';

    public $css = [
        'css/adminlte.rtl.min.css',
        'bootstrap-icons/bootstrap-icons.min.css',
    ];

    public $js = [
        'js/adminlte.min.js',
    ];

    // YiiAsset أولاً (يحمّل jQuery) ثم Bootstrap 5 JS، فيسجّل Bootstrap إضافات jQuery
    // مثل $('#modal').modal('show') ويبقى ajax-modal-popup.js القديم شغالاً غالباً.
    public $depends = [
        YiiAsset::class,
        BootstrapPluginAsset::class,
    ];
}
