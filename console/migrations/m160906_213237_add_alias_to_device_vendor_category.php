<?php

use yii\db\Migration;
use yii\helpers\Inflector;
use common\models\ar\Device;
use common\models\ar\DeviceCategory;
use common\models\ar\Vendor;

class m160906_213237_add_alias_to_device_vendor_category extends Migration
{
    private  $user;

    public function init()
    {
        $this->user = \common\models\ar\User::findByUsername('console@console.ru');
    }

    public function safeUp()
    {
        $this->addColumn('{{%device}}', 'alias', $this->string()->unique()->comment('Уникальный параметр для URL'));
        /** @var Device[] $devices */
        $devices = Device::find()->all();
        foreach ($devices as $device) {
            $device->alias = Inflector::slug($device->name);
            $device->save(false);
        }
        $this->execute('ALTER TABLE {{%device}} ALTER COLUMN [[alias]] SET NOT NULL');

        /** @var DeviceCategory[] $categories */
        $categories = DeviceCategory::find()->all();
        foreach ($categories as $category) {
            $category->alias = Inflector::slug($category->name);
            $category->save(false);
        }

        $this->addColumn('{{%vendor}}', 'alias', $this->string()->unique()->comment('Уникальный параметр для URL'));
        /** @var Vendor[] $vendors */
        $vendors = Vendor::find()->all();
        foreach ($vendors as $vendor) {
            $vendor->alias = Inflector::slug($vendor->name);
            $vendor->getBehavior('blameable')->value = $this->user->id;
            $vendor->save(false);
        }
        $this->execute('ALTER TABLE {{%vendor}} ALTER COLUMN [[alias]] SET NOT NULL');

        $this->addColumn('{{%order}}', 'device_provider_id', $this->integer()->comment('id Устройства, со странице которого был заказ'));
        $this->addForeignKey(
            'FK__order__device_provider_id__device__id',
            '{{%order}}',
            'device_provider_id',
            '{{%device}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'device_provider_id');
        $this->dropColumn('{{%device}}', 'alias');
        $this->dropColumn('{{%vendor}}', 'alias');
    }
}
