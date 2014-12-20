<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\backend\modules\page;

use gromver\platform\basic\interfaces\DesktopInterface;
use gromver\platform\basic\interfaces\MenuItemRoutesInterface;
use Yii;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements DesktopInterface, MenuItemRoutesInterface
{
    public $controllerNamespace = 'gromver\platform\basic\modules\page\controllers';
    public $defaultRoute = 'frontend/default';
    public $desktopOrder = 5;

    /**
     * @inheritdoc
     */
    public function getDesktopItem()
    {
        return [
            'label' => Yii::t('gromver.platform', 'Pages'),
            'links' => [
                ['label' => Yii::t('gromver.platform', 'Pages'), 'url' => ['/grom/page/default/index']]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getMenuItemRoutes()
    {
        return [
            'label' => Yii::t('gromver.platform', 'Pages'),
            'routers' => [
                ['label' => Yii::t('gromver.platform', 'Page View'), 'url' => ['/grom/page/default/select']],
            ]
        ];
    }
}
