<?php

use yii\db\Migration;

class m160906_101758_vendor_table extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%vendor}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->unique()->notNull()->comment('Название производителя'),
            'enabled' => $this->boolean()->notNull()->comment('Активен (отображать ли на сайте)'),
            'created_at' => $this->dateTime()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
            'updated_by' => $this->integer()->notNull(),
        ]);
        $this->addCommentOnTable('{{%vendor}}', 'Таблица производителей');
        $this->addForeignKey(
            'FK__vendor__created_by__user__id',
            '{{%vendor}}',
            'created_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK__vendor__updated_by__user__id',
            '{{%vendor}}',
            'updated_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addColumn('{{%device}}', 'vendor_id', $this->integer());

        $this->addForeignKey(
            'FK__device__vendor_id__vendor__id',
            '{{%device}}',
            'vendor_id',
            '{{%vendor}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

    }

    public function safeDown()
    {
        $this->dropColumn('{{%device}}', 'vendor_id');
        $this->dropTable('{{%vendor}}');

    }
}
