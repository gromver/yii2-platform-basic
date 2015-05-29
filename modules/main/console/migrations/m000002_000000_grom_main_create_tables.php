<?php

use yii\db\Schema;

class m000002_000000_grom_main_create_tables extends \yii\db\Migration
{
    public function up()
    {
        // db state
        $this->createTable('{{%grom_db_state}}', [
            'id' => Schema::TYPE_STRING . ' NOT NULL',
            'timestamp' => Schema::TYPE_INTEGER . ' NOT NULL',
            'PRIMARY KEY (`id`)'
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%grom_db_state}}');
    }
}