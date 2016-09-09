<?php

use yii\db\Migration;

class m160909_091403_add_column_client_comment extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'client_comment', $this->string());
        $this->insert('{{%order_provider}}', ['name' => 'Форма "Не нашёл нужную модель"']);
    }

    public function safeDown()
    {
        $this->delete('{{%order_provider}}', ['name' => 'Форма "Не нашёл нужную модель"']);
        $this->dropColumn('{{%order}}', 'client_comment');
    }
}
