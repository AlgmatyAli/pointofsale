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
        'css/style.css',
        'css/sweet-alert.css',
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
