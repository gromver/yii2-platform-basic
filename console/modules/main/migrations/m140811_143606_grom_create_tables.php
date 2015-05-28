<?php

namespace gromver\platform\basic\console\modules\main\migrations;

use yii\db\Schema;

class m140811_143606_grom_create_tables extends \yii\db\Migration
{
    public function up()
    {
        //TABLE
        $this->createTable('{{%grom_db_state}}', [
            'id' => Schema::TYPE_STRING . ' NOT NULL',
            'timestamp' => Schema::TYPE_INTEGER . ' NOT NULL',
            'PRIMARY KEY (`id`)'
        ]);
    }

    public function down()
    {
        //TABLE
        $this->dropTable('{{%grom_db_state}}');
    }
}