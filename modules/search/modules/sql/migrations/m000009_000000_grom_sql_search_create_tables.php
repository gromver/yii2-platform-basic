<?php

use yii\db\Schema;

class m000009_000000_grom_sql_search_create_tables extends \yii\db\Migration
{
    public function up()
    {
        // index
        $this->createTable('{{%grom_index}}', [
            'id' => Schema::TYPE_PK,
            'model_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'model_class' => Schema::TYPE_STRING . ' NOT NULL',
            'title' => Schema::TYPE_STRING . '(1024) NOT NULL',
            'content' => Schema::TYPE_TEXT . ' NOT NULL',
            'tags' => Schema::TYPE_STRING . '(1024) NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'url_frontend' => Schema::TYPE_STRING . '(1024) NOT NULL',
            'url_backend' => Schema::TYPE_STRING . '(1024) NOT NULL',
            'FULLTEXT (title,content)',
            'FULLTEXT (tags)',
            'UNIQUE INDEX IndexModelId_ModelClass (model_id, model_class)'
        ], 'ENGINE=MyISAM;');
    }

    public function down()
    {
        $this->dropTable('{{%grom_index}}');
    }
}