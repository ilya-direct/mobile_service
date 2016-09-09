<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use common\components\behaviors\RevisionBehavior;
use common\components\db\ActiveRecord;

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
 * @property string $client_lead
 * @property string $ip
 * @property boolean $deleted
 * @property boolean $user_agent
 * @property integer $device_provider_id  id устройства, со страницы которого был сделан заказ
 * @property string $client_comment комментарий клиента (заполняется только клиентом при оформлении заказа)
 *
 * @property OrderPerson $orderPerson
 * @property OrderProvider $orderProvider
 * @property OrderStatus $orderStatus
 * @property OrderService[] $orderServices
 */
class Order extends ActiveRecord
{
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
            'blameable' => [
                'class' => BlameableBehavior::className(),
            ],
            'attribute' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['deleted'],
                ],
                'value' => false,
            ],
            'revision' => [
                'class' => RevisionBehavior::className(),
                'attributes' => [
                    'comment',
                    'client_lead',
                    'deleted',
                    'order_status_id',
                    'preferable_date',
                    'time_from',
                    'time_to',
                ]
            ],
        ];
    }

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
            [['order_status_id'], 'required'],
            ['uid', 'unique'],
            [['created_at', 'preferable_date', 'time_from', 'time_to'], 'safe'],
            [['order_status_id'], 'integer'],
            [['uid'], 'string', 'max' => 10],
            [['client_lead', 'comment', 'referer'], 'string', 'max' => 255],
            [['order_status_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrderStatus::className(), 'targetAttribute' => ['order_status_id' => 'id']],
            ['preferable_date', 'date', 'format' => 'dd.mm.yyyy'],
            [['time_from', 'time_to'], 'date', 'format' => 'H:i', 'message' => 'XX:XX'],
            ['time_to', 'compare', 'compareAttribute' => 'time_from', 'operator' => '>=', 'message' => '< время с'],
            [['preferable_date', 'time_from', 'time_to', 'client_lead', 'comment'], 'default'],
            ['order_status_id', 'filter', 'filter' => 'intval'],
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
            'created_at' => 'Время создания',
            'order_status_id' => 'Статус заказа',
            'order_person_id' => 'Данные заказавшего',
            'order_provider_id' => 'Источник заказа',
            'preferable_date' => 'Желаемая дата ремонта',
            'time_from' => 'Время с',
            'time_to' => 'Время по',
            'comment' => 'Комментарий к заказу',
            'referer' => 'Откуда был заход на сайт(referer)',
            'client_lead' => 'Откуда узнали про нас?',
            'ip' => 'IP',
            'created_by' => 'Кем создан(id)',
            'updated_at' => 'Время изменения',
            'updated_by' => 'Кем изменён(id)',
            'user_agent' => 'User Agent',
            'client_comment' => 'Комментарий клиента',
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

    private function setUid()
    {
        $initCounter = 330120;
        $uidNumber = $initCounter + $this->id;
        $this->uid = 'U-' . $uidNumber;

        return $this->uid;
    }

    public function getOrderServices()
    {
        return $this->hasMany(OrderService::className(), ['order_id' => 'id']);
    }

    public function beforeSave($insert)
    {
        $this->time_from = $this->time_from ? date('H:i:s', strtotime($this->time_from)) : null;
        $this->time_to = $this->time_to ? date('H:i:s', strtotime($this->time_to)) : null;
        $this->preferable_date = $this->preferable_date ? date('Y-m-d', strtotime($this->preferable_date)) : null;

        if ($insert) {
            $this->referer = empty(Yii::$app->session->get('referer')) ? null : Yii::$app->session->get('referer');
            $this->ip = Yii::$app->request->userIP;
            $this->user_agent = Yii::$app->request->userAgent;
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert && !$this->uid) {
            $this->setUid();
            $this->save(false);
        }
    }

    public function afterFind()
    {
        if (!empty($this->preferable_date)) {
            $this->preferable_date = Yii::$app->formatter->asDate($this->preferable_date);
        }
        if (!empty($this->time_from)) {
            $this->time_from = substr($this->time_from, 0, 5);
        }
        if (!empty($this->time_to)) {
            $this->time_to = substr($this->time_to, 0, 5);
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        OrderPerson::findOne($this->order_person_id)->delete(false);
        Yii::$app->db->transaction->commit();
    }

    public function delete($soft = true)
    {
        if ($soft) {
            $this->deleted = true;
            return $this->update(false, ['deleted']);
        }
        Yii::$app->db->beginTransaction();
        return parent::delete();
    }

    /**
     * Нельзя удалять заказы массово, так как используется тригер на удаление у модели
     * @param string $condition
     * @param array $params
     * @return bool
     */
    public static function  deleteAll($condition = '', $params = [])
    {
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdater()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeviceProvider()
    {
        return $this->hasOne(Device::className(), ['id' => 'device_provider_id']);
    }

}
