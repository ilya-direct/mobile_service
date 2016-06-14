<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%admin}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'first_name' => $this->string(20)->notNull(),
            'last_name' => $this->string(20)->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'phone' => $this->string(),
            'enabled' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ], $tableOptions);


        $this->createTable('{{%customer}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'first_name' => $this->string()->notNull(),
            'last_name' => $this->string(),
            'middle_name' => $this->string(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'enabled' => $this->smallInteger()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%customer}}');
        $this->dropTable('{{%admin}}');
    }
}
