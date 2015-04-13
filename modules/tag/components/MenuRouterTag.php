<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\tag\components;


use gromver\platform\basic\modules\menu\models\MenuItem;
use gromver\platform\basic\modules\tag\models\Tag;

/**
 * Class MenuRouterTag
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuRouterTag extends \gromver\platform\basic\components\MenuRouter
{
    /**
     * @inheritdoc
     */
    public function parseUrlRules()
    {
        return [
            [
                'menuRoute' => 'grom/tag/frontend/default/index',
                'handler' => 'parseTagCloud'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function createUrlRules()
    {
        return [
            [
                'requestRoute' => 'grom/tag/frontend/default/view',
                'requestParams' => ['id'],
                'handler' => 'createTagItems'
            ],
        ];
    }

    /**
     * @param \gromver\platform\basic\components\MenuRequestInfo $requestInfo
     * @return array
     */
    public function parseTagCloud($requestInfo)
    {
        if (preg_match('/^\d+$/', $requestInfo->requestRoute)) {
            return ['grom/tag/frontend/default/view', ['id' => $requestInfo->requestRoute]];
        } else {
            return ['grom/tag/frontend/default/view', ['id' => Tag::find()->select('id')->where(['alias' => $requestInfo->requestRoute, 'language' => $requestInfo->menuMap->language])->scalar()]];
        }
    }

    /**
     * @param \gromver\platform\basic\components\MenuRequestInfo $requestInfo
     * @return mixed|null|string
     */
    public function createTagItems($requestInfo)
    {
        if($path = $requestInfo->menuMap->getMenuPathByRoute('grom/tag/frontend/default/index')) {
            $path .= '/' . (isset($requestInfo->requestParams['alias']) ? $requestInfo->requestParams['alias'] : $this->findTagAlias($requestInfo->requestParams['id']));
            unset($requestInfo->requestParams['id'], $requestInfo->requestParams['alias']);
            return MenuItem::toRoute($path, $requestInfo->requestParams);
        }
    }

    private $_tagAliases = [];

    /**
     * @param integer $tagId
     * @return string
     */
    private function findTagAlias($tagId)
    {
        if (!isset($this->_tagAliases[$tagId])) {
            $this->_tagAliases[$tagId] = Tag::find()->select('alias')->where(['id' => $tagId])->scalar();
        }

        return $this->_tagAliases[$tagId];
    }
}