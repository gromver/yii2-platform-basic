<?php

class m140820_085040_grom_rbac extends \yii\db\Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        // add "createPermission" permission
        $createPermission = $auth->createPermission('create');
        $createPermission->description = 'create';
        $auth->add($createPermission);

        // add "readPermission" permission
        $readPermission = $auth->createPermission('read');
        $readPermission->description = 'read';
        $auth->add($readPermission);

        // add "updatePermission" permission
        $updatePermission = $auth->createPermission('update');
        $updatePermission->description = 'update';
        $auth->add($updatePermission);

        // add "deletePermission" permission
        $deletePermission = $auth->createPermission('delete');
        $deletePermission->description = 'delete';
        $auth->add($deletePermission);

        // add "userPermission" permission
        $administratePermission = $auth->createPermission('administrate');
        $administratePermission->description = 'administrate';
        $auth->add($administratePermission);

        // add the rule
        $authorizedRule = new \gromver\platform\basic\rules\AuthorizedRule();
        $auth->add($authorizedRule);

        //add "Authorized" role
        $authorized = $auth->createRole('Authorized');
        $authorized->ruleName = $authorizedRule->name;
        $auth->add($authorized);

        // add "Reader" role
        $reader = $auth->createRole('Reader');
        $auth->add($reader);
        $auth->addChild($reader, $readPermission);

        // add "Author" role
        $author = $auth->createRole('Author');
        $auth->add($author);
        $auth->addChild($author, $createPermission);
        $auth->addChild($author, $updatePermission);
        $auth->addChild($author, $reader);

        // add "Administrator" role
        $admin = $auth->createRole('Administrator');
        $auth->add($admin);
        $auth->addChild($admin, $deletePermission);
        $auth->addChild($admin, $administratePermission);
        $auth->addChild($admin, $author);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        $auth->remove($auth->getRole('Reader'));
        $auth->remove($auth->getRole('Author'));
        $auth->remove($auth->getRole('Administrator'));
        $auth->remove($auth->getRole('Authorized'));
        $auth->remove($auth->getPermission('create'));
        $auth->remove($auth->getPermission('read'));
        $auth->remove($auth->getPermission('update'));
        $auth->remove($auth->getPermission('delete'));
        $auth->remove($auth->getPermission('administrate'));
        $auth->remove($auth->getRule('isAuthorized'));
    }
}
