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
use gromver\platform\basic\widgets\SearchResultsElasticsearch;
use gromver\platform\basic\widgets\SearchResultsSql;
use gromver\platform\basic\modules\elasticsearch\Module as ElasticsearchModule;
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
    public $desktopOrder = 4;
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
     * @param $event \gromver\platform\basic\widgets\events\MenuUrlRuleEvent
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
            SearchResultsSql::EVENT_BEFORE_SEARCH . Post::className()  => [Post::className(), 'sqlBeforeSearch'],
            SearchResultsSql::EVENT_BEFORE_SEARCH . Category::className()  => [Category::className(), 'sqlBeforeSearch'],
            ElasticsearchModule::EVENT_BEFORE_CREATE_INDEX . Post::className() => [Post::className(), 'elasticsearchBeforeCreateIndex'],
            SearchResultsElasticsearch::EVENT_BEFORE_SEARCH . Post::className() => [Post::className(), 'elasticsearchBeforeSearch'],
            SearchResultsElasticsearch::EVENT_BEFORE_SEARCH . Category::className() => [Category::className(), 'elasticsearchBeforeSearch'],
        ];
    }
}
