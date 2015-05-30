<?php

class m000003_000001_grom_menu_type_rbac extends \yii\db\Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        // право на создание элемента (create)
        $createPermission = $auth->createPermission('createMenuType');
        $createPermission->description = 'create menu type';
        $auth->add($createPermission);

        // право на просмотр элемента (view, index)
        $readPermission = $auth->createPermission('readMenuType');
        $readPermission->description = 'read menu type';
        $auth->add($readPermission);

        // право на изменение элемента (update)
        $updatePermission = $auth->createPermission('updateMenuType');
        $updatePermission->description = 'update menu type';
        $auth->add($updatePermission);

        // право удалять элементы (delete)
        $deletePermission = $auth->createPermission('deleteMenuType');
        $deletePermission->description = 'delete menu type';
        $auth->add($deletePermission);

        // setup reader role
        $reader = $auth->getRole('reader');
        $auth->addChild($reader, $readPermission);

        // setup editor role
        $editor = $auth->getRole('editor');
        $auth->addChild($editor, $createPermission);
        $auth->addChild($editor, $updatePermission);
        $auth->addChild($editor, $deletePermission);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        // roles
        $reader = $auth->getRole('reader');
        $editor = $auth->getRole('editor');

        // permissions
        $createPermission = $auth->getPermission('createMenuType');
        $readPermission = $auth->getPermission('readMenuType');
        $updatePermission = $auth->getPermission('updateMenuType');
        $deletePermission = $auth->getPermission('deleteMenuType');

        $auth->removeChild($reader, $readPermission);
        $auth->removeChild($editor, $createPermission);
        $auth->removeChild($editor, $updatePermission);
        $auth->removeChild($editor, $deletePermission);

        $auth->remove($readPermission);
        $auth->remove($createPermission);
        $auth->remove($updatePermission);
        $auth->remove($deletePermission);
    }
}
