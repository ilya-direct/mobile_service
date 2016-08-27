<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property integer $id
 * @property string $uid
 * @property string $created_at
 * @property integer $order_status_id
 * @property integer $order_person_id
 * @property integer $order_provider_id
 * @property string $preferable_date
 * @property string $time_from
 * @property string $time_to
 * @property string $comment
 * @property string $referer
 *
 * @property OrderPerson $orderPerson
 * @property OrderProvider $orderProvider
 * @property OrderStatus $orderStatus
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'order_status_id', 'order_person_id', 'order_provider_id'], 'required'],
            [['created_at', 'preferable_date', 'time_from', 'time_to'], 'safe'],
            [['order_status_id', 'order_person_id', 'order_provider_id'], 'integer'],
            [['uid'], 'string', 'max' => 10],
            [['comment', 'referer'], 'string', 'max' => 255],
            [['order_person_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrderPerson::className(), 'targetAttribute' => ['order_person_id' => 'id']],
            [['order_provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrderProvider::className(), 'targetAttribute' => ['order_provider_id' => 'id']],
            [['order_status_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrderStatus::className(), 'targetAttribute' => ['order_status_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Номер заказа',
            'created_at' => 'Created At',
            'order_status_id' => 'Статус заказа',
            'order_person_id' => 'Данные заказавшего',
            'order_provider_id' => 'Источник заказа',
            'preferable_date' => 'Желаемая дата ремонта',
            'time_from' => 'Время с',
            'time_to' => 'Время по',
            'comment' => 'Комментарий к заказу',
            'referer' => 'Откуда был заход на сайт',
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderPerson()
    {
        return $this->hasOne(OrderPerson::className(), ['id' => 'order_person_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderProvider()
    {
        return $this->hasOne(OrderProvider::className(), ['id' => 'order_provider_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderStatus()
    {
        return $this->hasOne(OrderStatus::className(), ['id' => 'order_status_id']);
    }

    public function setUid()
    {
        $initCounter = 330120;
        $uidNumber = $initCounter + $this->id;
        $this->uid = 'U-' . $uidNumber;

        return $this->uid;
    }
}
