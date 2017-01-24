<?php

use yii\db\Migration;

class m170119_120104_table_token extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%user_token}}', [
            'id' => $this->primaryKey(),
            'value' => $this->string()->notNull()->comment('User token that is used for authentication'),
            'user_id' => $this->integer()->notNull()->comment('User link'),
            'expire_date' => $this->dateTime()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
        ]);
        $this->addCommentOnTable('{{%user_token}}', 'For storing user api tokens');
        
        $this->createIndex('IX__token__value', '{{%user_token}}', 'value');
        $this->addForeignKey(
            'FK__token__user_id__user__id',
            '{{%user_token}}',
            'user_id',
            '{{%user}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%user_token}}');
    }
}
