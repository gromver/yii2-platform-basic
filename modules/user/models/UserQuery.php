<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\user\models;


use yii\db\ActiveQuery;

/**
 * Class UserQuery
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class UserQuery extends ActiveQuery {
    /**
     * Все элементы не отправленные в корзину
     * @return static
     */
    public function published()
    {
        return $this->andWhere(['deleted_at' => null]);
    }

    /**
     * Элементы отправленные в корзину
     * @return static
     */
    public function trashed()
    {
        return $this->andWhere(['not', ['deleted_at' => null]]);
    }

    /**
     * Активные пользователи
     * @return static
     */
    public function active()
    {
        return $this->andWhere(['status' => User::STATUS_ACTIVE, 'deleted_at' => null]);
    }

    /**
     * Неактивные пользователи
     * @return static
     */
    public function inactive()
    {
        return $this->andWhere(['status' => User::STATUS_INACTIVE, 'deleted_at' => null]);
    }

    /**
     * Пользователи ожидающие активации
     * @return static
     */
    public function suspended()
    {
        return $this->andWhere(['status' => User::STATUS_SUSPENDED, 'deleted_at' => null]);
    }
}