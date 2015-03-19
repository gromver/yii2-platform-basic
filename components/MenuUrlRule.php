<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\components;


//use gromver\modulequery\ModuleEvent;
use gromver\modulequery\ModuleQuery;
//use gromver\modulequery\Value;
use gromver\platform\basic\modules\menu\models\MenuItem;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\base\Object;
use yii\di\Instance;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\UrlRuleInterface;
use yii\caching\Cache;
use Yii;
use yii\web\View;

/**
 * Class MenuUrlRule
 * Маршрутизация меню
 * для маршрутизации используется объекты MenuRouter с правилами маршрутизации,
 * MenuRouter* объект для приложения может расшарить любой модуль, через \gromver\platform\basic\interfaces\module\MenuRouterInterface
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuUrlRule extends Object implements UrlRuleInterface
{
    const EVENT_MODULE_ROUTERS = 'MenuUrlRuleModuleRouters';

    public $cache = 'cache';
    public $cacheDuration;
    public $cacheDependency;
    /**
     * @var MenuManager
     */
    public  $menuManager;

    private $_routers = [];
    /**
     * @var MenuRouterUrlRuleCreate[]
     */
    private $_createUrlRules = [];
    /**
     * @var MenuRouterUrlRuleParse[]
     */
    private $_parseUrlRules = [];

    public function init()
    {
        Instance::ensure($this->menuManager, MenuManager::className());

        if ($this->cache) {
            /** @var Cache $cache */
            $this->cache = Instance::ensure($this->cache, Cache::className());
            $cacheKey = __CLASS__;
            if ((list($createUrlRules, $parseUrlRules) = $this->cache->get($cacheKey)) === false) {
                $this->buildRules();
                $this->cache->set($cacheKey, [$this->_createUrlRules, $this->_parseUrlRules], $this->cacheDuration, $this->cacheDependency);
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
        //$routers = new Value([]);
        //ModuleEvent::trigger(self::EVENT_MODULE_ROUTERS, [$routers]);
        // вытаскиваем инструкции из всех роутеров
        foreach ($routers as $routerClass) {
            $router = $this->getRouter($routerClass);

            foreach ($router->createUrlRules() as $rule) {
                @$rule['class'] or $rule['class'] = MenuRouterUrlRuleCreate::className();
                $rule['router'] = $router->className();
                $this->_createUrlRules[] = Yii::createObject($rule);
            }

            foreach ($router->parseUrlRules() as $rule) {
                @$rule['class'] or $rule['class'] = MenuRouterUrlRuleParse::className();
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
     * Parses the given request and returns the corresponding route and parameters.
     * @param \yii\web\UrlManager $manager the URL manager
     * @param Request $request the request component
     * @return array|bool the parsing result. The route and the parameters are returned as an array.
     * @throws ForbiddenHttpException
     */
    public function parseRequest($manager, $request)
    {
        if (!($pathInfo = $request->getPathInfo() or $pathInfo = $this->menuManager->getMenuMap()->getMainPagePath())) {
            return false;
        }
        // помечаем как активные все пункты меню которые ссылаются на тотже урл что в запросе
        $this->menuManager->setActiveMenuIds(array_keys($this->menuManager->getMenuMap()->getLinks(), $request->getUrl()));

        // ищем пункт меню, чей путь совпадает с путем в запросе
        if ($menu = $this->menuManager->getMenuMap()->getMenuByPath($pathInfo)) {
            // устанавливаем найденный пункт меню в качестве активного
            $this->menuManager->setActiveMenu($menu);
            // добавляем данный пункт в список активных пунктов меню
            $this->menuManager->getActiveMenuIds()[] = $menu->id;
            // помечаем пункт меню как "правильный"
            $menu->setContext(MenuItem::CONTEXT_PROPER);
            //при полном совпадении метаданные меню перекрывают метаднные контроллера
            Yii::$app->getView()->on(View::EVENT_BEGIN_PAGE, [$this->menuManager, 'applyMetaData']);
        }
        // ищем пункт меню который имеет в пути общее начало с путем из запроса
        elseif($menu = $this->menuManager->getMenuMap()->getApplicableMenuByPath($pathInfo)) {
            // устанавливаем найденный пункт меню в качестве активного
            $this->menuManager->setActiveMenu($menu);
            // добавляем данный пункт в список активных пунктов меню
            $this->menuManager->getActiveMenuIds()[] = $menu->id;
            // помечаем пункт меню как "подходящий"
            $menu->setContext(MenuItem::CONTEXT_APPLICABLE);
            $this->menuManager->applyMetaData();
        } else {
            return false;
        }

        // устанавливаем макет приложению
        Event::on(Controller::className(), Controller::EVENT_BEFORE_ACTION, [$this->menuManager, 'applyLayout']);

        // Проверка на доступ к пунтку меню
        if (!empty($menu->access_rule) && !Yii::$app->user->can($menu->access_rule)) {
            if (Yii::$app->user->getIsGuest()) {
                Yii::$app->user->loginRequired();
            } else {
                throw new ForbiddenHttpException(Yii::t('gromver.platform', 'You have no rights for access to this section of the site.'));
            }
        }

        if ($menu->getContext() === MenuItem::CONTEXT_PROPER) {
            return $menu->parseUrl();
        } else {
            $requestRoute = substr($pathInfo, mb_strlen($menu->path) + 1);
            list($menuRoute, $menuParams) = $menu->parseUrl();
            $requestInfo = new MenuRequestInfo([
                'menuMap' => $this->menuManager->getMenuMap(),
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

        if ($path = $this->menuManager->getMenuMap($language)->getMenuPathByRoute(MenuItem::toRoute($route, $params))) {
            return $path;
        }

        $requestInfo = new MenuRequestInfo([
            'menuMap' => $this->menuManager->getMenuMap($language),
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

} 