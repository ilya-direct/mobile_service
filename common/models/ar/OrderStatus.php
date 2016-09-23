<?php

namespace common\models\ar;

use Yii;
use yii\base\Exception;
use common\components\db\ActiveRecord;

/**
 * This is the model class for table "{{%order_status}}".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Order[] $orders
 */
class OrderStatus extends ActiveRecord
{
    const STATUS_NEW = 'new';
    const STATUS_CONFIRMED = 'confirmed';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_status}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Статус заказа',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['order_status_id' => 'id']);
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_NEW => 'Новый',
            self::STATUS_CONFIRMED => 'Подтверждён',
        ];
    }

    public static function getId($status)
    {
        $statuses = self::getStatuses();

        if (isset($statuses[$status])) {
            return self::findOrCreateReturnScalar(['name' => $statuses[$status]]);
        } else {
            throw new Exception('Undefined OderStatus: ' . $status);
        }
    }

}
