<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\news;


use gromver\modulequery\ModuleEventsInterface;
use gromver\platform\basic\components\MenuUrlRule;
use gromver\platform\basic\widgets\Desktop;
use gromver\platform\basic\widgets\MenuItemRoutes;
use gromver\platform\basic\modules\news\components\MenuRouterNews;
use gromver\platform\basic\modules\news\models\Category;
use gromver\platform\basic\modules\news\models\Post;
use gromver\platform\basic\modules\search\modules\elastic\Module as ElasticModule;
use gromver\platform\basic\modules\search\modules\elastic\widgets\SearchResultsFrontend as ElasticSearchResults;
use gromver\platform\basic\modules\search\modules\sql\widgets\SearchResultsFrontend as SqlSearchResults;
use Yii;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements ModuleEventsInterface
{
    public $controllerNamespace = 'gromver\platform\basic\modules\news\controllers';
    public $defaultRoute = 'backend/post';
    public $rssPageSize = 50;

    /**
     * @param $event \gromver\platform\basic\widgets\events\DesktopEvent
     */
    public function addDesktopItem($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'News'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Categories'), 'url' => ['/grom/news/backend/category/index']],
                ['label' => Yii::t('gromver.platform', 'Posts'), 'url' => ['/grom/news/backend/post/index']],
            ]
        ];
    }

    /**
     * @param $event \gromver\platform\basic\widgets\events\MenuItemRoutesEvent
     */
    public function addMenuItemRoutes($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'News'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Post View'), 'url' => ['/grom/news/backend/post/select']],
                ['label' => Yii::t('gromver.platform', 'Category View'), 'url' => ['/grom/news/backend/category/select']],
                ['label' => Yii::t('gromver.platform', 'All Posts'), 'route' => 'grom/news/frontend/post/index'],
            ]
        ];
    }

    /**
     * @param $event \gromver\platform\basic\components\events\FetchRoutersEvent
     */
    public function addMenuRouter($event)
    {
        $event->routers[] = MenuRouterNews::className();
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Desktop::EVENT_FETCH_ITEMS => 'addDesktopItem',
            MenuItemRoutes::EVENT_FETCH_ITEMS => 'addMenuItemRoutes',
            MenuUrlRule::EVENT_FETCH_MODULE_ROUTERS => 'addMenuRouter',
            ElasticModule::EVENT_BEFORE_CREATE_INDEX . Post::className() => [Post::className(), 'elasticBeforeCreateIndex'],
            ElasticSearchResults::EVENT_BEFORE_SEARCH . Post::className()  => [Post::className(), 'elasticBeforeFrontendSearch'],
            ElasticSearchResults::EVENT_BEFORE_SEARCH . Category::className()  => [Category::className(), 'elasticBeforeFrontendSearch'],
            SqlSearchResults::EVENT_BEFORE_SEARCH . Post::className() => [Post::className(), 'sqlBeforeFrontendSearch'],
            SqlSearchResults::EVENT_BEFORE_SEARCH . Category::className() => [Category::className(), 'sqlBeforeFrontendSearch'],
        ];
    }
}
