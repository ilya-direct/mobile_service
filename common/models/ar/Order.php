<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\Transaction;
use yii\web\IdentityInterface;
use common\components\behaviors\RevisionBehavior;
use common\components\db\ActiveRecord;
use common\components\db\ActiveQuery;

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
 * @property string $session_id id сессии заказавшего
 * @property integer $operator_id id оператора
 * @property integer $worker_id id мастера
 *
 * @property OrderPerson $orderPerson
 * @property OrderProvider $orderProvider
 * @property OrderStatus $orderStatus
 * @property OrderService[] $orderServices
 */
class Order extends ActiveRecord
{
    const SCENARIO_WORKER = 'worker';
    const SCENARIO_OPERATOR = 'operator';

    /** @var  Transaction */
    private $transaction; // Для выполнения межмодельных транзакций

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
                    'operator_id',
                    'worker_id',
                ]
            ],
        ];
    }

    public function scenarios()
    {
        $scenarios[self::SCENARIO_WORKER] = ['order_status_id', 'client_lead', 'comment'];
        $scenarios[self::SCENARIO_OPERATOR] = ['order_status_id', 'client_lead', 'comment', 'worker_id', 'preferable_date', 'time_from', 'time_to', 'client_lead'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * TODO: Убрать из валидации client_comment, так как он не редактируется в карточке заказа
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_status_id'], 'required'],
            [['created_at', 'preferable_date', 'time_from', 'time_to'], 'safe'],
            [['order_status_id'], 'integer'],
            [['client_lead', 'comment', 'referer', 'client_comment'], 'string', 'max' => 255],
            [['order_status_id'], function ($attribute, $params) {
                $oldStatus = $this->oldAttributes ? $this->oldAttributes[$attribute] : null;
                $statusIds = array_keys(OrderStatus::availableStatuses($oldStatus, Yii::$app->user->identity->role));
                if (!in_array($this->$attribute, $statusIds)) {
                    $this->addError($attribute, 'Неверный статус');
                }
            }],
            ['preferable_date', 'date', 'format' => 'dd.mm.yyyy'],
            [['time_from', 'time_to'], function ($attribute, $params) {
                if (!preg_match('/^\d{1,2}:\d{1,2}:\d{1,2}|\d{1,2}:\d{1,2}$/', $this->$attribute)) {
                    $this->addError($attribute, 'Формат XX:XX');
                    return;
                }
                $time = strtotime($this->$attribute);
                if (!$time) {
                    $this->addError($attribute, 'Неправильно указано время');
                } else {
                    $this->$attribute = date('H:i:s', $time);
                }
            }],
            ['time_to', 'compare', 'compareAttribute' => 'time_from', 'operator' => '>=', 'message' => '< время с'],
            [['preferable_date', 'time_from', 'time_to', 'client_lead', 'comment', 'client_comment'], 'default'],
            ['order_status_id', 'filter', 'filter' => 'intval'],
            ['worker_id', 'in', 'range' => array_keys(User::getWorkersList())],
            ['worker_id', 'required', 'when' => function(/** @var self $model */$model) {
                return OrderStatus::getAlias($model->order_status_id) == OrderStatus::STATUS_DELEGATED;
            },  'enableClientValidation' => false],
            ['worker_id', 'filter', 'filter' => function ($value) {
                $workerNeeded = OrderStatus::getAlias($this->order_status_id) == OrderStatus::STATUS_DELEGATED;
                return $workerNeeded ? $value : null;
            }],
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
            'worker_id' => 'Мастер по заказу',
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
        $this->preferable_date = $this->preferable_date ? date('Y-m-d', strtotime($this->preferable_date)) : null;

        if ($insert) {
            $this->referer = empty(Yii::$app->session->get('referer')) ? null : Yii::$app->session->get('referer');
            $this->ip = Yii::$app->request->userIP;
            $this->session_id = Yii::$app->session->id;
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

            // Отправка письма о новом заказе, если заказ с фронта
            if ($this->order_provider_id !== OrderProvider::getId(OrderProvider::PROVIDER_ADMIN_PANEL)) {
                Yii::$app->mailer->compose(['html' => 'new-order'], [
                    'link' => Yii::$app->urlManagerBackend->createAbsoluteUrl(['order/view', 'id' => $this->id]),
                    'uid' => $this->uid,
                ])->setFrom([Yii::$app->params['appEmail'] => Yii::$app->params['companyName']])
                    ->setTo(Yii::$app->params['adminEmail'])
                    ->setSubject('Новый заказ ' . $this->uid)
                    ->send();
            }
        }
    }

    public function afterFind()
    {
        if (!empty($this->preferable_date)) {
            $this->preferable_date = Yii::$app->formatter->asDate($this->preferable_date);
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        OrderPerson::findOne($this->order_person_id)->delete(false);
        OrderService::deleteAll(['order_id' => $this->id], false);
        $this->transaction->commit();
    }

    public function delete($soft = true)
    {
        if ($soft) {
            $this->deleted = true;
            return $this->update(false, ['deleted']);
        }
        $this->transaction = Yii::$app->db->beginTransaction();
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

    /**
     * Поиск только собственных заказов
     *
     * @param $user IdentityInterface|User
     * @return ActiveQuery
     */
    public static function findOwnOrders($user)
    {
        $query = parent::find();

        switch ($user->role) {
            case User::ROLE_WORKER:
                $query->andWhere(['worker_id' => $user->id]);
                break;
            case User::ROLE_OPERATOR:
                $query->andWhere(['or', ['operator_id' => $user->id], ['operator_id' => null]]);
                break;
        }

        return $query;
    }

}
