<?php

use yii\db\Migration;
use common\models\ar\Order;
use common\models\ar\User;

class m161009_152425_order_operator_and_worker extends Migration
{
    public function safeUp()
    {
        $this->addColumn(Order::tableName(), 'operator_id', $this->integer()->comment('id Оператора'));
        $this->addForeignKey('FK__order__operator_id__user__id',
            Order::tableName(),
            'operator_id',
            User::tableName(),
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addColumn(Order::tableName(), 'worker_id', $this->integer()->comment('id Мастера'));
        $this->addForeignKey('FK__order__worker_id__user__id',
            Order::tableName(),
            'worker_id',
            User::tableName(),
            'id',
            'RESTRICT',
            'CASCADE'
        );

    }

    public function safeDown()
    {
        $this->dropColumn(Order::tableName(), 'operator_id');
        $this->dropColumn(Order::tableName(), 'worker_id');
    }
}
