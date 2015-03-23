<?php

class m140820_085040_grom_rbac extends \yii\db\Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        // право на создание элемента (create)
        $createPermission = $auth->createPermission('create');
        $createPermission->description = 'create';
        $auth->add($createPermission);

        // право на просмотр элемента (view, index)
        $readPermission = $auth->createPermission('read');
        $readPermission->description = 'read';
        $auth->add($readPermission);

        // право на изменение элемента (update)
        $updatePermission = $auth->createPermission('update');
        $updatePermission->description = 'update';
        $auth->add($updatePermission);

        // право удалять элементы (delete)
        $deletePermission = $auth->createPermission('delete');
        $deletePermission->description = 'delete';
        $auth->add($deletePermission);

        // право заходить в админку (обязательная проверка для всех экшенов в BackendController)
        $administratePermission = $auth->createPermission('administrate');
        $administratePermission->description = 'administrate';
        $auth->add($administratePermission);

        // право настраивать виджеты
        $customizePermission = $auth->createPermission('customize');
        $customizePermission->description = 'customize';
        $auth->add($customizePermission);

        // право доступа к контролю версий элемента
        $versionsPermission = $auth->createPermission('versions');
        $versionsPermission->description = 'versions';
        $auth->add($versionsPermission);

        // add the rule
        $authorizedRule = new \gromver\platform\basic\rules\AuthorizedRule();
        $auth->add($authorizedRule);

        //add "Authorized" role
        $authorized = $auth->createRole('Authorized');
        $authorized->ruleName = $authorizedRule->name;
        $auth->add($authorized);

        // add "Author" role
        $author = $auth->createRole('Author');
        $auth->add($author);
        $auth->addChild($author, $administratePermission);
        $auth->addChild($author, $readPermission);
        $auth->addChild($author, $createPermission);
        $auth->addChild($author, $updatePermission);

        // add "Administrator" role
        $admin = $auth->createRole('Administrator');
        $auth->add($admin);
        $auth->addChild($admin, $deletePermission);
        $auth->addChild($admin, $customizePermission);
        $auth->addChild($admin, $versionsPermission);
        $auth->addChild($admin, $author);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        $auth->remove($auth->getRole('Author'));
        $auth->remove($auth->getRole('Administrator'));
        $auth->remove($auth->getRole('Authorized'));
        $auth->remove($auth->getPermission('create'));
        $auth->remove($auth->getPermission('read'));
        $auth->remove($auth->getPermission('update'));
        $auth->remove($auth->getPermission('delete'));
        $auth->remove($auth->getPermission('administrate'));
        $auth->remove($auth->getPermission('customize'));
        $auth->remove($auth->getPermission('versions'));
        $auth->remove($auth->getRule('isAuthorized'));
    }
}
