<?php

class m000005_000001_grom_widget_rbac extends \yii\db\Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        // право на создание элемента (create)
        $createPermission = $auth->createPermission('createWidget');
        $createPermission->description = 'create widget';
        $auth->add($createPermission);

        // право на просмотр элемента (view, index)
        $readPermission = $auth->createPermission('readWidget');
        $readPermission->description = 'read widget';
        $auth->add($readPermission);

        // право на изменение элемента (update)
        $updatePermission = $auth->createPermission('updateWidget');
        $updatePermission->description = 'update widget';
        $auth->add($updatePermission);

        // право удалять элементы (delete)
        $deletePermission = $auth->createPermission('deleteWidget');
        $deletePermission->description = 'delete widget';
        $auth->add($deletePermission);

        // право настраивать виджеты (customize)
        $customizePermission = $auth->createPermission('customizeWidget');
        $customizePermission->description = 'customize widget';
        $auth->add($customizePermission);


        // setup administrator role
        $administrator = $auth->getRole('administrator');
        $auth->addChild($administrator, $readPermission);
        $auth->addChild($administrator, $createPermission);
        $auth->addChild($administrator, $updatePermission);
        $auth->addChild($administrator, $deletePermission);
        $auth->addChild($administrator, $customizePermission);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        // roles
        $administrator = $auth->getRole('administrator');

        // permissions
        $createPermission = $auth->getPermission('createWidget');
        $readPermission = $auth->getPermission('readWidget');
        $updatePermission = $auth->getPermission('updateWidget');
        $deletePermission = $auth->getPermission('deleteWidget');
        $customizePermission = $auth->getPermission('customizeWidget');

        $auth->removeChild($administrator, $readPermission);
        $auth->removeChild($administrator, $createPermission);
        $auth->removeChild($administrator, $updatePermission);
        $auth->removeChild($administrator, $deletePermission);
        $auth->removeChild($administrator, $customizePermission);

        $auth->remove($readPermission);
        $auth->remove($createPermission);
        $auth->remove($updatePermission);
        $auth->remove($deletePermission);
        $auth->remove($customizePermission);
    }
}
