<?php

use yii\db\Migration;

/**
 * Handles the creation for table `first_visit`.
 */
class m160912_141702_create_first_visit_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%first_visit}}', [
            'id' => $this->primaryKey(),
            'session_id' => $this->string()->notNull()->comment('ID сессии'),
            'requested_url' => $this->string()->notNull()->comment('Первая страница, на которую перешёл клиент'),
            'referer' => $this->string()->comment('Откуда был заход'),
            'user_agent' => $this->string(),
            'time' => $this->dateTime()->notNull(),
        ]);
        $this->addCommentOnTable('{{%first_visit}}', 'Список первых заходов на сайт. Откуда зашёл и на какую страницу');

        $this->addColumn('{{%order}}', 'session_id', $this->string()->comment('Сессия, с которой был создан заказ'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'session_id');
        $this->dropTable('{{%first_visit}}');
    }
}
