<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\menu\models;


use creocoder\nestedsets\NestedSetsQueryBehavior;
use yii\db\Query;

/**
 * Class MenuItemQuery
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuItemQuery extends \yii\db\ActiveQuery
{
    public function behaviors() {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }

    /**
     * @param $typeId
     * @return static
     */
    public function type($typeId)
    {
        return $this->andWhere(['{{%grom_menu_item}}.menu_type_id' => $typeId]);
    }
    /**
     * @return static
     */
    public function published()
    {
        $badcatsQuery = new Query([
            'select' => ['badcats.id'],
            'from' => ['{{%grom_menu_item}} AS unpublished'],
            'join' => [
                ['LEFT JOIN', '{{%grom_menu_item}} AS badcats', 'unpublished.lft <= badcats.lft AND unpublished.rgt >= badcats.rgt']
            ],
            'where' => 'unpublished.status = ' . MenuItem::STATUS_UNPUBLISHED,
            'groupBy' => ['badcats.id']
        ]);

        return $this->andWhere(['NOT IN', '{{%grom_menu_item}}.id', $badcatsQuery]);
    }

    /**
     * @param $language
     * @return static
     */
    public function language($language)
    {
        return $this->andFilterWhere(['{{%grom_menu_item}}.language' => $language]);
    }

    /**
     * @return static
     */
    public function noRoots()
    {
        return $this->andWhere('{{%grom_menu_item}}.lft!=1');
    }
}