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
 * Class MenuRouterUrlRuleCreate
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuRouterUrlRuleCreate extends MenuRouterUrlRule
{
    /**
     * @var string
     */
    public $requestRoute;
    /**
     * @var array
     */
    public $requestParams;

    /**
     * @inheritdoc
     */
    public function process($requestInfo, $menuUrlRule)
    {
        if ($this->requestRoute && $this->requestRoute != $requestInfo->requestRoute) {
            return false;
        }

        if (isset($this->requestParams)) {
            foreach ($this->requestParams as $param) {
                if (!isset($requestInfo->requestParams[$param])) {
                    return false;
                }
            }
        }

        return $menuUrlRule->getRouter($this->router)->{$this->handler}($requestInfo);
    }
} 