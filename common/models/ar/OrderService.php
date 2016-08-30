<?php

namespace common\models\ar;

use Yii;
use common\components\db\ActiveRecord;

/**
 * This is the model class for table "{{%order_service}}".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $device_assign_id
 *
 * @property DeviceAssign $deviceAssign
 * @property Order $order
 */
class OrderService extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_service}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'device_assign_id'], 'required'],
            [['order_id', 'device_assign_id'], 'integer'],
            [['order_id', 'device_assign_id'], 'unique', 'targetAttribute' => ['order_id', 'device_assign_id'], 'message' => 'The combination of Order ID and Device Assign ID has already been taken.'],
            [['device_assign_id'], 'exist', 'skipOnError' => true, 'targetClass' => DeviceAssign::className(), 'targetAttribute' => ['device_assign_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'device_assign_id' => 'Device Assign ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeviceAssign()
    {
        return $this->hasOne(DeviceAssign::className(), ['id' => 'device_assign_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
}
