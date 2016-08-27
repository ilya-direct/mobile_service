<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $sourcePath = '@app/themes/classic/resources';

    public $css = [
        'css/reset-min.css',
        'css/main.css',
        'css/jquery.bxslider.css',
        'css/icons.css',
    ];
    public $js = [
//        'js/bootstrap.min.js',
        'js/respond.js',
        'js/jquery.bxslider.min.js',
        'js/jquery.maskedinput-1.4.1.js',
        'js/jquery.dropdown.min.js',
        'js/main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
