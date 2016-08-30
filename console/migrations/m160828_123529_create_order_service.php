<?php

use yii\db\Migration;

/**
 * Handles the creation for table `order_service`.
 */
class m160828_123529_create_order_service extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%order_service}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'device_assign_id' => $this->integer()->notNull(),
        ]);
        $this->addCommentOnTable('{{%order_service}}', 'Услуги в заказе');
        $this->createIndex('UQ__order_service__order_id_device_assign_id', '{{%order_service}}', ['order_id', 'device_assign_id'], true);
        $this->addForeignKey(
            'FK__order_service__order_id__order__id',
            '{{%order_service}}',
            'order_id',
            '{{%order}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK__order_service__device_assign_id__device_assign__id',
            '{{%order_service}}',
            'device_assign_id',
            '{{%device_assign}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addColumn('{{%order}}', 'client_lead', $this->string()->comment('Откуда пришёл клиент, заполняется оператором со слов'));
        $this->addColumn('{{%order}}', 'admin_id', $this->integer());

        $this->addForeignKey(
            'FK__order__admin_id__admin__id',
            '{{%order}}',
            'admin_id',
            '{{%admin}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->update('{{%order_status}}', ['name' => 'Новый'], ['name' => 'Новый заказ']);

        $this->insert('{{%order_status}}', ['name' => 'Подтверждён']);
        $this->insert('{{%order_status}}', ['name' => 'Уточняется']);
        $this->insert('{{%order_status}}', ['name' => 'Анулирован']);

        $this->insert('{{%order_provider}}', ['name' => 'Административная панель']);

        $this->addColumn('{{%order_person}}', 'address_latitude', $this->float());
        $this->addColumn('{{%order_person}}', 'address_longitude', $this->float());

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order_person}}', 'address_longitude');
        $this->dropColumn('{{%order_person}}', 'address_latitude');

        $this->delete('{{%order_provider}}', ['name' => 'Административная панель']);
        $this->delete('{{%order_status}}', ['name' => 'Анулирован']);
        $this->delete('{{%order_status}}', ['name' => 'Уточняется']);
        $this->delete('{{%order_status}}', ['name' => 'Подтверждён']);
        $this->update('{{%order_status}}', ['name' => 'Новый заказ'], ['name' => 'Новый']);

        $this->dropForeignKey('FK__order__admin_id__admin__id', '{{%order}}');
        $this->dropColumn('{{%order}}', 'admin_id');
        $this->dropColumn('{{%order}}', 'client_lead');
        $this->dropForeignKey('FK__order_service__device_assign_id__device_assign__id', '{{%order_service}}');
        $this->dropForeignKey('FK__order_service__order_id__order__id', '{{%order_service}}');
        $this->dropIndex('UQ__order_service__order_id_device_assign_id', '{{%order_service}}');
        $this->dropTable('{{%order_service}}');
    }
}
