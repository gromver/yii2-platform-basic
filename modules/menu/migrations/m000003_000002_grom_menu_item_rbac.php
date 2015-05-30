<?php

class m000003_000002_grom_menu_item_rbac extends \yii\db\Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        // право на создание элемента (create)
        $createPermission = $auth->createPermission('createMenuItem');
        $createPermission->description = 'create menu item';
        $auth->add($createPermission);

        // право на просмотр элемента (view, index)
        $readPermission = $auth->createPermission('readMenuItem');
        $readPermission->description = 'read menu item';
        $auth->add($readPermission);

        // право на изменение элемента (update)
        $updatePermission = $auth->createPermission('updateMenuItem');
        $updatePermission->description = 'update menu item';
        $auth->add($updatePermission);

        // право удалять элементы (delete)
        $deletePermission = $auth->createPermission('deleteMenuItem');
        $deletePermission->description = 'delete menu item';
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
        $createPermission = $auth->getPermission('createMenuItem');
        $readPermission = $auth->getPermission('readMenuItem');
        $updatePermission = $auth->getPermission('updateMenuItem');
        $deletePermission = $auth->getPermission('deleteMenuItem');

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
