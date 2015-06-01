<?php

class m000008_000001_grom_page_rbac extends \yii\db\Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        // add the rule
        $authorRule = new \gromver\platform\basic\modules\page\rules\AuthorRule();
        $auth->add($authorRule);

        // право на создание элемента (create)
        $createPermission = $auth->createPermission('createPage');
        $createPermission->description = 'create page';
        $auth->add($createPermission);

        // право на просмотр элемента (view, index)
        $readPermission = $auth->createPermission('readPage');
        $readPermission->description = 'read page';
        $auth->add($readPermission);

        // право на изменение элемента (update)
        $updatePermission = $auth->createPermission('updatePage');
        $updatePermission->description = 'update page';
        $auth->add($updatePermission);

        // право на изменение элемента (update)
        $updateOwnPermission = $auth->createPermission('updateOwnPage');
        $updateOwnPermission->description = 'update own page';
        $updateOwnPermission->ruleName = $authorRule->name;
        $auth->add($updateOwnPermission);
        $auth->addChild($updateOwnPermission, $updatePermission);

        // право удалять элементы (delete)
        $deletePermission = $auth->createPermission('deletePage');
        $deletePermission->description = 'delete page';
        $auth->add($deletePermission);

        // право на изменение элемента (update)
        $deleteOwnPermission = $auth->createPermission('deleteOwnPage');
        $deleteOwnPermission->description = 'delete own page';
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
        $createPermission = $auth->getPermission('createPage');
        $readPermission = $auth->getPermission('readPage');
        $updatePermission = $auth->getPermission('updatePage');
        $updateOwnPermission = $auth->getPermission('updateOwnPage');
        $deletePermission = $auth->getPermission('deletePage');
        $deleteOwnPermission = $auth->getPermission('deleteOwnPage');

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
        $auth->remove($auth->getRule('isPageAuthor'));
    }
}
