<?php

use yii\db\Migration;

class m160324_065716_tables extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%device}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'description' => $this->text(),
            'image' => $this->string(),
            'device_category_id' => $this->integer(),
            'enabled' => $this->smallInteger()->defaultValue(1),
        ]);

        $this->createTable('{{%device_category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'tree' => $this->integer()->notNull(),
            'lft' => $this->integer()->notNull(),
            'rgt' => $this->integer()->notNull(),
            'depth' => $this->integer()->notNull(),
            'alias' => $this->string()->notNull(),
            'description' => $this->text(),
            'enabled' => $this->smallInteger()->defaultValue(1),
        ]);

        $this->createTable('{{%service}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'small_description' => $this->text(),
            'service_category_id' => $this->integer(),
            'position' => $this->integer(),
            'enabled' => $this->smallInteger()->defaultValue(1),
        ]);

        $this->createTable('{{%service_category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'position' => $this->integer(),
        ]);


        $this->createTable('{{%device_assign}}', [
            'id' => $this->primaryKey(),
            'device_id' => $this->integer()->notNull(),
            'service_id' => $this->integer()->notNull(),
            'price' => $this->integer()->notNull(),
            'price_old' => $this->integer(),
            'enabled' => $this->smallInteger()->defaultValue(1),
        ]);

        $this->createTable('{{%news}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull()->notNull(),
            'description_short' => $this->text(),
            'description' => $this->text()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime(),
            'enabled' => $this->smallInteger()->defaultValue(1),
        ]);

        $this->addForeignKey(
            'device-device_category',
            '{{%device}}',
            'device_category_id',
            '{{%device_category}}',
            'id'
        );

        $this->addForeignKey(
            'service-service_category',
            '{{%service}}',
            'service_category_id',
            '{{%service_category}}',
            'id'
        );

        $this->addForeignKey(
            'device_assign-device',
            '{{%device_assign}}',
            'device_id',
            '{{%device}}',
            'id'
        );

        $this->addForeignKey(
            'device_assign-service',
            '{{%device_assign}}',
            'service_id',
            '{{%service}}',
            'id'
        );

    }

    public function safeDown()
    {
        $this->dropForeignKey('device_assign-service','{{%device_assign}}');
        $this->dropForeignKey('device_assign-device','{{%device_assign}}');
        $this->dropForeignKey('service-service_category','{{%service}}');
        $this->dropForeignKey('device-device_category','{{%device}}');

        $this->dropTable('{{%news}}');
        $this->dropTable('{{%device_assign}}');
        $this->dropTable('{{%service_category}}');
        $this->dropTable('{{%service}}');
        $this->dropTable('{{%device_category}}');
        $this->dropTable('{{%device}}');
    }
}
