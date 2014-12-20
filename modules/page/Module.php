<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\page;

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
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Pages'), 'url' => ['/' . $this->getUniqueId() . '/backend/default/index']]
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
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Page View'), 'url' => ['/' . $this->getUniqueId() . '/backend/default/select']],
            ]
        ];
    }
}
