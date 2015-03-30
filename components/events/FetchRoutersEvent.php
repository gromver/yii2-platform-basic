<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\components\events;


use gromver\modulequery\Event;

/**
 * Class FetchRoutersEvent
 * Сбор маршрутизаторов с модулей
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \gromver\platform\basic\components\MenuUrlRule $sender
 */
class FetchRoutersEvent extends Event {
    /**
     * @var array   ["RouterClass1", "RouterClass2", ...]
     */
    public $routers;
}