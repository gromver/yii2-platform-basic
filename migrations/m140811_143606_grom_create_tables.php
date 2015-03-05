<?php

use yii\db\Schema;

class m140811_143606_grom_create_tables extends \yii\db\Migration
{
    public function up()
    {
        //USER
        $this->createTable('{{%grom_user}}', [
            'id' => Schema::TYPE_PK,
            'username' => Schema::TYPE_STRING . '(64) NOT NULL',
            'email' => Schema::TYPE_STRING . '(128) NOT NULL',
            'password_hash' => Schema::TYPE_STRING . '(128) NOT NULL',
            'password_reset_token' => Schema::TYPE_STRING . '(32)',
            'auth_key' => Schema::TYPE_STRING . '(128)',
            'params' => Schema::TYPE_TEXT,
            'status' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT ' . \gromver\platform\basic\modules\user\models\User::STATUS_ACTIVE,
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER,
            'deleted_at' => Schema::TYPE_INTEGER,
            'last_visit_at' => Schema::TYPE_INTEGER,
        ]);

        $this->createIndex('Status_idx', '{{%grom_user}}', 'status');

        //RBAC
        $this->createTable('{{%grom_auth_rule}}', [
            'name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (name)',
        ]);

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

        $this->createTable('{{%grom_auth_item_child}}', [
            'parent' => Schema::TYPE_STRING . '(64) NOT NULL',
            'child' => Schema::TYPE_STRING . '(64) NOT NULL',
            'PRIMARY KEY (parent,child)',
        ]);

        $this->addForeignKey('CmsAuthItemChild_Parent_fk', '{{%grom_auth_item_child}}', 'parent', '{{%grom_auth_item}}', 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('CmsAuthItemChild_Child_fk', '{{%grom_auth_item_child}}', 'child', '{{%grom_auth_item}}', 'name', 'CASCADE', 'CASCADE');

        $this->createTable('{{%grom_auth_assignment}}', [
            'item_name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (item_name,user_id)',
        ]);

        $this->addForeignKey('CmsAuthAssignment_ItemName_fk', '{{%grom_auth_assignment}}', 'item_name', '{{%grom_auth_item}}', 'name', 'CASCADE', 'CASCADE');
        //USER PROFILE
        $this->createTable('{{%grom_user_profile}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'name' => Schema::TYPE_STRING . '(64)',
            'surname' => Schema::TYPE_STRING . '(64)',
            'patronymic' => Schema::TYPE_STRING . '(64)',
            'phone' => Schema::TYPE_STRING . '(20)',
            'work_phone' => Schema::TYPE_STRING . '(20)',
            'email' => Schema::TYPE_STRING,
            'work_email' => Schema::TYPE_STRING,
            'address' => Schema::TYPE_STRING . '(1024)',
        ]);

        $this->createIndex('UserId_idx', '{{%grom_user_profile}}', 'user_id');
        $this->createIndex('UserId_Id_idx', '{{%grom_user_profile}}', 'user_id, id');

        $this->addForeignKey('CmsUserProfile_UserId_fk', '{{%grom_user_profile}}', 'user_id', '{{%grom_user}}', 'id', 'CASCADE', 'CASCADE');

        //NEWS
        /* Category */
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
        /* Post */
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
        /* Post Viewed */
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

        //PAGE
        $this->createTable('{{%grom_page}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'translation_id' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'language' => Schema::TYPE_STRING . '(7) NOT NULL',
            'title' => Schema::TYPE_STRING . '(1024)',
            'alias' => Schema::TYPE_STRING,
            'path' => Schema::TYPE_STRING . '(2048)',
            'preview_text' => Schema::TYPE_TEXT,
            'detail_text' => Schema::TYPE_TEXT,
            'metakey' => Schema::TYPE_STRING,
            'metadesc' => Schema::TYPE_STRING . '(2048)',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER,
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
        $this->createIndex('TranslationId_idx', '{{%grom_page}}', 'translation_id');
        $this->createIndex('Language_idx', '{{%grom_page}}', 'language');
        $this->createIndex('Alias_idx', '{{%grom_page}}', 'alias');
        $this->createIndex('Path_idx', '{{%grom_page}}', 'path');
        $this->createIndex('Status_idx', '{{%grom_page}}', 'status');
        $this->createIndex('ParentId_idx', '{{%grom_page}}', 'parent_id');
        $this->createIndex('Lft_Rgt_idx', '{{%grom_page}}', 'lft, rgt');
        //вставляем рутовый элемент
        $this->insert('{{%grom_page}}', [
            'status' => 1,
            'title' => 'Root',
            'language' => '',   //maybe 'all'..?
            'created_at' => time(),
            'created_by' => 1,
            'lft' => 1,
            'rgt' => 2,
            'level' => 1,
            'ordering' => 1
        ]);

        //TAGS
        /* tag */
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
        /* tag_to_item */
        $this->createTable('{{%grom_tag_to_item}}', [
            'tag_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'item_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'item_class' => Schema::TYPE_STRING . '(1024) NOT NULL',
        ]);
        $this->createIndex('TagId_ItemId_idx', '{{%grom_tag_to_item}}', 'tag_id, item_id');

        $this->addForeignKey('CmsTagToItem_TagId_fk', '{{%grom_tag_to_item}}', 'tag_id', '{{%grom_tag}}', 'id', 'CASCADE', 'CASCADE');

        //HISTORY
        $this->createTable('{{%grom_version}}', [
            'id' => Schema::TYPE_PK,
            'item_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'item_class' => Schema::TYPE_STRING . '(1024)',
            'version_note' => Schema::TYPE_STRING,
            'version_hash' => Schema::TYPE_STRING . '(50)',
            'version_data' => Schema::TYPE_TEXT,
            'character_count' => Schema::TYPE_INTEGER . ' UNSIGNED',
            'keep_forever' => Schema::TYPE_BOOLEAN . ' DEFAULT 0',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL',
        ]);
        $this->createIndex('Item_idx', '{{%grom_version}}', 'item_id');
        $this->createIndex('ItemClass_idx', '{{%grom_version}}', 'item_class');
        $this->createIndex('VersionHash_idx', '{{%grom_version}}', 'version_hash');

        //MENU
        $this->createTable('{{%grom_menu_type}}', [
            'id' => Schema::TYPE_PK,
            'title' => Schema::TYPE_STRING . '(1024)',
            'alias' => Schema::TYPE_STRING,
            'path' => Schema::TYPE_STRING . '(2048)',
            'note' => Schema::TYPE_STRING . '(255)',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER,
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_by' => Schema::TYPE_INTEGER,
            'lock' => Schema::TYPE_BIGINT . ' UNSIGNED DEFAULT 1',
        ]);

        $this->createTable('{{%grom_menu_item}}', [
            'id' => Schema::TYPE_PK,
            'menu_type_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'parent_id' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'translation_id' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'status' => Schema::TYPE_SMALLINT,
            'language' => Schema::TYPE_STRING . '(7) NOT NULL',
            'title' => Schema::TYPE_STRING . '(1024)',
            'alias' => Schema::TYPE_STRING,
            'path' => Schema::TYPE_STRING . '(2048)',
            'note' => Schema::TYPE_STRING,
            'link' => Schema::TYPE_STRING . '(1024)',
            'link_type' => Schema::TYPE_SMALLINT,
            'link_weight' => Schema::TYPE_SMALLINT . ' DEFAULT 0',
            'link_params' => Schema::TYPE_TEXT,
            'layout_path' => Schema::TYPE_STRING . '(1024)',
            'access_rule' => Schema::TYPE_STRING . '(50)',
            'metakey' => Schema::TYPE_STRING,
            'metadesc' => Schema::TYPE_STRING . '(2048)',
            'robots' => Schema::TYPE_STRING . '(50)',
            'secure' => Schema::TYPE_BOOLEAN,
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER,
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_by' => Schema::TYPE_INTEGER,
            'lft' => Schema::TYPE_INTEGER,
            'rgt' => Schema::TYPE_INTEGER,
            'level' => Schema::TYPE_SMALLINT . ' UNSIGNED',
            'ordering' => Schema::TYPE_INTEGER . ' UNSIGNED',
            'hits' => Schema::TYPE_BIGINT . ' UNSIGNED',
            'lock' => Schema::TYPE_BIGINT . ' UNSIGNED DEFAULT 1',
        ]);
        $this->createIndex('MenuTypeId_idx', '{{%grom_menu_item}}', 'menu_type_id');
        $this->createIndex('ParentId_idx', '{{%grom_menu_item}}', 'parent_id');
        $this->createIndex('TranslationId_idx', '{{%grom_menu_item}}', 'translation_id');
        $this->createIndex('Lft_Rgt_idx', '{{%grom_menu_item}}', 'lft, rgt');
        $this->createIndex('Language_idx', '{{%grom_menu_item}}', 'language');
        $this->createIndex('Path_idx', '{{%grom_menu_item}}', 'path');
        $this->createIndex('Alias_idx', '{{%grom_menu_item}}', 'alias');
        $this->createIndex('Status_idx', '{{%grom_menu_item}}', 'status');
        //вставляем рутовый элемент
        $this->insert('{{%grom_menu_item}}', [
            'menu_type_id' => 0,
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

        //WIDGET CONFIG
        $this->createTable('{{%grom_widget_config}}', [
            'id' => Schema::TYPE_PK,
            'widget_id' => Schema::TYPE_STRING . '(50) NOT NULL',
            'widget_class' => Schema::TYPE_STRING . ' NOT NULL',
            'language' => Schema::TYPE_STRING . '(7) NOT NULL',
            'context' => Schema::TYPE_STRING . '(1024)',
            'url' => Schema::TYPE_STRING . '(1024)',
            'params' => Schema::TYPE_TEXT,
            'valid' => Schema::TYPE_BOOLEAN . ' DEFAULT 1',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER,
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_by' => Schema::TYPE_INTEGER,
            'lock' => Schema::TYPE_BIGINT . ' UNSIGNED DEFAULT 1',
        ]);
        $this->createIndex('WidgetId_Language_idx', '{{%grom_widget_config}}', 'widget_id, language');
        $this->createIndex('WidgetContext_idx', '{{%grom_widget_config}}', 'context');

        //TABLE
        $this->createTable('{{%grom_db_state}}', [
            'id' => Schema::TYPE_STRING . ' NOT NULL',
            'timestamp' => Schema::TYPE_INTEGER . ' NOT NULL',
            'PRIMARY KEY (`id`)'
        ]);

        //SEARCH INDEX
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
        //RBAC
        $this->dropTable('{{%grom_auth_assignment}}');
        $this->dropTable('{{%grom_auth_item_child}}');
        $this->dropTable('{{%grom_auth_item}}');
        $this->dropTable('{{%grom_auth_rule}}');
        //USER PROFILE
        $this->dropTable('{{%grom_user_profile}}');
        //NEWS
        $this->dropTable('{{%grom_post_viewed}}');
        $this->dropTable('{{%grom_post}}');
        $this->dropTable('{{%grom_category}}');
        //PAGE
        $this->dropTable('{{%grom_page}}');
        //TAG
        $this->dropTable('{{%grom_tag_to_item}}');
        $this->dropTable('{{%grom_tag}}');
        //HISTORY
        $this->dropTable('{{%grom_version}}');
        //MENU
        $this->dropTable('{{%grom_menu_type}}');
        $this->dropTable('{{%grom_menu_item}}');
        //WIDGET CONFIG
        $this->dropTable('{{%grom_widget_config}}');
        //TABLE
        $this->dropTable('{{%grom_db_state}}');
        //USER
        $this->dropTable('{{%grom_user}}');
        //SEARCH INDEX
        $this->dropTable('{{%grom_index}}');
    }
}