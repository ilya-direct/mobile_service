<?php

use yii\db\Expression;
use yii\db\Migration;

class m160901_082936_alter_order_service extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%order_service}}', 'created_at', $this->dateTime());
        $this->update('{{%order_service}}', ['created_at' => new Expression('NOW()')]);
        $this->execute('ALTER TABLE {{%order_service}} ALTER COLUMN [[created_at]] SET NOT NULL');

        $this->addColumn('{{%order_service}}', 'deleted', $this->boolean());
        $this->update('{{%order_service}}', ['deleted' => false]);
        $this->execute('ALTER TABLE {{%order_service}} ALTER COLUMN [[deleted]] SET NOT NULL');


        $this->update('{{%service}}', ['position' => 0], ['position' => null]);
        $this->execute('ALTER TABLE {{%service}} ALTER COLUMN [[position]] SET NOT NULL');

        $this->dropIndex('UQ__order_service__order_id_device_assign_id', '{{%order_service}}');
    }

    public function safeDown()
    {
//        return false; Вернуть не получится
        $this->createIndex('UQ__order_service__order_id_device_assign_id', '{{%order_service}}', ['order_id', 'device_assign_id'], true);
        $this->execute('ALTER TABLE {{%service}} ALTER COLUMN [[position]] DROP NOT NULL');
        $this->dropColumn('{{%order_service}}', 'deleted');
        $this->dropColumn('{{%order_service}}', 'created_at');
    }
}
