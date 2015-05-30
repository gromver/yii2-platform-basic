<?php

use yii\db\Schema;

class m000002_000000_grom_user_create_tables extends \yii\db\Migration
{
    public function up()
    {
        // user
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

        // user profile
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
    }

    public function down()
    {
        $this->dropTable('{{%grom_user_profile}}');
        $this->dropTable('{{%grom_user}}');
    }
}