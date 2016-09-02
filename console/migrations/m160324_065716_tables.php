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
            'enabled' => $this->boolean()->defaultValue(true)->notNull(),
        ]);

        $this->createTable('{{%device_category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'tree' => $this->integer(),
            'lft' => $this->integer()->notNull(),
            'rgt' => $this->integer()->notNull(),
            'depth' => $this->integer()->notNull(),
            'alias' => $this->string()->unique()->notNull(),
            'description' => $this->text(),
            'enabled' => $this->boolean()->defaultValue(true)->notNull(),
        ]);

        $this->createTable('{{%service}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'small_description' => $this->text(),
            'service_category_id' => $this->integer(),
            'position' => $this->integer(),
            'enabled' => $this->boolean()->defaultValue(true)->notNull(),
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
            'enabled' => $this->boolean()->defaultValue(true)->notNull(),
        ]);

        $this->createTable('{{%news}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull()->notNull(),
            'description_short' => $this->text(),
            'description' => $this->text()->notNull(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'enabled' => $this->boolean()->defaultValue(true)->notNull(),
        ]);

        $this->addForeignKey(
            'FK__device__device_category_id__device_category__id',
            '{{%device}}',
            'device_category_id',
            '{{%device_category}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK__service__service_category_id__service_category__id',
            '{{%service}}',
            'service_category_id',
            '{{%service_category}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK__device_assign__device_id__device__id',
            '{{%device_assign}}',
            'device_id',
            '{{%device}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK__device_assign__service_id__service__id',
            '{{%device_assign}}',
            'service_id',
            '{{%service}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

    }

    public function safeDown()
    {
        $this->dropForeignKey('FK__device_assign__service_id__service__id','{{%device_assign}}');
        $this->dropForeignKey('FK__device_assign__device_id__device__id','{{%device_assign}}');
        $this->dropForeignKey('FK__service__service_category_id__service_category__id','{{%service}}');
        $this->dropForeignKey('FK__device__device_category_id__device_category__id','{{%device}}');

        $this->dropTable('{{%news}}');
        $this->dropTable('{{%device_assign}}');
        $this->dropTable('{{%service_category}}');
        $this->dropTable('{{%service}}');
        $this->dropTable('{{%device_category}}');
        $this->dropTable('{{%device}}');
    }
}
