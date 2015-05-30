<?php

class m000007_000001_grom_news_post_rbac extends \yii\db\Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        // add the rule
        $authorRule = new \gromver\platform\basic\modules\news\rules\PostAuthorRule();
        $auth->add($authorRule);

        // право на создание элемента (create)
        $createPermission = $auth->createPermission('createPost');
        $createPermission->description = 'create post';
        $auth->add($createPermission);

        // право на просмотр элемента (view, index)
        $readPermission = $auth->createPermission('readPost');
        $readPermission->description = 'read post';
        $auth->add($readPermission);

        // право на изменение элемента (update)
        $updatePermission = $auth->createPermission('updatePost');
        $updatePermission->description = 'update post';
        $auth->add($updatePermission);

        // право на изменение элемента (update)
        $updateOwnPermission = $auth->createPermission('updateOwnPost');
        $updateOwnPermission->description = 'update own post';
        $updateOwnPermission->ruleName = $authorRule->name;
        $auth->add($updateOwnPermission);
        $auth->addChild($updateOwnPermission, $updatePermission);

        // право удалять элементы (delete)
        $deletePermission = $auth->createPermission('deletePost');
        $deletePermission->description = 'delete post';
        $auth->add($deletePermission);

        // право на изменение элемента (update)
        $deleteOwnPermission = $auth->createPermission('deleteOwnPost');
        $deleteOwnPermission->description = 'delete own post';
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
        $createPermission = $auth->getPermission('createPost');
        $readPermission = $auth->getPermission('readPost');
        $updatePermission = $auth->getPermission('updatePost');
        $updateOwnPermission = $auth->getPermission('updateOwnPost');
        $deletePermission = $auth->getPermission('deletePost');
        $deleteOwnPermission = $auth->getPermission('deleteOwnPost');

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
