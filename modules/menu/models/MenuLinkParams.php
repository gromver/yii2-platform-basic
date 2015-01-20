<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\menu\models;


use Yii;

/**
 * Class MenuLinkParams
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuLinkParams extends \yii\base\Model
{
    public $title;
    public $class;
    public $style;
    public $target;
    public $onclick;
    public $rel;

    public function rules()
    {
        return [
            [['title', 'class', 'style', 'target', 'onclick', 'rel'], 'string']
        ];
    }
}