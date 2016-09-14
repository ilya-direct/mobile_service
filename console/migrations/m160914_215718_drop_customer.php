<?php

use yii\db\Migration;

class m160914_215718_drop_customer extends Migration
{
    public function safeUp()
    {
        $this->dropTable('{{%customer}}');
    }

    public function safeDown()
    {
        return false;
    }
}
