<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\components;


use yii\base\Object;

/**
 * Class MenuRequest
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuRequest extends Object {
    public $menuMap;
    public $menuRoute;
    public $menuParams;
    public $requestRoute;
    public $requestParams;
}