<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\news;

use gromver\platform\basic\interfaces\DesktopInterface;
use gromver\platform\basic\interfaces\MenuItemRoutesInterface;
use gromver\platform\basic\interfaces\MenuRouterInterface;
use gromver\platform\basic\modules\news\components\MenuRouterNews;
use Yii;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements MenuRouterInterface, DesktopInterface, MenuItemRoutesInterface
{
    public $controllerNamespace = 'gromver\platform\basic\modules\news\controllers';
    public $defaultRoute = 'backend/post';
    public $desktopOrder = 4;
    public $rssPageSize = 50;

    /**
     * @inheritdoc
     */
    public function getDesktopItem()
    {
        return [
            'label' => Yii::t('gromver.platform', 'News'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Categories'), 'url' => ['/grom/news/backend/category/index']],
                ['label' => Yii::t('gromver.platform', 'Posts'), 'url' => ['/grom/news/backend/post/index']],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getMenuItemRoutes()
    {
        return [
            'label' => Yii::t('gromver.platform', 'News'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Post View'), 'url' => ['/grom/news/backend/post/select']],
                ['label' => Yii::t('gromver.platform', 'Category View'), 'url' => ['/grom/news/backend/category/select']],
                ['label' => Yii::t('gromver.platform', 'All Posts'), 'route' => 'grom/news/frontend/post/index'],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getMenuRouter()
    {
        return MenuRouterNews::className();
    }
}
