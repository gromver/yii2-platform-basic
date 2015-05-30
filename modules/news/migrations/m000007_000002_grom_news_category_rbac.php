<?php

class m000007_000002_grom_news_category_rbac extends \yii\db\Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        // add the rule
        $authorRule = new \gromver\platform\basic\modules\news\rules\CategoryAuthorRule();
        $auth->add($authorRule);

        // право на создание элемента (create)
        $createPermission = $auth->createPermission('createCategory');
        $createPermission->description = 'create category';
        $auth->add($createPermission);

        // право на просмотр элемента (view, index)
        $readPermission = $auth->createPermission('readCategory');
        $readPermission->description = 'read category';
        $auth->add($readPermission);

        // право на изменение элемента (update)
        $updatePermission = $auth->createPermission('updateCategory');
        $updatePermission->description = 'update category';
        $auth->add($updatePermission);

        // право на изменение элемента (update)
        $updateOwnPermission = $auth->createPermission('updateOwnCategory');
        $updateOwnPermission->description = 'update own category';
        $updateOwnPermission->ruleName = $authorRule->name;
        $auth->add($updateOwnPermission);
        $auth->addChild($updateOwnPermission, $updatePermission);

        // право удалять элементы (delete)
        $deletePermission = $auth->createPermission('deleteCategory');
        $deletePermission->description = 'delete category';
        $auth->add($deletePermission);

        // право на изменение элемента (update)
        $deleteOwnPermission = $auth->createPermission('deleteOwnCategory');
        $deleteOwnPermission->description = 'delete own category';
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
        $createPermission = $auth->getPermission('createCategory');
        $readPermission = $auth->getPermission('readCategory');
        $updatePermission = $auth->getPermission('updateCategory');
        $updateOwnPermission = $auth->getPermission('updateOwnCategory');
        $deletePermission = $auth->getPermission('deleteCategory');
        $deleteOwnPermission = $auth->getPermission('deleteOwnCategory');

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
