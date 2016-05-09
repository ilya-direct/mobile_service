<?php

use yii\db\Migration;

class m160330_174518_alter_category_drop_not_null extends Migration
{
    public function safeUp()
    {
        $this->execute('ALTER TABLE {{%device_category}} ALTER  COLUMN [[tree]] DROP NOT NULL');

    }

    public function safeDown()
    {
        $this->execute('ALTER TABLE {{%device_category}} ALTER  COLUMN [[tree]] SET NOT NULL');
    }
}
