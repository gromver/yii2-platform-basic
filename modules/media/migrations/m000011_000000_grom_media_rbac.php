<?php

class m000011_000000_grom_media_rbac extends \yii\db\Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        // право на просмотр элемента (view, index)
        $readPermission = $auth->createPermission('readMedia');
        $readPermission->description = 'read media';
        $auth->add($readPermission);

        // право на изменение элемента (update)
        $updatePermission = $auth->createPermission('updateMedia');
        $updatePermission->description = 'update media';
        $auth->add($updatePermission);

        // setup reader role
        $reader = $auth->getRole('reader');
        $auth->addChild($reader, $readPermission);

        // setup author role
        $author = $auth->getRole('author');
        $auth->addChild($author, $updatePermission);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        // roles
        $reader = $auth->getRole('reader');
        $author = $auth->getRole('author');

        // permissions
        $readPermission = $auth->getPermission('readMedia');
        $updatePermission = $auth->getPermission('updateMedia');

        $auth->removeChild($reader, $readPermission);
        $auth->removeChild($author, $updatePermission);

        $auth->remove($readPermission);
        $auth->remove($updatePermission);
    }
}
