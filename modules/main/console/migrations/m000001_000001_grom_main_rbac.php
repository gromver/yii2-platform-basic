<?php

class m000001_000001_grom_main_rbac extends \yii\db\Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        $administratePermission = $auth->createPermission('administrate');
        $administratePermission->description = 'administrate';
        $auth->add($administratePermission);

        $settingsPermission = $auth->createPermission('customizeMainSettings');
        $settingsPermission->description = 'customizeMainSettings';
        $auth->add($settingsPermission);

        $flushCachePermission = $auth->createPermission('flushCache');
        $flushCachePermission->description = 'flushCache';
        $auth->add($flushCachePermission);

        $flushAssetsPermission = $auth->createPermission('flushAssets');
        $flushAssetsPermission->description = 'flushAssets';
        $auth->add($flushAssetsPermission);

        // add the rule
        $authorizedRule = new \gromver\platform\basic\modules\main\rules\AuthorizedRule();
        $auth->add($authorizedRule);

        //add "authorized" role
        $authorized = $auth->createRole('authorized');
        $authorized->ruleName = $authorizedRule->name;
        $auth->add($authorized);

        // add "reader" role
        $reader = $auth->createRole('reader');
        $auth->add($reader);
        $auth->addChild($reader, $authorized);
        $auth->addChild($reader, $administratePermission);

        // add "author" role
        $author = $auth->createRole('author');
        $auth->add($author);
        $auth->addChild($author, $reader);

        // add "editor" role
        $editor = $auth->createRole('editor');
        $auth->add($editor);
        $auth->addChild($editor, $author);

        // add "administrator" role
        $administrator = $auth->createRole('administrator');
        $auth->add($administrator);
        $auth->addChild($administrator, $settingsPermission);
        $auth->addChild($administrator, $flushCachePermission);
        $auth->addChild($administrator, $flushAssetsPermission);
        $auth->addChild($administrator, $editor);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        $auth->remove($auth->getRole('administrator'));
        $auth->remove($auth->getRole('editor'));
        $auth->remove($auth->getRole('author'));
        $auth->remove($auth->getRole('reader'));
        $auth->remove($auth->getRole('authorized'));
        $auth->remove($auth->getPermission('customizeMainSettings'));
        $auth->remove($auth->getPermission('flushCache'));
        $auth->remove($auth->getPermission('flushAssets'));
        $auth->remove($auth->getPermission('administrate'));
        $auth->remove($auth->getRule('isAuthorized'));
    }
}
