<?php

use yii\db\Schema;

class m000006_000000_grom_tag_create_tables extends \yii\db\Migration
{
    public function up()
    {
        // tag
        $this->createTable('{{%grom_tag}}', [
            'id' => Schema::TYPE_PK,
            'translation_id' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'language' => Schema::TYPE_STRING . '(7) NOT NULL',
            'title' => Schema::TYPE_STRING . '(100)',
            'alias' => Schema::TYPE_STRING,
            'status' => Schema::TYPE_SMALLINT,
            'group' => Schema::TYPE_STRING,
            'metakey' => Schema::TYPE_STRING,
            'metadesc' => Schema::TYPE_STRING . '(2048)',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER,
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_by' => Schema::TYPE_INTEGER,
            'hits' => Schema::TYPE_BIGINT . ' UNSIGNED',
            'lock' => Schema::TYPE_BIGINT . ' UNSIGNED DEFAULT 1',
        ]);
        $this->createIndex('TranslationId_idx', '{{%grom_tag}}', 'translation_id');
        $this->createIndex('Language_idx', '{{%grom_tag}}', 'language');
        $this->createIndex('Alias_idx', '{{%grom_tag}}', 'alias');
        $this->createIndex('Status_idx', '{{%grom_tag}}', 'status');

        // tag_to_item
        $this->createTable('{{%grom_tag_to_item}}', [
            'tag_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'item_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'item_class' => Schema::TYPE_STRING . '(1024) NOT NULL',
        ]);
        $this->createIndex('TagId_ItemId_idx', '{{%grom_tag_to_item}}', 'tag_id, item_id');
        $this->addForeignKey('CmsTagToItem_TagId_fk', '{{%grom_tag_to_item}}', 'tag_id', '{{%grom_tag}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%grom_tag_to_item}}');
        $this->dropTable('{{%grom_tag}}');
    }
}