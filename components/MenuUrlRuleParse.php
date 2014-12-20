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
 * Class MenuUrlRuleParse
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuUrlRuleParse extends MenuUrlRule {
    /**
     * @var string
     */
    public $menuRoute;

    /**
     * @inheritdoc
     */
    public function process($requestInfo, $menuManager)
    {
        if ($this->menuRoute && $this->menuRoute != $requestInfo->menuRoute) {
            return false;
        }

        return $menuManager->getRouter($this->router)->{$this->handler}($requestInfo);
    }
} 