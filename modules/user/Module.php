<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\user;

use gromver\platform\basic\interfaces\module\DesktopInterface;
use gromver\platform\basic\interfaces\module\MenuItemRoutesInterface;
use Yii;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements DesktopInterface, MenuItemRoutesInterface
{
    public $controllerNamespace = 'gromver\platform\basic\modules\user\controllers';
    public $defaultRoute = 'frontend/default';
    public $allowDelete = true;   //позволяет удалять пользователей из БД, при условии что они уже имеют статус User::STATUS_DELETED
    public $userParamsClass = 'gromver\platform\basic\modules\user\models\UserParams';
    public $desktopOrder = 8;

    /**
     * @inheritdoc
     */
    public function getDesktopItem()
    {
        return [
            'label' => Yii::t('gromver.platform', 'Users'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Users'), 'url' => ['/grom/user/backend/default/index']]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getMenuItemRoutes()
    {
        return [
            'label' => Yii::t('gromver.platform', 'Users'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'User Profile'), 'route' => 'grom/user/frontend/default/update'],
            ]
        ];
    }
}
