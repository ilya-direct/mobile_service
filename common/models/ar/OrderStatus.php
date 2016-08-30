<?php

namespace common\models\ar;

use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "{{%order_status}}".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Order[] $orders
 */
class OrderStatus extends \yii\db\ActiveRecord
{
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
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 30],
        ];
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


    private static $statuses = [
        'new' => 'Новый',
    ];

    public static function get($statusAlias)
    {
        $status = self::$statuses[$statusAlias];
        $status_id = self::find()
            ->select('id')
            ->where(['name' => $status])
            ->scalar();
        if (!$status_id) {
            throw new Exception('Undefined order status');
        }
        return $status_id;
    }

    public static function getList()
    {
        $list =self::find()
            ->select('name')
            ->indexBy('id')
            ->orderBy('id')
            ->column();

        return $list;
    }
}
