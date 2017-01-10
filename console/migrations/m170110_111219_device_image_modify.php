<?php

use yii\db\Migration;
use common\models\ar\Device;

class m170110_111219_device_image_modify extends Migration
{
    public function safeUp()
    {
        $this->renameColumn(Device::tableName(), 'image', 'image_name');

    }

    public function safeDown()
    {
        $this->renameColumn(Device::tableName(), 'image_name', 'image');
    }
}
