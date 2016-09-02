<?php

use yii\db\Migration;

class m160831_092427_order_change extends Migration
{
    public function safeUp()
    {
        $this->createIndex('IDX__order__order_person_id', '{{%order}}', 'order_person_id',  true);
        $this->addColumn('{{%order}}', 'deleted', $this->boolean()->notNull()->defaultValue(false)->comment('Удаленный заказ'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'deleted');
        $this->dropIndex('IDX__order__order_person_id', '{{%order}}');
    }
}
