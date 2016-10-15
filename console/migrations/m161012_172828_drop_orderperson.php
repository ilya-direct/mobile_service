<?php

use yii\db\Migration;
use common\models\ar\Order;
use common\models\ar\OrderPerson;

class m161012_172828_drop_orderperson extends Migration
{
    public function safeUp()
    {
        $this->addColumn(Order::tableName(), 'first_name', $this->string(30)->comment('Имя'));
        $this->addColumn(Order::tableName(), 'last_name', $this->string(30)->comment('Фамилия'));
        $this->addColumn(Order::tableName(), 'middle_name', $this->string(30)->comment('Отчество'));
        $this->addColumn(Order::tableName(), 'phone', $this->string(12)->comment('Телефон Пример +79636568378'));
        $this->addColumn(Order::tableName(), 'email', $this->string(50));
        $this->addColumn(Order::tableName(), 'address', $this->string()->comment('Адрес проживания'));
        $this->addColumn(Order::tableName(), 'address_latitude', $this->float());
        $this->addColumn(Order::tableName(), 'address_longitude', $this->float());

        /** @var Order[] $orders */
        $orders = Order::find()->innerJoinWith('orderPerson')->all();
        foreach ($orders as $order) {
            $order->first_name = $order->orderPerson->first_name;
            $order->last_name = $order->orderPerson->last_name;
            $order->middle_name = $order->orderPerson->middle_name;
            $order->phone = $order->orderPerson->phone;
            $order->email = $order->orderPerson->email;
            $order->address = $order->orderPerson->address;
            $order->address_latitude = $order->orderPerson->address_latitude;
            $order->address_longitude = $order->orderPerson->address_longitude;
            $order->save(false);
        }

        $this->execute('ALTER TABLE ' . Order::tableName() . ' ALTER COLUMN [[first_name]]  SET NOT NULL');
        $this->execute('ALTER TABLE ' . Order::tableName() . ' ALTER COLUMN [[phone]]  SET NOT NULL');

        $this->dropColumn(Order::tableName(), 'order_person_id');
        $this->dropTable(OrderPerson::tableName());


    }

    public function safeDown()
    {
        return false;
    }
}
