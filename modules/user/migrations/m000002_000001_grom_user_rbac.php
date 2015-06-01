<?php

/**
 * Class m000002_000001_grom_user_rbac
 * суперадмин - может изменять/удалять/восстанавливать любые аккаунты пользователей, кроме аккаунтов суперадминов
 * суперадмин - может изменять свои аккаунт
 * любой суперадмининский аккаунт нельзя ни отправить в корзину ни удалить из бд
 * админы пользователей - могут изменять/удалять/восстанавливать любые аккаунты пользователей, кроме администраторов пользователей и суперадминов
 * админы пользователей - могут изменять свой аккаунт
 */
class m000002_000001_grom_user_rbac extends \yii\db\Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        // add the admin rule
        $administratorRule = new \gromver\platform\basic\modules\user\rules\AdministratorRule();
        $auth->add($administratorRule);

        $administratePermission = $auth->createPermission('administrateUser');
        $administratePermission->description = 'administrate user';
        $administratePermission->ruleName = $administratorRule->name;
        $auth->add($administratePermission);

        // право на создание элемента (create)
        $createPermission = $auth->createPermission('createUser');
        $createPermission->description = 'create user';
        $auth->add($createPermission);

        // право на просмотр элемента (index)
        $readPermission = $auth->createPermission('readUser');
        $readPermission->description = 'read user';
        $auth->add($readPermission);

        // право на изменение элемента (update, params, view)
        $updatePermission = $auth->createPermission('updateUser');
        $updatePermission->description = 'update user';
        $auth->add($updatePermission);
        $auth->addChild($updatePermission, $administratePermission);

        // право удалять элементы из БД (delete, bulk-delete)
        $deletePermission = $auth->createPermission('deleteUser');
        $deletePermission->description = 'delete user';
        $auth->add($deletePermission);
        $auth->addChild($deletePermission, $administratePermission);

        // право отправлять элементы в корзину (trash, bulk-trash)
        $trashPermission = $auth->createPermission('trashUser');
        $trashPermission->description = 'trash user';
        $auth->add($trashPermission);
        $auth->addChild($trashPermission, $administratePermission);

        // право авторизироваться под выбранным пользователем (login-as)
        $loginPermission = $auth->createPermission('loginAsUser');
        $loginPermission->description = 'login as user';
        $auth->add($loginPermission);
        $auth->addChild($loginPermission, $administratePermission);

        // setup administrator role
        $administrator = $auth->getRole('administrator');
        $auth->addChild($administrator, $readPermission);
        $auth->addChild($administrator, $createPermission);
        $auth->addChild($administrator, $updatePermission);
        $auth->addChild($administrator, $trashPermission);
        $auth->addChild($administrator, $deletePermission);
        $auth->addChild($administrator, $loginPermission);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        // roles
        $administrator = $auth->getRole('administrator');

        // permissions
        $administratePermission = $auth->getPermission('administrateUser');
        $createPermission = $auth->getPermission('createUser');
        $readPermission = $auth->getPermission('readUser');
        $updatePermission = $auth->getPermission('updateUser');
        $deletePermission = $auth->getPermission('deleteUser');
        $trashPermission = $auth->getPermission('trashUser');
        $loginPermission = $auth->getPermission('loginAsUser');

        $auth->removeChild($administrator, $readPermission);
        $auth->removeChild($administrator, $createPermission);
        $auth->removeChild($administrator, $updatePermission);
        $auth->removeChild($administrator, $trashPermission);
        $auth->removeChild($administrator, $deletePermission);
        $auth->removeChild($administrator, $loginPermission);

        $auth->remove($administratePermission);
        $auth->remove($readPermission);
        $auth->remove($createPermission);
        $auth->remove($updatePermission);
        $auth->remove($deletePermission);
        $auth->remove($trashPermission);
        $auth->remove($loginPermission);
        $auth->remove($auth->getRule('isUserAdministrator'));
    }
}
