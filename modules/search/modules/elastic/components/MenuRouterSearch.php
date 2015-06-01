<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\search\modules\elastic\components;


use gromver\platform\basic\modules\menu\models\MenuItem;
use gromver\platform\basic\components\MenuRequestInfo;

/**
 * Class MenuRouterSearch
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuRouterSearch extends \gromver\platform\basic\components\MenuRouter
{
    /**
     * @inheritdoc
     */
    public function createUrlRules()
    {
        return [
            [
                'requestRoute' => 'grom/search/elastic/frontend/default/index',
                'handler' => 'createSearch'
            ],
        ];
    }

    /**
     * @param $requestInfo MenuRequestInfo;
     * @return mixed|null|string
     */
    public function createSearch($requestInfo)
    {
        if($path = $requestInfo->menuMap->getMenuPathByRoute('grom/search/elastic/frontend/default/index')) {
            return MenuItem::toRoute($path, $requestInfo->requestParams);
        }
    }
}