<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\tag;

use gromver\platform\basic\interfaces\DesktopInterface;
use gromver\platform\basic\interfaces\MenuItemRoutesInterface;
use gromver\platform\basic\interfaces\MenuRouterInterface;
use gromver\platform\basic\modules\tag\components\MenuRouterTag;
use Yii;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements MenuRouterInterface, DesktopInterface, MenuItemRoutesInterface
{
    public $controllerNamespace = 'gromver\platform\basic\modules\tag\controllers';
    public $defaultRoute = 'frontend/default';
    public $desktopOrder = 7;

    /**
     * @inheritdoc
     */
    public function getDesktopItem()
    {
        return [
            'label' => Yii::t('gromver.platform', 'Tags'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Tags'), 'url' => ['/grom/tag/backend/default/index']],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getMenuItemRoutes()
    {
        return [
            'label' => Yii::t('gromver.platform', 'Tags'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Tag Cloud'), 'route' => 'grom/tag/frontend/default/index'],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getMenuRouter()
    {
        return MenuRouterTag::className();
    }
}
