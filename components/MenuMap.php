<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\components;


use gromver\platform\basic\modules\menu\models\MenuItem;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\caching\Cache;

/**
 * Class MenuMap
 * Хелпер для работы с пунктами меню
 * Использует кеширование
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuMap extends \yii\base\Object
{
    const CACHE_KEY = __CLASS__;

    public $language;
    /**
     * @var Cache|string
     */
    public $cache;
    /**
     * @var integer
     */
    public $cacheDuration;
    /**
     * @var \yii\caching\Dependency
     */
    public $cacheDependency;

    private $_routes = [];
    private $_paths = [];
    private $_links = [];
    private $_mainPagePath;

    public function init()
    {
        if (!$this->language) {
            throw new InvalidConfigException(get_called_class().'::language must be set.');
        }

        if ($this->cache) {
            /** @var Cache $cache */
            $this->cache = Instance::ensure($this->cache, Cache::className());
            $cacheKey = $this->language . self::CACHE_KEY;
            if ((list($paths, $routes, $links, $mainPagePath) = $this->cache->get($cacheKey)) === false) {
                $this->createMap();
                $this->cache->set($cacheKey, [$this->_paths, $this->_routes, $this->_links, $this->_mainPagePath], $this->cacheDuration, $this->cacheDependency);
            } else {
                $this->_paths = $paths;
                $this->_routes = $routes;
                $this->_links = $links;
                $this->_mainPagePath = $mainPagePath;
            }
        } else {
            $this->createMap();
        }
    }

    private function createMap()
    {
        $items = MenuItem::find()->published()->language($this->language)->orderBy('link_weight ASC')->asArray()->all();

        foreach ($items as $item) {
            if ($item['link_type'] == MenuItem::LINK_ROUTE) {
                $this->_paths[$item['id']] = $item['path'];
                $this->_routes[$item['id']] = $item['link'];
                if ($item['status'] == MenuItem::STATUS_MAIN_PAGE) {
                    $this->_mainPagePath = $item['path'];
                }
            } else {
                $this->_links[$item['id']] = $item['link'];
            }

        }
    }

    public function getMainPagePath()
    {
        return $this->_mainPagePath;
    }
    /**
     * @param $path
     * @return MenuItem
     */
    public function getMenuByPath($path)
    {
        $menuId = array_search($path, $this->_paths);
        return $menuId ? MenuItem::findOne($menuId) : null;
    }

    /**
     * @param $route
     * @return MenuItem
     */
    public function getMenuByRoute($route)
    {
        $menuId = array_search($route, $this->_routes);
        return $menuId ? MenuItem::findOne($menuId) : null;
    }

    /**
     * @param $route
     * @return string
     */
    public function getMenuPathByRoute($route)
    {
        $menuId = array_search($route, $this->_routes);
        return $menuId ? $this->_paths[$menuId] : null;
    }

    /**
     * @param $menuId integer
     * @return string|null
     */
    public function getMenuPathById($menuId)
    {
        return @$this->_paths[$menuId];
    }

    /**
     * @param $link
     * @return MenuItem
     */
    public function getMenuByLink($link)
    {
        $menuId = array_search($link, $this->_links);
        return $menuId ? MenuItem::findOne($menuId) : null;
    }


    /**
     * @param $path
     * @return MenuItem
     */
    public function getApplicableMenuByPath($path)
    {
        $segments = explode('/', $path);
        $menu = null;
        do {
            array_pop($segments);
        } while(count($segments) && !$menu=$this->getMenuByPath(implode('/', $segments)));

        return $menu;
    }

    /**
     * @param $path
     * @return MenuItem
     */
    public function getMenuByPathRecursive($path)
    {
        $segments = explode('/', $path);
        $menu = null;
        while(count($segments) && !$menu=$this->getMenuByPath(implode('/', $segments))) {
            array_pop($segments);
        }
        return $menu;
    }

    public function getLinks()
    {
        return $this->_links;
    }

    public function getPaths()
    {
        return $this->_paths;
    }

    public function getRoutes()
    {
        return $this->_routes;
    }
}