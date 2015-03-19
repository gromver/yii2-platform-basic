<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\components;


/**
 * Class MenuRouter
 * Базовый класс для описания правил маршрутизации
 * используется на уровне модулей в связке с \gromver\platform\basic\interfaces\module\MenuRouterInterface
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuRouter extends \yii\base\Object
{
    /**
     * @return array
     */
    public function createUrlRules()
    {
        return [];
    }

    /**
     * @return array
     */
    public function parseUrlRules()
    {
        return [];
    }
} 