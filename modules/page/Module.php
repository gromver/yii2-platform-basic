<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\page;


use gromver\modulequery\ModuleEventsInterface;
use gromver\platform\basic\components\MenuUrlRule;
use gromver\platform\basic\widgets\Desktop;
use gromver\platform\basic\widgets\MenuItemRoutes;
use gromver\platform\basic\modules\page\components\MenuRouterPage;
use gromver\platform\basic\modules\page\models\Page;
use gromver\platform\basic\modules\search\modules\sql\widgets\SearchResultsFrontend as SqlSearchResults;
use gromver\platform\basic\modules\search\modules\elastic\widgets\SearchResultsFrontend as ElasticSearchResults;
use Yii;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements ModuleEventsInterface
{
    public $controllerNamespace = 'gromver\platform\basic\modules\page\controllers';
    public $defaultRoute = 'frontend/default';
    public $desktopOrder = 5;

    /**
     * @param $event \gromver\platform\basic\widgets\events\DesktopEvent
     */
    public function addDesktopItem($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'Pages'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Pages'), 'url' => ['/grom/page/backend/default/index']]
            ]
        ];
    }

    /**
     * @param $event \gromver\platform\basic\widgets\events\MenuItemRoutesEvent
     */
    public function addMenuItemRoutes($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'Pages'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Page View'), 'url' => ['/grom/page/backend/default/select']],
                ['label' => Yii::t('gromver.platform', 'Page Guide'), 'url' => ['/grom/page/backend/default/select', 'route' => 'grom/page/frontend/default/guide']],
            ]
        ];
    }

    /**
     * @param $event \gromver\platform\basic\components\events\FetchRoutersEvent
     */
    public function addMenuRouter($event)
    {
        $event->routers[] = MenuRouterPage::className();
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
            SqlSearchResults::EVENT_BEFORE_SEARCH . Page::className()  => [Page::className(), 'sqlBeforeFrontendSearch'],
            ElasticSearchResults::EVENT_BEFORE_SEARCH . Page::className() => [Page::className(), 'elasticBeforeFrontendSearch'],
        ];
    }
}
