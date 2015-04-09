<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\assets;


use yii\web\AssetBundle;

/**
 * Class BackendAsset
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class BackendAsset extends AssetBundle {
    public $sourcePath = '@gromver/platform/basic/assets/layout';
    public $css = ['css/backend.css'];
} 