<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\components;

use gromver\modulequery\ModuleQuery;
use gromver\platform\basic\modules\menu\models\MenuItem;
use Yii;
use yii\base\Component;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\caching\Cache;
use yii\di\Instance;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\UrlRuleInterface;
use yii\web\View;

/**
 * Class MenuManager
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuManager extends Component implements UrlRuleInterface
{
    public $cache = 'cache';

    /**
     * @var MenuItem
     */
    private $_menu;
    private $_maps = [];
    private $_activeMenuIds = [];
    private $_routers = [];
    /**
     * @var MenuUrlRuleCreate[]
     */
    private $_createUrlRules = [];
    /**
     * @var MenuUrlRuleParse[]
     */
    private $_parseUrlRules = [];

    public function init()
    {
        if ($this->cache) {
            /** @var Cache $cache */
            $this->cache = Instance::ensure($this->cache, Cache::className());
            $cacheKey = __CLASS__;
            if ((list($createUrlRules, $parseUrlRules) = $this->cache->get($cacheKey)) === false) {
                $this->buildRules();
                $this->cache->set($cacheKey, [$this->_createUrlRules, $this->_parseUrlRules]);
            } else {
                $this->_createUrlRules = $createUrlRules;
                $this->_parseUrlRules = $parseUrlRules;
            }
        } else {
            $this->buildRules();
        }
    }

    protected function buildRules()
    {
        //нам нужно собрать все роутеры от модулей и вытащить из них инструкции по маршрутизации
        $routers = ModuleQuery::instance()->implement('\gromver\platform\basic\interfaces\module\MenuRouterInterface')->fetch('getMenuRouter');
        // вытаскиваем инструкции из всех роутеров
        foreach ($routers as $routerClass) {
            $router = $this->getRouter($routerClass);

            foreach ($router->createUrlRules() as $rule) {
                @$rule['class'] or $rule['class'] = MenuUrlRuleCreate::className();
                $rule['router'] = $router->className();
                $this->_createUrlRules[] = Yii::createObject($rule);
            }

            foreach ($router->parseUrlRules() as $rule) {
                @$rule['class'] or $rule['class'] = MenuUrlRuleParse::className();
                $rule['router'] = $router->className();
                $this->_parseUrlRules[] = Yii::createObject($rule);
            }
        }
    }

    /**
     * @param $router string|array|MenuRouter
     * @return MenuRouter
     * @throws InvalidConfigException
     */
    public function getRouter($router)
    {
        if (is_string($router) && isset($this->_routers[$router])) {
            return $this->_routers[$router];
        }

        if (!is_object($router)) {
            $router = Yii::createObject($router);
        }

        if (!$router instanceof MenuRouter) {
            throw new InvalidConfigException('MenuItemRoutes must be an instance of \gromver\platform\basic\components\MenuRouter class.');
        }

        return $this->_routers[$router->className()] = $router;
    }

    /**
     * @param null $language
     * @return MenuMap
     */
    public function getMenuMap($language = null)
    {
        $language or $language = Yii::$app->language;
        if(!isset($this->_maps[$language]))
            $this->_maps[$language] = Yii::createObject([
                'class' => MenuMap::className(),
                'language' => $language,
            ]);

        return $this->_maps[$language];
    }

    public function getActiveMenu()
    {
        return $this->_menu;
    }

    public function getActiveMenuIds()
    {
        return $this->_activeMenuIds;
    }

    /**
     * Parses the given request and returns the corresponding route and parameters.
     * @param \yii\web\UrlManager $manager the URL manager
     * @param Request $request the request component
     * @return array|bool the parsing result. The route and the parameters are returned as an array.
     * @throws ForbiddenHttpException
     */
    public function parseRequest($manager, $request)
    {
        if (!($pathInfo = $request->getPathInfo() or $pathInfo = $this->getMenuMap()->getMainPagePath())) {
            return false;
        }
        //Пункт 2
        $this->_activeMenuIds = array_keys($this->getMenuMap()->getLinks(), $request->getUrl());

        if ($this->_menu = $this->getMenuMap()->getMenuByPath($pathInfo)) {
            //Пункт 1.1
            $this->_activeMenuIds[] = $this->_menu->id;
            $this->_menu->setContext(MenuItem::CONTEXT_PROPER);
            //при полном совпадении метаданные меню перекрывают метаднные контроллера
            Yii::$app->getView()->on(View::EVENT_BEGIN_PAGE, [$this, 'applyMetaData']);
        } elseif($this->_menu = $this->getMenuMap()->getApplicableMenuByPath($pathInfo)) {
            //Пункт 1.2
            $this->_activeMenuIds[] = $this->_menu->id;
            $this->_menu->setContext(MenuItem::CONTEXT_APPLICABLE);
            $this->applyMetaData();
        } else
            return false;

        // устанавливаем макет приложению
        Event::on(Controller::className(), Controller::EVENT_BEFORE_ACTION, [$this, 'applyLayout']);

        //Проверка на доступ к пунтку меню
        if (!empty($this->_menu->access_rule) && !Yii::$app->user->can($this->_menu->access_rule)) {
            if (Yii::$app->user->getIsGuest()) {
                Yii::$app->user->loginRequired();
            } else {
                throw new ForbiddenHttpException(Yii::t('gromver.platform', 'You have no rights for access to this section of the site.'));
            }
        }

        if ($this->_menu->getContext() === MenuItem::CONTEXT_PROPER) {
            return $this->_menu->parseUrl();
        } else {
            $requestRoute = substr($pathInfo, mb_strlen($this->_menu->path) + 1);
            list($menuRoute, $menuParams) = $this->_menu->parseUrl();
            $requestInfo = new MenuRequest([
                'menuMap' => $this->getMenuMap(),
                'menuRoute' => $menuRoute,
                'menuParams' => $menuParams,
                'requestRoute' => $requestRoute,
                'requestParams' => $request->getQueryParams()
            ]);

            foreach ($this->_parseUrlRules as $rule) {
                if ($result = $rule->process($requestInfo, $this)) {
                    return $result;
                }
            }

            return false;
        }
    }

    /**
     * Creates a URL according to the given route and parameters.
     * @param UrlManager $manager the URL manager
     * @param string $route the route. It should not have slashes at the beginning or the end.
     * @param array $params the parameters
     * @return string|boolean the created URL, or false if this rule cannot be used for creating this URL.
     */
    public function createUrl($manager, $route, $params)
    {
        $language = $manager->getLanguageContext();

        if ($path = $this->getMenuMap($language)->getMenuPathByRoute(MenuItem::toRoute($route, $params))) {
            return $path;
        }

        $requestInfo = new MenuRequest([
            'menuMap' => $this->getMenuMap($language),
            'requestRoute' => $route,
            'requestParams' => $params,
        ]);

        foreach ($this->_createUrlRules as $rule) {
            if ($result = $rule->process($requestInfo, $this)) {
                return $result;
            }
        }

        return false;
    }

    public function applyMetaData()
    {
        if ($this->_menu->metakey) {
            Yii::$app->getView()->registerMetaTag(['name' => 'keywords', 'content' => $this->_menu->metakey], 'keywords');
        }
        if ($this->_menu->metadesc) {
            Yii::$app->getView()->registerMetaTag(['name' => 'description', 'content' => $this->_menu->metadesc], 'description');
        }
        if ($this->_menu->robots) {
            Yii::$app->getView()->registerMetaTag(['name' => 'robots', 'content' => $this->_menu->robots], 'robots');
        }
    }

    public function applyLayout()
    {
        if ($this->_menu->layout_path) {
            // если в пункте меню установлен шаблон приложения, применяем его
            Yii::$app->controller->layout = $this->_menu->layout_path;
        }
    }
}