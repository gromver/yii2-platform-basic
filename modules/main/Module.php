<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\main;


use gromver\modulequery\ModuleEventsInterface;
use gromver\modulequery\ModuleQuery;
use gromver\platform\basic\components\MenuManager;
use gromver\platform\basic\modules\main\models\DbState;
use gromver\platform\basic\modules\menu\models\MenuItem;
use gromver\platform\basic\modules\user\models\User;
use gromver\platform\basic\widgets\Desktop;
use gromver\platform\basic\widgets\MenuItemRoutes;
use Yii;
use yii\base\BootstrapInterface;
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
    public $desktopOrder = 1;
    public $errorLayout = '@gromver/platform/basic/modules/main/views/layouts/error';
    public $modalLayout = '@gromver/platform/basic/modules/main/views/layouts/modal';
    public $backendLayout = '@gromver/platform/basic/modules/main/views/layouts/main';

    private $_mode;

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $app->set($this->id, $this);

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
        $moduleConfigDependency = new ExpressionDependency(['expression' => '\Yii::$app->getModulesHash()']);
        Yii::$container->set('gromver\modulequery\ModuleQuery', [
            'cache' => $app->cache,
            'cacheDependency' => $moduleConfigDependency
        ]);
        Yii::$container->set('gromver\platform\basic\components\MenuMap', [
            'cache' => $app->cache,
            'cacheDependency' => DbState::dependency(MenuItem::tableName())
        ]);
        Yii::$container->set('gromver\platform\basic\components\MenuUrlRule', [
            'cache' => $app->cache,
            'cacheDependency' => $moduleConfigDependency
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

        ModuleQuery::instance()->implement('\gromver\platform\common\interfaces\BootstrapInterface')->invoke('bootstrap', [$app]);
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
     * @param $event \gromver\platform\basic\widgets\events\DesktopEvent
     */
    public function addDesktopItem($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'System'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Desktop'), 'url' => ['/grom/backend/default/index']],
                ['label' => Yii::t('gromver.platform', 'System Configuration'), 'url' => ['/grom/backend/default/params']],
                ['label' => Yii::t('gromver.platform', 'Flush Cache'), 'url' => ['/grom/backend/default/flush-cache']],
            ]
        ];
    }

    /**
     * @param $event \gromver\platform\basic\widgets\events\MenuItemRoutesEvent
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

    public function setMode($mode, $saveInSession = true)
    {
        $this->_mode = in_array($mode, self::modes()) ? $mode : self::MODE_VIEW;

        if ($saveInSession) {
            Yii::$app->session->set(self::SESSION_KEY_MODE, $mode);
        }
    }

    public function getMode()
    {
        if(!isset($this->_mode)) {
            $this->setMode(Yii::$app->session->get(self::SESSION_KEY_MODE, self::MODE_VIEW));
        }

        return $this->_mode;
    }

    public function getIsEditMode()
    {
        return $this->getMode() === self::MODE_EDIT;
    }

    public static function modes()
    {
        return [self::MODE_VIEW, self::MODE_EDIT];
    }

    public function getSiteName()
    {
        return !empty($this->params['siteName']) ? $this->params['siteName'] : Yii::$app->name;
    }

    public function applyBackendLayout()
    {
        Yii::$app->layout = $this->backendLayout;
    }

    public function applyErrorLayout()
    {
        Yii::$app->layout = $this->errorLayout;
    }

    public function applyModalLayout()
    {
        Yii::$app->layout = $this->modalLayout;
    }

    public function events()
    {
        return [
            User::EVENT_BEFORE_USER_ROLES_SAVE => 'beforeUserRolesSave',
            Desktop::EVENT_FETCH_ITEMS => 'addDesktopItem',
            MenuItemRoutes::EVENT_FETCH_ITEMS => 'addMenuItemRoutes',
        ];
    }

    /**
     * @param $user User
     * Всем пользователям всегда устанавливаем роль Authorized
     */
    public function beforeUserRolesSave($user)
    {
        $roles = $user->getRoles();
        if (!in_array('Authorized', $roles)) {
            $roles[] = 'Authorized';
            $user->setRoles($roles);
        }
    }
}
