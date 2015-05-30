<?php

class m000006_000001_grom_tag_rbac extends \yii\db\Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        // add the rule
        $authorRule = new \gromver\platform\basic\modules\tag\rules\AuthorRule();
        $auth->add($authorRule);

        // право на создание элемента (create)
        $createPermission = $auth->createPermission('createTag');
        $createPermission->description = 'create tag';
        $auth->add($createPermission);

        // право на просмотр элемента (view, index)
        $readPermission = $auth->createPermission('readTag');
        $readPermission->description = 'read tag';
        $auth->add($readPermission);

        // право на изменение элемента (update)
        $updatePermission = $auth->createPermission('updateTag');
        $updatePermission->description = 'update tag';
        $auth->add($updatePermission);

        // право на изменение элемента (update)
        $updateOwnPermission = $auth->createPermission('updateOwnTag');
        $updateOwnPermission->description = 'update own tag';
        $updateOwnPermission->ruleName = $authorRule->name;
        $auth->add($updateOwnPermission);
        $auth->addChild($updateOwnPermission, $updatePermission);

        // право удалять элементы (delete)
        $deletePermission = $auth->createPermission('deleteTag');
        $deletePermission->description = 'delete tag';
        $auth->add($deletePermission);

        // право на изменение элемента (update)
        $deleteOwnPermission = $auth->createPermission('deleteOwnTag');
        $deleteOwnPermission->description = 'delete own tag';
        $deleteOwnPermission->ruleName = $authorRule->name;
        $auth->add($deleteOwnPermission);
        $auth->addChild($deleteOwnPermission, $deletePermission);

        // setup reader role
        $reader = $auth->getRole('reader');
        $auth->addChild($reader, $readPermission);

        // setup author role
        $author = $auth->getRole('author');
        $auth->addChild($author, $createPermission);
        $auth->addChild($author, $updateOwnPermission);
        $auth->addChild($author, $deleteOwnPermission);

        // setup editor role
        $editor = $auth->getRole('editor');
        $auth->addChild($editor, $updatePermission);
        $auth->addChild($editor, $deletePermission);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        // roles
        $reader = $auth->getRole('reader');
        $author = $auth->getRole('author');
        $editor = $auth->getRole('editor');

        // permissions
        $createPermission = $auth->getPermission('createTag');
        $readPermission = $auth->getPermission('readTag');
        $updatePermission = $auth->getPermission('updateTag');
        $updateOwnPermission = $auth->getPermission('updateOwnTag');
        $deletePermission = $auth->getPermission('deleteTag');
        $deleteOwnPermission = $auth->getPermission('deleteOwnTag');

        $auth->removeChild($reader, $readPermission);
        $auth->removeChild($author, $createPermission);
        $auth->removeChild($author, $updateOwnPermission);
        $auth->removeChild($author, $deleteOwnPermission);
        $auth->removeChild($editor, $updatePermission);
        $auth->removeChild($author, $deletePermission);

        $auth->remove($updateOwnPermission);
        $auth->remove($deleteOwnPermission);
        $auth->remove($readPermission);
        $auth->remove($createPermission);
        $auth->remove($updatePermission);
        $auth->remove($deletePermission);
    }
}
