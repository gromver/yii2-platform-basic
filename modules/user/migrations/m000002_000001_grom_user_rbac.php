<?php

class m000002_000001_grom_user_rbac extends \yii\db\Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        // право на создание элемента (create)
        $createPermission = $auth->createPermission('createUser');
        $createPermission->description = 'create user';
        $auth->add($createPermission);

        // право на просмотр элемента (view, index)
        $readPermission = $auth->createPermission('readUser');
        $readPermission->description = 'read user';
        $auth->add($readPermission);

        // право на изменение элемента (update)
        $updatePermission = $auth->createPermission('updateUser');
        $updatePermission->description = 'update user';
        $auth->add($updatePermission);

        // право удалять элементы из БД (delete)
        $deletePermission = $auth->createPermission('deleteUser');
        $deletePermission->description = 'delete user';
        $auth->add($deletePermission);

        // право отправлять элементы в корзину (trash)
        $trashPermission = $auth->createPermission('trashUser');
        $trashPermission->description = 'trash user';
        $auth->add($trashPermission);

        // право авторизироваться под выбранным пользователем (login)
        $loginPermission = $auth->createPermission('loginAsUser');
        $loginPermission->description = 'login as user';
        $auth->add($loginPermission);

        // setup administrator role
        $administrator = $auth->getRole('administrator');
        $auth->addChild($administrator, $readPermission);
        $auth->addChild($administrator, $createPermission);
        $auth->addChild($administrator, $updatePermission);
        $auth->addChild($administrator, $trashPermission);
        $auth->addChild($administrator, $loginPermission);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        // roles
        $administrator = $auth->getRole('administrator');

        // permissions
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
        $auth->removeChild($administrator, $loginPermission);

        $auth->remove($readPermission);
        $auth->remove($createPermission);
        $auth->remove($updatePermission);
        $auth->remove($deletePermission);
        $auth->remove($trashPermission);
        $auth->remove($loginPermission);
    }
}
