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
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
       // 'css/template.css',
        'css/site.css',
        'css/style.css',
        'css/util.css',
        'css/main.css',
        'css/sweet-alert.css',
    ];
    public $js = [
        'js/ajax-modal-popup.js',
        'js/sweet-alert.js',
       // 'js/loadSalesGrid.js',
       // 'js/popup.js',
        //'js/invoice.js',
        'js/tafqeet.js',
        //'js/ajaxsave.js',
    //    'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',  
    ];
}
