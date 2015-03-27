<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widgets\events;


use gromver\modulequery\Event;

/**
 * Class DesktopEvent
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class DesktopEvent extends Event {
    /**
     * @var array
     */
    public $items;
}