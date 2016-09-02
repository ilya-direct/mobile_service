<?php

use yii\db\Migration;

/**
 * Class m160831_110934_revision_tables
 *
 * Миграция по созданию ревизий
 */
class m160831_110934_revision_tables extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%revision_field}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique()->comment('Название поля таблицы по ревизии'),
        ]);
        $this->addCommentOnTable('{{%revision_field}}', 'Поля, по которым производится ревизия');

        $this->createTable('{{%revision_table}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique()->comment('Название таблицы по ревизии'),
        ]);
        $this->addCommentOnTable('{{%revision_table}}', 'Таблицы, по которым производится ревизия');

        $this->createTable('{{%revision_value_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique()->comment('Название типа данных'),
        ]);
        $this->addCommentOnTable('{{%revision_value_type}}', 'Тип данных у поля');

        $this->createTable('{{%revision}}', [
            'id' => $this->primaryKey(),
            'revision_table_id' => $this->integer()->notNull()->comment('Таблица по ревизии'),
            'revision_field_id' => $this->integer()->notNull()->comment('Поле таблицы по ревизии'),
            'record_id' => $this->integer()->notNull()->comment('id записи в изменённой таблице'),
            'revision_value_type_id' => $this->integer()->notNull()->comment('Тип данных у значения'),
            'value' => $this->text()->comment('Новое значение'),
            'admin_id' => $this->integer()->comment('Пользователь, изменивший значение'),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'operation_type' => $this->boolean()->notNull()->comment('true - insert operation, false - update operation')
        ]);
        $this->addCommentOnTable('{{%revision}}', 'Ревизии (логи) по всем таблицам');

        $this->addForeignKey(
            'FK__revision__revision_table_id__revision_table__id',
            '{{%revision}}',
            'revision_table_id',
            '{{%revision_table}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK__revision__revision_field_id__revision_field__id',
            '{{%revision}}',
            'revision_field_id',
            '{{%revision_field}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK__revision__revision_value_type_id__revision_value_type__id',
            '{{%revision}}',
            'revision_value_type_id',
            '{{%revision_value_type}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK__revision__admin_id__admin__id',
            '{{%revision}}',
            'admin_id',
            '{{%admin}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->createIndex('IDX__revision__admin_id', '{{%revision}}', 'admin_id');
        $this->createIndex('IDX__revision__created_at', '{{%revision}}', 'created_at');
        $this->createIndex('IDX__revision__revision_table_id__revision_field_id__record_id', '{{%revision}}', ['revision_table_id', 'revision_field_id', 'record_id']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%revision}}');
        $this->dropTable('{{%revision_value_type}}');
        $this->dropTable('{{%revision_table}}');
        $this->dropTable('{{%revision_field}}');
    }
}
