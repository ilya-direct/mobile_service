<?php

use yii\db\Migration;

class m160902_161918_admin_deleted_field extends Migration
{

    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'deleted', $this->boolean());
        $this->update('{{%user}}', ['deleted' => false]);
        $this->execute('ALTER TABLE {{%user}} ALTER COLUMN [[deleted]] SET NOT NULL');
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'deleted');
    }

}
