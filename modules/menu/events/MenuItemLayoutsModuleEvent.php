<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\menu\events;


/**
 * Список шаблонов приложения применимых для пункта меню
 * Class MenuItemLayoutsModuleEvent
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property $sender null
 */
class MenuItemLayoutsModuleEvent extends \gromver\modulequery\Event {
    /**
     * @var array   ['@app/views/layouts/main' => 'Main']
     */
    public $items;
} 