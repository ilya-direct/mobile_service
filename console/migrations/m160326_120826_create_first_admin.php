<?php

use yii\db\Migration;

class m160326_120826_create_first_admin extends Migration
{
    public function safeUp()
    {
        $this->insert('{{%admin}}', [
            'username' => 'root',
            'first_name' => 'ROOT',
            'last_name' => 'ROOT',
            'email' => 'root@root.root',
            'enabled' => true,
            'created_at' => time(),
            'updated_at' => time(),
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('tour'),
        ]);
    }

    public function safeDown()
    {
        $this->delete('{{%admin}}', ['username' => 'root']);
    }
}
