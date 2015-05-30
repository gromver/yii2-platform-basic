<?php

class m000010_000001_grom_elastic_search_rbac extends \yii\db\Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        // право на поиск (index)
        $searchPermission = $auth->createPermission('searchElastic');
        $searchPermission->description = 'search elastic';
        $auth->add($searchPermission);

        // право переиндексацию (reindex)
        $reindexPermission = $auth->createPermission('reindexElastic');
        $reindexPermission->description = 'reindex elastic';
        $auth->add($reindexPermission);

        // setup reader role
        $reader = $auth->getRole('reader');
        $auth->addChild($reader, $searchPermission);

        // setup administrator role
        $administrator = $auth->getRole('administrator');
        $auth->addChild($administrator, $reindexPermission);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        // roles
        $reader = $auth->getRole('reader');
        $administrator = $auth->getRole('administrator');

        // permissions
        $searchPermission = $auth->getPermission('searchElastic');
        $reindexPermission = $auth->getPermission('reindexElastic');

        $auth->removeChild($reader, $searchPermission);
        $auth->removeChild($administrator, $reindexPermission);

        $auth->remove($searchPermission);
        $auth->remove($reindexPermission);
    }
}
