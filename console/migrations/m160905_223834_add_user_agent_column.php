<?php

use yii\db\Migration;

class m160905_223834_add_user_agent_column extends Migration
{
    public function safeUp()
    {
        $this->insert('{{%order_provider}}', ['name' => 'Нижняя форма "Заявка на звонок"']);
        $this->insert('{{%order_provider}}', ['name' => 'Калькулятор услуг на главной странице']);
        $this->addColumn('{{%order}}', 'user_agent', $this->string()->comment('User Agent с которого был создан заказ'));
    }

    public function safeDown()
    {
        return false;
        //$this->delete('{{%order_provider}}', ['name' => 'Нижняя форма "Заявка на звонок"']);
    }
}
