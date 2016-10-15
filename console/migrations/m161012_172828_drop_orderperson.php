<?php

use yii\db\Migration;
use common\models\ar\Order;

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

        Yii::$app->runAction('fix/revision', [Order::className()]);

        /** @var Order[] $orders */
        $orders = Order::find()->all();
        $orderPersons = (new \yii\db\Query)
            ->select([
                'id',
                'first_name',
                'last_name',
                'middle_name',
                'phone',
                'email',
                'address',
                'address_latitude',
                'address_longitude'
            ])
            ->indexBy('id')
            ->from('{{%order_person}}')
            ->all();

        foreach ($orders as $order) {
            $order->first_name = $orderPersons[$order->order_person_id]['first_name'];
            $order->last_name = $orderPersons[$order->order_person_id]['last_name'];
            $order->middle_name = $orderPersons[$order->order_person_id]['middle_name'];
            $order->phone = $orderPersons[$order->order_person_id]['phone'];
            $order->email = $orderPersons[$order->order_person_id]['email'];
            $order->address = $orderPersons[$order->order_person_id]['address'];
            $order->address_latitude = $orderPersons[$order->order_person_id]['address_latitude'];
            $order->address_longitude = $orderPersons[$order->order_person_id]['address_longitude'];
            $order->save(false);
        }

        $this->execute('ALTER TABLE ' . Order::tableName() . ' ALTER COLUMN [[first_name]]  SET NOT NULL');
        $this->execute('ALTER TABLE ' . Order::tableName() . ' ALTER COLUMN [[phone]]  SET NOT NULL');

        $this->dropColumn(Order::tableName(), 'order_person_id');
        $this->dropTable('{{%order_person}}');
    }

    public function safeDown()
    {
        return false;
    }
}
