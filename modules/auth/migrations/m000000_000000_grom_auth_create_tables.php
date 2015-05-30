<?php

use yii\db\Schema;

class m000000_000000_grom_auth_create_tables extends \yii\db\Migration
{
    public function up()
    {
        // auth rule
        $this->createTable('{{%grom_auth_rule}}', [
            'name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (name)',
        ]);

        // auth item
        $this->createTable('{{%grom_auth_item}}', [
            'name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'type' => Schema::TYPE_INTEGER . ' NOT NULL',
            'description' => Schema::TYPE_TEXT,
            'rule_name' => Schema::TYPE_STRING . '(64)',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (name)',
        ]);

        $this->addForeignKey('CmsAuthItem_RuleName_fk', '{{%grom_auth_item}}', 'rule_name', '{{%grom_auth_rule}}', 'name', 'SET NULL', 'CASCADE');

        $this->createIndex('AuthItem_type_idx', '{{%grom_auth_item}}', 'type');

        // auth item child
        $this->createTable('{{%grom_auth_item_child}}', [
            'parent' => Schema::TYPE_STRING . '(64) NOT NULL',
            'child' => Schema::TYPE_STRING . '(64) NOT NULL',
            'PRIMARY KEY (parent,child)',
        ]);

        $this->addForeignKey('CmsAuthItemChild_Parent_fk', '{{%grom_auth_item_child}}', 'parent', '{{%grom_auth_item}}', 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('CmsAuthItemChild_Child_fk', '{{%grom_auth_item_child}}', 'child', '{{%grom_auth_item}}', 'name', 'CASCADE', 'CASCADE');

        // auth assignment
        $this->createTable('{{%grom_auth_assignment}}', [
            'item_name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (item_name,user_id)',
        ]);

        $this->addForeignKey('CmsAuthAssignment_ItemName_fk', '{{%grom_auth_assignment}}', 'item_name', '{{%grom_auth_item}}', 'name', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%grom_auth_assignment}}');
        $this->dropTable('{{%grom_auth_item_child}}');
        $this->dropTable('{{%grom_auth_item}}');
        $this->dropTable('{{%grom_auth_rule}}');
    }
}