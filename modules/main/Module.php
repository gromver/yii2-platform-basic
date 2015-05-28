<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\main;


use gromver\modulequery\ModuleEvent;
use gromver\modulequery\ModuleEventsInterface;
use gromver\modulequery\ModuleQuery;
use gromver\platform\basic\components\MenuManager;
use gromver\platform\basic\modules\main\events\ListItemsModuleEvent;
use gromver\platform\basic\modules\main\models\DbState;
use gromver\platform\basic\modules\menu\models\MenuItem;
use gromver\platform\basic\modules\search\widgets\SearchResultsBackend;
use gromver\platform\basic\modules\search\widgets\SearchResultsFrontend;
use gromver\platform\basic\modules\user\models\User;
use gromver\platform\basic\modules\main\widgets\Desktop;
use gromver\platform\basic\modules\menu\widgets\MenuItemRoutes;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\caching\ExpressionDependency;
use yii\helpers\ArrayHelper;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property string $siteName
 * @property bool $isEditMode
 */
class Module extends \yii\base\Module implements BootstrapInterface, ModuleEventsInterface
{
    const SESSION_KEY_MODE = '__grom_mode';

    const MODE_EDIT = 'edit';
    const MODE_VIEW = 'view';

    const EVENT_FETCH_LIST_ITEMS = 'mainFetchListItems';

    public $controllerNamespace = '\gromver\platform\basic\modules\main\controllers';
    public $defaultRoute = 'frontend/default';
    /**
     * @var string место хранения настроек сайта
     */
    public $paramsPath = '@app/config/grom';
    /**
     * @var array список компонентов к которым нельзя попасть на прямую(grom/post/frontend/..., grom/page/frontend/...)
     * эта блокировка нужна для того чтобы управлять структурой сайта только через меню
     * во время разработки проекта ету блокировку можно снять указав в конфиге приложения
     * [
     *      'blockedUrlRules' => []
     * ]
     */
    public $blockedUrlRules = [
        'grom/news/frontend<path:(/.*)?>',
        'grom/page/frontend<path:(/.*)?>',
        'grom/tag/frontend<path:(/.*)?>',
        'grom/user/frontend<path:(/.*)?>',
    ];
    public $errorLayout = '@gromver/platform/basic/modules/main/views/layouts/error';
    public $modalLayout = '@gromver/platform/basic/modules/main/views/layouts/modal';
    public $backendLayout = '@gromver/platform/basic/modules/main/views/layouts/backend';

    /**
     * @var string
     */
    private $_mode;
    /**
     * @var null|\yii\caching\Dependency
     */
    private $_moduleConfigDependency;

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $app->set($this->id, $this);

        $this->_moduleConfigDependency = new ExpressionDependency(['expression' => '\Yii::$app->getModulesHash()']);

        DbState::bootstrap();

        Yii::$container->set('gromver\models\fields\EditorField', [
            'controller' => 'grom/media/manager',
            'editorOptions' => [
                'filebrowserBrowseUrl' => ['/grom/menu/backend/item/ckeditor-select'],
                'extraPlugins' => 'codesnippet'
            ]
        ]);
        Yii::$container->set('gromver\models\fields\MediaField', [
            'controller' => 'grom/media/manager'
        ]);
        Yii::$container->set('gromver\modulequery\ModuleQuery', [
            'cache' => $app->cache,
            'cacheDependency' => $this->_moduleConfigDependency
        ]);
        Yii::$container->set('gromver\platform\basic\components\MenuMap', [
            'cache' => $app->cache,
            'cacheDependency' => DbState::dependency(MenuItem::tableName())
        ]);
        Yii::$container->set('gromver\platform\basic\components\MenuUrlRule', [
            'cache' => $app->cache,
            'cacheDependency' => $this->_moduleConfigDependency
        ]);
        Yii::$container->set('gromver\platform\basic\modules\main\widgets\Desktop', [
            'cache' => $app->cache,
            'cacheDependency' => $this->_moduleConfigDependency
        ]);

        /** @var MenuManager $manager */
        $rules['auth'] = 'grom/auth/default/login';
        $rules['admin'] = 'grom/backend/default/index';
        if (is_array($this->blockedUrlRules) && count($this->blockedUrlRules)) {
            foreach ($this->blockedUrlRules as $rule) {
                $rules[$rule] = 'grom/default/page-not-found'; //блокируем доступ напрямую
            }
        }

        $app->urlManager->addRules($rules, false); //вставляем в начало списка

        $app->set('menuManager', \Yii::createObject(MenuManager::className()));

        // пропускаем \gromver\models\fields\events\ListItemsEvent событие, через ModuleEvent - не факт, что нужно, но почему бы и нет
        Event::on('\gromver\models\fields\ListField', 'fetchItems', function($event){
            /** @var $event \gromver\models\fields\events\ListItemsEvent */
            ModuleEvent::trigger(self::EVENT_FETCH_LIST_ITEMS, new ListItemsModuleEvent([
                'sender' => $event
            ]));
        });

