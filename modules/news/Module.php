<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\backend\modules\news;

use gromver\platform\basic\interfaces\DesktopInterface;
use gromver\platform\basic\interfaces\MenuItemRoutesInterface;
use gromver\platform\basic\interfaces\MenuRouterInterface;
use gromver\platform\frontend\modules\news\components\MenuRouterNews;
use Yii;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements MenuRouterInterface, DesktopInterface, MenuItemRoutesInterface
{
    public $controllerNamespace = 'gromver\platform\basic\modules\news\controllers';
    public $defaultRoute = 'frontend/post';
    public $desktopOrder = 4;
    public $rssPageSize = 50;

    /**
     * @inheritdoc
     */
    public function getDesktopItem()
    {
        return [
            'label' => Yii::t('gromver.platform', 'News'),
            'links' => [
                ['label' => Yii::t('gromver.platform', 'Categories'), 'url' => ['/grom/news/category']],
                ['label' => Yii::t('gromver.platform', 'Posts'), 'url' => ['/grom/news/post']],
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
            'routers' => [
                ['label' => Yii::t('gromver.platform', 'Post View'), 'url' => ['/grom/news/post/select']],
                ['label' => Yii::t('gromver.platform', 'Category View'), 'url' => ['/grom/news/category/select']],
                ['label' => Yii::t('gromver.platform', 'All Posts'), 'route' => 'grom/news/post/index'],
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
