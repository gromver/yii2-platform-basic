<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\search\modules\sql\components;


use gromver\platform\basic\modules\menu\models\MenuItem;

/**
 * Class MenuRouterSearch
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuRouterSearch extends \gromver\platform\basic\components\MenuRouter {
    /**
     * @return array
     */
    public function createUrlRules()
    {
        return [
            [
                'requestRoute' => 'grom/search/default/index',
                'handler' => 'createSearch'
            ],
        ];
    }

    public function createSearch($requestInfo)
    {
        if($path = $requestInfo->menuMap->getMenuPathByRoute('grom/search/default/index')) {
            return MenuItem::toRoute($path, $requestInfo->requestParams);
        }
    }
}