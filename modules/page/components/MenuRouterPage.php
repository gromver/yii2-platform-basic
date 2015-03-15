<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\page\components;


use gromver\platform\basic\modules\menu\models\MenuItem;
use gromver\platform\basic\modules\page\models\Page;

/**
 * Class MenuRouterPage
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuRouterPage extends \gromver\platform\basic\components\MenuRouter
{
    public function parseUrlRules()
    {
        return [
            [
                'menuRoute' => 'grom/page/frontend/default/guide',
                'handler' => 'parsePageGuide'
            ],
        ];
    }

    /**
     * @return array
     */
    public function createUrlRules()
    {
        return [
            [
                'requestRoute' => 'grom/page/frontend/default/view',
                'requestParams' => ['id'],
                'handler' => 'createPageView'
            ],
            [
                'requestRoute' => 'grom/page/frontend/default/guide',
                'requestParams' => ['id'],
                'handler' => 'createPageGuide'
            ],
        ];
    }

    public function parsePageGuide($requestInfo)
    {
        if ($menuPage = Page::findOne($requestInfo->menuParams['id'])) {
            if ($page = Page::findOne([
                'path' => $menuPage->path . '/' . $requestInfo->requestRoute,
                'language' => $menuPage->language
            ])) {
                return ['grom/page/frontend/default/guide', ['id' => $page->id]];
            }
        }
    }

    public function createPageView($requestInfo)
    {
        //пытаемся найти пункт меню ссылющийся на данный пост
        if ($path = $requestInfo->menuMap->getMenuPathByRoute(MenuItem::toRoute('grom/page/frontend/default/view', ['id' => $requestInfo->requestParams['id']]))) {
            unset($requestInfo->requestParams['id'], $requestInfo->requestParams['alias']);
            return MenuItem::toRoute($path, $requestInfo->requestParams);
        }

        return $this->createPageGuide($requestInfo);
    }

    public function createPageGuide($requestInfo)
    {
        //ищем пункт меню ссылающийся на категорию данного поста либо ее предков
        if (isset($requestInfo->requestParams['alias'])) {
            //можем привязаться к пункту меню ссылающемуся на категорию новостей к которой принадлежит данный пост(напрямую либо косвенно)
            if ($path = $this->findPageMenuPath($requestInfo->requestParams['id'], $requestInfo->menuMap)) {
                unset($requestInfo->requestParams['id'], $requestInfo->requestParams['alias']);
                return MenuItem::toRoute($path, $requestInfo->requestParams);
            }
        }
    }

    private $_pagePaths = [];

    /**
     * Находит путь к пункту меню ссылающемуся на категорию $categoryId, либо ее предка
     * Если путь ведет к предку, то достраиваем путь категории $categoryId
     * @param $pageId
     * @param $menuMap \gromver\platform\basic\components\MenuMap
     * @return null|string
     */
    private function findPageMenuPath($pageId, $menuMap)
    {
        if (!isset($this->_pagePaths[$menuMap->language][$pageId])) {
            if ($path = $menuMap->getMenuPathByRoute(MenuItem::toRoute('grom/page/frontend/default/guide', ['id' => $pageId]))) {
                $this->_pagePaths[$menuMap->language][$pageId] = $path;
            } elseif (($page = Page::findOne($pageId)) && !$page->isRoot() && $path = $this->findPageMenuPath($page->parent_id, $menuMap)) {
                $this->_pagePaths[$menuMap->language][$pageId] = $path . '/' . $page->alias;
            } else {
                $this->_pagePaths[$menuMap->language][$pageId] = false;
            }
        }

        return $this->_pagePaths[$menuMap->language][$pageId];
    }
}