<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\assets;


/**
 * Class HcStickyAsset
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class HcStickyAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@gromver/platform/basic/assets/layout';

    public $js = [
        'js/jquery.hc-sticky.min.js'
    ];

    public $depends = [
        '\yii\web\JqueryAsset'
    ];
}
