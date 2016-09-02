<?php

use yii\db\Migration;

class m160618_151726_news__add_column extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%news}}', 'created_by', $this->integer());
        $this->addForeignKey(
            'FK__news__created_by__admin__id',
            '{{%news}}',
            'created_by',
            '{{%admin}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('FK__news__created_by__admin__id', '{{%news}}');
        $this->dropColumn('{{%news}}', 'created_by');
    }
}