        ModuleQuery::instance()->implement('\gromver\platform\common\interfaces\BootstrapInterface')->invoke('bootstrap', [$app]);
    }

    /**
     * @return null|\yii\caching\Dependency
     */
    public function getModuleConfigDependency()
    {
        return $this->_moduleConfigDependency;
    }
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->initI18N();

        $params = @include Yii::getAlias($this->paramsPath . '/params.php');

        if (is_array($params)) {
            $this->params = ArrayHelper::merge($params, $this->params);
        }

        // устанавливает мета описание сайта по умолчанию
        $view = Yii::$app->getView();
        $view->title = $this->getSiteName();
        if (!empty($this->params['keywords'])) {
            $view->registerMetaTag(['name' => 'keywords', 'content' => $this->params['keywords']], 'keywords');
        }
        if (!empty($this->params['description'])) {
            $view->registerMetaTag(['name' => 'description', 'content' => $this->params['description']], 'description');
        }
        if (!empty($this->params['robots'])) {
            $view->registerMetaTag(['name' => 'robots', 'content' => $this->params['robots']], 'robots');
        }
        $view->registerMetaTag(['name' => 'generator', 'content' => 'Grom Platform - Open Source Yii2 Development Platform.'], 'generator');
    }

    public function initI18N()
    {
        Yii::$app->i18n->translations['gromver.*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@gromver/platform/basic/messages',
        ];
    }

    /**
     * @param $event \gromver\platform\basic\modules\main\widgets\events\DesktopEvent
     */
    public function addDesktopItem($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'System'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Desktop'), 'url' => ['/grom/backend/default/index']],
                ['label' => Yii::t('gromver.platform', 'System Configuration'), 'url' => ['/grom/backend/default/params']],
                ['label' => Yii::t('gromver.platform', 'Flush Cache'), 'url' => ['/grom/backend/default/flush-cache']],
                ['label' => Yii::t('gromver.platform', 'Flush Assets'), 'url' => ['/grom/backend/default/flush-assets']],
            ]
        ];
    }

    /**
     * @param $event \gromver\platform\basic\modules\menu\widgets\events\MenuItemRoutesEvent
     */
    public function addMenuItemRoutes($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'System'),
            'items' => [
                //['label' => Yii::t('gromver.platform', 'Sitemap'), 'route' => 'grom/frontend/default/sitemap'/*, 'icon' => '<i class="glyphicon glyphicon-cog"></i>'*/],
                ['label' => Yii::t('gromver.platform', 'Dummy Page'), 'route' => 'grom/frontend/default/dummy-page'],
                ['label' => Yii::t('gromver.platform', 'Contact Form'), 'route' => 'grom/frontend/default/contact'],
            ]
        ];
    }

    /**
     * @param $event \gromver\platform\basic\modules\search\widgets\events\SearchableModelsEvent
     */
    public function addSearchableModelsBackend($event)
    {
        $event->items = array_merge($event->items, [
            'gromver\platform\basic\modules\page\models\Page' => Yii::t('gromver.platform', 'Pages'),
            'gromver\platform\basic\modules\news\models\Post' => Yii::t('gromver.platform', 'Posts'),
            'gromver\platform\basic\modules\news\models\Category' => Yii::t('gromver.platform', 'Categories'),
        ]);
    }

    /**
     * @param $event \gromver\platform\basic\modules\search\widgets\events\SearchableModelsEvent
     */
    public function addSearchableModelsFrontend($event)
    {
        $event->items = array_merge($event->items, [
            'gromver\platform\basic\modules\page\models\Page' => Yii::t('gromver.platform', 'Pages'),
            'gromver\platform\basic\modules\news\models\Post' => Yii::t('gromver.platform', 'Posts'),
            'gromver\platform\basic\modules\news\models\Category' => Yii::t('gromver.platform', 'Categories'),
        ]);
    }

    /**
     * @param string $mode
     * @param bool $saveInSession
     */
    public function setMode($mode, $saveInSession = true)
    {
        $this->_mode = in_array($mode, self::modes()) ? $mode : self::MODE_VIEW;

        if ($saveInSession) {
            Yii::$app->session->set(self::SESSION_KEY_MODE, $mode);
        }
    }

    /**
     * @return string
     */
    public function getMode()
    {
        if(!isset($this->_mode)) {
            $this->setMode(Yii::$app->session->get(self::SESSION_KEY_MODE, self::MODE_VIEW));
        }

        return $this->_mode;
    }

    /**
     * @return bool
     */
    public function getIsEditMode()
    {
        return $this->getMode() === self::MODE_EDIT;
    }

    /**
     * @return array
     */
    public static function modes()
    {
        return [self::MODE_VIEW, self::MODE_EDIT];
    }

    /**
     * @return string
     */
    public function getSiteName()
    {
        return !empty($this->params['siteName']) ? $this->params['siteName'] : Yii::$app->name;
    }

    /**
     * apply platform's backend layout
     */
    public function applyBackendLayout()
    {
        Yii::$app->layout = $this->backendLayout;
    }

    /**
     * apply platform's error layout
     */
    public function applyErrorLayout()
    {
        Yii::$app->layout = $this->errorLayout;
    }

    /**
     * apply platform's modal layout
     */
    public function applyModalLayout()
    {
        Yii::$app->layout = $this->modalLayout;
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            User::EVENT_BEFORE_USER_ROLES_SAVE => 'beforeUserRolesSave',
            Desktop::EVENT_FETCH_ITEMS => 'addDesktopItem',
            MenuItemRoutes::EVENT_FETCH_ITEMS => 'addMenuItemRoutes',
            SearchResultsBackend::EVENT_FETCH_SEARCHABLE_MODELS => 'addSearchableModelsBackend',
            SearchResultsFrontend::EVENT_FETCH_SEARCHABLE_MODELS => 'addSearchableModelsFrontend'
        ];
    }

    /**
     * @param $event \gromver\platform\basic\modules\user\models\events\BeforeRolesSaveEvent
     * Всем пользователям всегда устанавливаем роль Authorized
     */
    public function beforeUserRolesSave($event)
    {
        $roles = $event->sender->getRoles();
        if (!in_array('Authorized', $roles)) {
            $roles[] = 'Authorized';
            $event->sender->setRoles($roles);
        }
    }
}
