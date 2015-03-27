<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\user;


use gromver\modulequery\ModuleEventsInterface;
use gromver\platform\basic\widgets\Desktop;
use gromver\platform\basic\widgets\MenuItemRoutes;
use Yii;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements ModuleEventsInterface
{
    public $controllerNamespace = 'gromver\platform\basic\modules\user\controllers';
    public $defaultRoute = 'frontend/default';
    public $allowDelete = true;   //позволяет удалять пользователей из БД, при условии что они уже имеют статус User::STATUS_DELETED
    public $userParamsClass = 'gromver\platform\basic\modules\user\models\UserParams';
    public $desktopOrder = 8;

    /**
     * @param $event \gromver\platform\basic\widgets\events\DesktopEvent
     */
    public function addDesktopItem($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'Users'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Users'), 'url' => ['/grom/user/backend/default/index']]
            ]
        ];
    }

    /**
     * @param $event \gromver\platform\basic\widgets\events\MenuItemRoutesEvent
     */
    public function addMenuItemRoutes($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'Users'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'User Profile'), 'route' => 'grom/user/frontend/default/update'],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Desktop::EVENT_FETCH_ITEMS => 'addDesktopItem',
            MenuItemRoutes::EVENT_FETCH_ITEMS => 'addMenuItemRoutes',
        ];
    }
}
