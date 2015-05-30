<?php

use yii\db\Schema;

class m000005_000000_grom_widget_create_tables extends \yii\db\Migration
{
    public function up()
    {
        // widget config
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

        // widget config personal
        $this->createTable('{{%grom_widget_config_personal}}', [
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
        $this->createIndex('WidgetId_Language_CreatedBy_idx', '{{%grom_widget_config_personal}}', 'widget_id, language, created_by');
        $this->createIndex('WidgetContext_idx', '{{%grom_widget_config_personal}}', 'context');
    }

    public function down()
    {
        $this->dropTable('{{%grom_widget_config}}');
        $this->dropTable('{{%grom_widget_config_personal}}');
    }
}