<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\console\components;


use yii\web\IdentityInterface;

/**
 * Class User
 * Фейковый компонент для консольных команд, авторизирует первого пользователя из БД
 *
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class User extends \gromver\platform\basic\components\User {
    public function init()
    {
        parent::init();

        /* @var $class \gromver\platform\basic\modules\user\models\User */
        $class = $this->identityClass;
        /* @var $identity IdentityInterface */
        $identity = $class::find()->orderBy(['id' => SORT_ASC])->one();

        $this->setIdentity($identity);
    }
} 