<?php

use yii\db\Schema;

class m000007_000000_grom_news_create_tables extends \yii\db\Migration
{
    public function up()
    {
        // category
        $this->createTable('{{%grom_category}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'translation_id' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'language' => Schema::TYPE_STRING . '(7) NOT NULL',
            'title' => Schema::TYPE_STRING . '(1024)',
            'alias' => Schema::TYPE_STRING,
            'path' => Schema::TYPE_STRING . '(2048)',
            'preview_text' => Schema::TYPE_TEXT,
            'preview_image' => Schema::TYPE_STRING . '(1024)',
            'detail_text' => Schema::TYPE_TEXT,
            'detail_image' => Schema::TYPE_STRING . '(1024)',
            'metakey' => Schema::TYPE_STRING,
            'metadesc' => Schema::TYPE_STRING . '(2048)',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER,
            'published_at' => Schema::TYPE_INTEGER,
            'status' => Schema::TYPE_SMALLINT,
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_by' => Schema::TYPE_INTEGER,
            'lft' => Schema::TYPE_INTEGER,
            'rgt' => Schema::TYPE_INTEGER,
            'level' => Schema::TYPE_SMALLINT . ' UNSIGNED',
            'ordering' => Schema::TYPE_INTEGER . ' UNSIGNED',
            'hits' => Schema::TYPE_BIGINT . ' UNSIGNED',
            'lock' => Schema::TYPE_BIGINT . ' UNSIGNED DEFAULT 1',
        ]);
        $this->createIndex('ParentId_idx', '{{%grom_category}}', 'parent_id');
        $this->createIndex('TranslationId_idx', '{{%grom_category}}', 'translation_id');
        $this->createIndex('Lft_Rgt_idx', '{{%grom_category}}', 'lft, rgt');
        $this->createIndex('Language_idx', '{{%grom_category}}', 'language');
        $this->createIndex('Path_idx', '{{%grom_category}}', 'path');
        $this->createIndex('Alias_idx', '{{%grom_category}}', 'alias');
        $this->createIndex('Status_idx', '{{%grom_category}}', 'status');
        //вставляем рутовый элемент
        $this->insert('{{%grom_category}}', [
            'status' => 1,
            'title' => 'Root',
            'language' => '',
            'created_at' => time(),
            'created_by' => 1,
            'lft' => 1,
            'rgt' => 2,
            'level' => 1,
            'ordering' => 1
        ]);

        // post
        $this->createTable('{{%grom_post}}', [
            'id' => Schema::TYPE_PK,
            'category_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'translation_id' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'language' => Schema::TYPE_STRING . '(7) NOT NULL',
            'title' => Schema::TYPE_STRING . '(1024)',
            'alias' => Schema::TYPE_STRING,
            'preview_text' => Schema::TYPE_TEXT,
            'preview_image' => Schema::TYPE_STRING . '(1024)',
            'detail_text' => Schema::TYPE_TEXT,
            'detail_image' => Schema::TYPE_STRING . '(1024)',
            'metakey' => Schema::TYPE_STRING,
            'metadesc' => Schema::TYPE_STRING . '(2048)',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER,
            'published_at' => Schema::TYPE_INTEGER,
            'status' => Schema::TYPE_SMALLINT,
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_by' => Schema::TYPE_INTEGER,
            'ordering' => Schema::TYPE_INTEGER . ' UNSIGNED',
            'hits' => Schema::TYPE_BIGINT . ' UNSIGNED',
            'lock' => Schema::TYPE_BIGINT . ' UNSIGNED DEFAULT 1',
        ]);
        $this->createIndex('CategoryId_idx', '{{%grom_post}}', 'category_id');
        $this->createIndex('TranslationId_idx', '{{%grom_post}}', 'translation_id');
        $this->createIndex('Language_idx', '{{%grom_post}}', 'language');
        $this->createIndex('Alias_idx', '{{%grom_post}}', 'alias');
        $this->createIndex('Status_idx', '{{%grom_post}}', 'status');
        $this->addForeignKey('CmsPost_CategoryId_fk', '{{%grom_post}}', 'category_id', '{{%grom_category}}', 'id', 'CASCADE', 'CASCADE');

        // post viewed
        $this->createTable('{{%grom_post_viewed}}', [
            'post_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'viewed_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ]);
        $this->addPrimaryKey('PostId_UserId_pr', '{{%grom_post_viewed}}', 'post_id, user_id');
        $this->createIndex('PostId_idx', '{{%grom_post_viewed}}', 'post_id');
        $this->createIndex('UserId_idx', '{{%grom_post_viewed}}', 'user_id');
        $this->addForeignKey('CmsPostViewed_PostId_fk', '{{%grom_post_viewed}}', 'post_id', '{{%grom_post}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('CmsPostViewed_UserId_fk', '{{%grom_post_viewed}}', 'user_id', '{{%grom_user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%grom_post_viewed}}');
        $this->dropTable('{{%grom_post}}');
        $this->dropTable('{{%grom_category}}');
    }
}