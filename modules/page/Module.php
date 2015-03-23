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
use gromver\platform\basic\interfaces\module\DesktopInterface;
use gromver\platform\basic\interfaces\module\MenuItemRoutesInterface;
use gromver\platform\basic\interfaces\module\MenuRouterInterface;
use gromver\platform\basic\modules\page\components\MenuRouterPage;
use gromver\platform\basic\modules\page\models\Page;
use gromver\platform\basic\widgets\SearchResultsElasticsearch;
use gromver\platform\basic\widgets\SearchResultsSql;
use gromver\platform\basic\modules\elasticsearch\Module as ElasticsearchModule;
use Yii;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements DesktopInterface, MenuItemRoutesInterface, MenuRouterInterface, ModuleEventsInterface
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
                ['label' => Yii::t('gromver.platform', 'Pages'), 'url' => ['/grom/page/backend/default/index']]
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
                ['label' => Yii::t('gromver.platform', 'Page View'), 'url' => ['/grom/page/backend/default/select']],
                ['label' => Yii::t('gromver.platform', 'Page Guide'), 'url' => ['/grom/page/backend/default/select', 'route' => 'grom/page/frontend/default/guide']],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getMenuRouter()
    {
        return MenuRouterPage::className();
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            SearchResultsSql::EVENT_BEFORE_SEARCH . Page::className()  => [Page::className(), 'sqlBeforeSearch'],
            SearchResultsElasticsearch::EVENT_BEFORE_SEARCH . Page::className() => [Page::className(), 'elasticsearchBeforeSearch'],
        ];
    }
}
