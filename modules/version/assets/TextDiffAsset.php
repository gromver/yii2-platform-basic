<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\version\assets;

use yii\web\AssetBundle;

/**
 * Class TextDiffAsset
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class TextDiffAsset extends AssetBundle
{
    public $sourcePath = '@gromver/platform/basic/modules/version/assets/PrettyTextDiff';
    public $js = [
        'diff_match_patch.js',
        'jquery.pretty-text-diff.min.js',
    ];
    public $css = [
        'style.css'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}