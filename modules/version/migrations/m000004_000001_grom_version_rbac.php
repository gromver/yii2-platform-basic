<?php

class m000004_000001_grom_version_rbac extends \yii\db\Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        // право на создание элемента (create)
        $createPermission = $auth->createPermission('createVersion');
        $createPermission->description = 'create version';
        $auth->add($createPermission);

        // право на просмотр элемента (view, index)
        $readPermission = $auth->createPermission('readVersion');
        $readPermission->description = 'read version';
        $auth->add($readPermission);

        // право на изменение элемента (update)
        $updatePermission = $auth->createPermission('updateVersion');
        $updatePermission->description = 'update version';
        $auth->add($updatePermission);

        // право удалять элементы (delete)
        $deletePermission = $auth->createPermission('deleteVersion');
        $deletePermission->description = 'delete version';
        $auth->add($deletePermission);

        // setup administrator role
        $administrator = $auth->getRole('administrator');
        $auth->addChild($administrator, $readPermission);
        $auth->addChild($administrator, $createPermission);
        $auth->addChild($administrator, $updatePermission);
        $auth->addChild($administrator, $deletePermission);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        // roles
        $administrator = $auth->getRole('administrator');

        // permissions
        $createPermission = $auth->getPermission('createVersion');
        $readPermission = $auth->getPermission('readVersion');
        $updatePermission = $auth->getPermission('updateVersion');
        $deletePermission = $auth->getPermission('deleteVersion');

        $auth->removeChild($administrator, $readPermission);
        $auth->removeChild($administrator, $createPermission);
        $auth->removeChild($administrator, $updatePermission);
        $auth->removeChild($administrator, $deletePermission);

        $auth->remove($readPermission);
        $auth->remove($createPermission);
        $auth->remove($updatePermission);
        $auth->remove($deletePermission);
    }
}
