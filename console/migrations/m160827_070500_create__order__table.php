<?php

use yii\db\Migration;

/**
 * Handles the creation for table `order` table.
 */
class m160827_070500_create__order__table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%order_person}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(30)->notNull()->comment('Имя'),
            'last_name' => $this->string(30)->comment('Фамилия'),
            'middle_name' => $this->string(30)->comment('Отчество'),
            'phone' => $this->string(12)->notNull(),
            'email' => $this->string(50),
            'address' => $this->string(150)->comment('Адрес проживания'),
        ]);

        $this->createTable('{{%order_status}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull()->comment('Статус заказа'),
        ]);

        $this->createTable('{{%order_provider}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull()->comment('Источник заказа'),
        ]);

        $this->createTable('{{%order}}', [
            'id' => $this->primaryKey(),
            'uid' => $this->string(10)->comment('Номер заказа'),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'order_status_id' => $this->integer()->notNull()->comment('Статус заказа'),
            'order_person_id' => $this->integer()->notNull()->comment('Данные заказавшего'),
            'order_provider_id' => $this->integer()->notNull()->comment('Источник заказа'),
            'preferable_date' => $this->dateTime()->comment('Желаемая дата ремонта'),
            'time_from' => $this->time()->comment('Время с'),
            'time_to' => $this->time()->comment('Время по'),
            'comment' => $this->string()->comment('Комментарий к заказу'),
            'referer' => $this->string()->notNull()->comment('Откуда был заход на сайт'),
            'ip' => 'inet NOT NULL',
        ]);
        $this->addCommentOnColumn('{{%order}}', 'ip', 'IP адрес клиента');

        $this->addForeignKey(
            'FK__order__order_status_id__order_status__id',
            '{{%order}}',
            'order_status_id',
            '{{%order_status}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK__order__order_provider_id__order_provider__id',
            '{{%order}}',
            'order_provider_id',
            '{{%order_provider}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK__order__order_person_id__order_person__id',
            '{{%order}}',
            'order_person_id',
            '{{%order_person}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->insert('{{%order_status}}',['name' => 'Новый заказ']);
        $this->insert('{{%order_provider}}',['name' => 'Верхняя форма "Оформить заявку" модальная']);
        $this->insert('{{%order_provider}}',['name' => 'Верхняя форма "Оформить заявку" на отдельной странице']);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK__order__order_person_id__order_person__id', '{{%order}}');
        $this->dropForeignKey('FK__order__order_provider_id__order_provider__id', '{{%order}}');
        $this->dropForeignKey('FK__order__order_status_id__order_status__id', '{{%order}}');
        $this->dropTable('{{%order}}');
        $this->dropTable('{{%order_provider}}');
        $this->dropTable('{{%order_status}}');
        $this->dropTable('{{%order_person}}');
    }
}
