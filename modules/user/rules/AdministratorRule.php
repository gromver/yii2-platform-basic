<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\user\rules;


use gromver\platform\basic\modules\user\models\User;
use yii\rbac\Item;
use Yii;

/**
 * Доступ ко всем аккаунтам кроме суперадминских и админских
 * Class AdministratorRule
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class AdministratorRule extends \yii\rbac\Rule
{
    public $name = 'isUserAdministrator';

    /**
     * Право изменять/авторизовываться/отправлять в корзину/удалять пользователей,
     * обладатель данного права имеет доступ:
     * - к своему аккаунту
     * - к любому пользователю не имеющему данного права
     * обладатель данного права не имеет доступ:
     * - к аккаунтам суперадминов
     *
     * @param string|integer $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        /** @var User $model */
        $model = @$params['user'];

        return isset($model) ? !($model->getIsSuperAdmin() || ($model->id != $user && $model->can('administrateUser'))) : true;
    }
} 