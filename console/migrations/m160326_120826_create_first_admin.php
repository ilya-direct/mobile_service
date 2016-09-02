<?php

use yii\db\Migration;

class m160326_120826_create_first_admin extends Migration
{
    public function safeUp()
    {
        $this->insert('{{%admin}}', [
            'username' => 'root',
            'first_name' => 'Илья',
            'last_name' => 'Смирнов',
            'email' => 'ilya-direct@ya.ru',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('tour'),
            'enabled' => true,
            'phone' => '+7(963) 656 83 77',
        ]);
    }

    public function safeDown()
    {
        $this->delete('{{%admin}}', ['username' => 'root']);
    }
}
