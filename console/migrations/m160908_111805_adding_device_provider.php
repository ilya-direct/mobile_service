<?php

use yii\db\Migration;

class m160908_111805_adding_device_provider extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->insert('{{%order_provider}}', ['name' => 'Форма на странице отдельного устройства']);
    }

    public function safeDown()
    {
        $this->delete('{{%order_provider}}', ['name' => 'Форма на странице отдельного устройства']);
    }
}
