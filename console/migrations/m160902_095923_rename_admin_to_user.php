<?php

use yii\db\Migration;
use yii\db\Query;
use yii\db\Expression;

class m160902_095923_rename_admin_to_user extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string(100)->unique()->notNull(),
            'first_name' => $this->string(50)->notNull()->comment('Имя'),
            'last_name' => $this->string(50)->comment('Фамилия'),
            'middle_name' => $this->string(50)->comment('Отчество'),
            'address' => $this->string(),
            'address_latitude' => $this->float(),
            'address_longitude' => $this->float(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string(),
            'password_reset_token' => $this->string(),
            'phone' => $this->string(12)->notNull(),
            'enabled' => $this->boolean()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ]);
        $this->addCommentOnTable('{{%user}}', 'Таблица со всеми пользователями');

        // Перенос пользователей
        $query = new Query();
        $query->select([
            'id',
            'first_name',
            'last_name',
            'auth_key',
            'password_hash',
            'password_reset_token',
            'email',
            'phone',
            'enabled',
            'created_at',
            'updated_at',
        ]);
        $query->from('{{%admin}}');
        $rows = $query->all();

        foreach ($rows as &$row) {
            $row['phone'] = '+' . preg_replace('/\D/', '', $row['phone']);
        }

        $this->batchInsert('{{%user}}', [
            'id',
            'first_name',
            'last_name',
            'auth_key',
            'password_hash',
            'password_reset_token',
            'email',
            'phone',
            'enabled',
            'created_at',
            'updated_at',
        ], $rows);

        // Новости
        $this->dropForeignKey('FK__news__created_by__admin__id', '{{%news}}');
        $this->addForeignKey(
            'FK__news__created_by__user__id',
            '{{%news}}',
            'created_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );
        $this->addColumn('{{%news}}', 'updated_by', $this->integer()->comment('Время последнего обновления'));
        $this->update('{{%news}}', ['updated_at' => new Expression('NOW()')]);
        $this->execute('ALTER TABLE {{%news}} ALTER COLUMN [[updated_at]] SET NOT NULL');
        $this->addForeignKey(
            'FK__order__updated_by__user__id',
            '{{%news}}',
            'updated_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        // Заказы
        $this->dropForeignKey('FK__order__admin_id__admin__id', '{{%order}}');
        $this->renameColumn('{{%order}}', 'admin_id', 'created_by');
        $this->addForeignKey(
            'FK__order__created_by__user__id',
            '{{%order}}',
            'created_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );
        $this->addColumn('{{%order}}', 'updated_at', $this->dateTime());
        $this->update('{{%order}}', ['updated_at' => new Expression('NOW()')]);
        $this->execute('ALTER TABLE {{%order}} ALTER COLUMN [[updated_at]] SET NOT NULL');
        $this->addColumn('{{%order}}', 'updated_by', $this->integer()->comment('Пользователь, который произвёл последнее обновление'));
        $this->addForeignKey(
            'FK__order__updated_by__user__id',
            '{{%order}}',
            'updated_by',
            '{{%user}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        // Revision
        $this->dropForeignKey('FK__revision__admin_id__admin__id' , '{{%revision}}');
        $this->renameColumn('{{%revision}}', 'admin_id', 'user_id');
        $this->addForeignKey(
            'FK__revision__user_id__user__id',
            '{{%revision}}',
            'user_id',
            '{{%user}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        // Удаление таблицы admin
        $this->dropTable('{{%admin}}');


    }

    public function safeDown()
    {
        return false;
    }
}
