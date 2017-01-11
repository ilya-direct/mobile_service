<?php

namespace common\models\ar;

use Yii;
use yii\base\Exception;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\Transaction;
use yii\web\IdentityInterface;
use common\components\behaviors\RevisionBehavior;
use common\components\db\ActiveRecord;
use common\components\db\ActiveQuery;
use linslin\yii2\curl\Curl;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property integer $id
 * @property string $uid
 * @property string $created_at
 * @property integer $order_status_id
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
 * // Данные клиента
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $phone
 * @property string $email
 * @property string $address
 * @property float $address_longitude Долгота
 * @property float $address_latitude Широта
 *
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
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
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
                    'address',
                    'address_latitude',
                    'address_longitude',
                    'email',
                    'first_name',
                    'last_name',
                    'middle_name',
                    'phone',
                ]
            ],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_WORKER] = [
            'order_status_id',
            'client_lead',
            'comment',
        ];

        $scenarios[self::SCENARIO_OPERATOR] = [
            'order_status_id',
            'client_lead',
            'comment',
            'worker_id',
            'preferable_date',
            'time_from',
            'time_to',
            'client_lead',
            'address',
            'email',
            'first_name',
            'last_name',
            'middle_name',
            'phone',
        ];

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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_status_id'], 'required'],
            [['created_at', 'preferable_date', 'time_from', 'time_to'], 'safe'],
            [['order_status_id'], 'integer'],
            [['client_lead', 'comment', 'referer'], 'string', 'max' => 255],
            [['order_status_id'], function ($attribute, $params) {
                $oldStatus = $this->oldAttributes ? $this->oldAttributes[$attribute] : null;
                $statusIds = array_keys(OrderStatus::availableStatuses($oldStatus, Yii::$app->user->identity->role));
                if (!in_array($this->$attribute, $statusIds)) {
                    $this->addError($attribute, 'Неверный статус');
                }
            }],
            ['preferable_date', 'date', 'format' => 'dd.mm.yyyy'],
            [['time_from', 'time_to'], function ($attribute, $params) {
                $value = $this->$attribute;
                if (!preg_match('/^\d{1,2}:\d{1,2}:\d{1,2}|\d{1,2}:\d{1,2}|\d{1,2}$/', $value)) {
                    $this->addError($attribute, 'Формат XX:XX');
                    return;
                }

                // Если узазан только час
                if (is_numeric($value)) {
                    $value = $value . ':00';
                }
                $time = strtotime($value);
                if (!$time) {
                    $this->addError($attribute, 'Неправильно указано время');
                } else {
                    $this->$attribute = date('H:i:s', $time);
                }
            }],
            ['time_to', 'compare', 'compareAttribute' => 'time_from', 'operator' => '>=', 'message' => '< время с'],
            [['preferable_date', 'time_from', 'time_to', 'client_lead', 'comment'], 'default'],
            ['order_status_id', 'filter', 'filter' => 'intval'],
            ['worker_id', 'in', 'range' => array_keys(User::getWorkersList())],
            ['worker_id', 'required', 'when' => function(/** @var self $model */$model) {
                return $model->order_status_id == OrderStatus::STATUS_DELEGATED;
            },  'enableClientValidation' => false],
            ['worker_id', 'filter', 'filter' => function ($value) {
                $workerNeeded = $this->order_status_id == OrderStatus::STATUS_DELEGATED;
                $oldValue = isset($this->oldAttributes['worker_id'])
                    ? $this->oldAttributes['worker_id']
                    : null;

                return $workerNeeded ? $value : $oldValue;
            }],
            [['first_name', 'phone'], 'required'],
            [['first_name', 'last_name', 'middle_name'], 'string', 'max' => 30],
            ['phone', 'match', 'pattern' => '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/', 'message' => 'Формат +7 (XXX) XXX-XX-XX'],
            ['phone', 'filter', 'filter' => function ($value) {
                $newValue = '+' . preg_replace('/\D/', '', $value);
                return $newValue;
            }],
            ['email', 'string', 'max' => 50],
            ['email', 'email'],
            ['address', 'string', 'max' => 255],
            ['address', 'validateAddress', 'skipOnEmpty' => false],
            [['last_name', 'middle_name', 'address', 'email'], 'default'],
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
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'middle_name' => 'Отчество',
            'phone' => 'Телефон',
            'email' => 'Email',
            'address' => 'Адрес проживания',
        ];
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
            if ($this->order_provider_id !== OrderProvider::PROVIDER_ADMIN_PANEL) {
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

    public function validateAddress($attribute)
    {
        if (empty($this->getDirtyAttributes([$attribute]))) {

            return null;
        }

        $value = trim($this->$attribute);

        if (empty($value)) {
            $this->address = null;
            $this->address_longitude = null;
            $this->address_latitude = null;

            return null;
        }

        $yandexGeocoder = new Curl();
        // если у библиотеки Curl не установлены сертификаты раскоментировать
        $yandexGeocoder->setOption(CURLOPT_SSL_VERIFYPEER, 0);
        try {
            $result = $yandexGeocoder->get(
                'https://geocode-maps.yandex.ru/1.x/?&format=json&results=1&bbox=36.044480,54.983701~38.835007,56.495778&geocode=' . urlencode($value),
                false);
        } catch (Exception $e) {
            $this->addError($attribute, 'Не удалось подключиться к Яндекс.Картам');

            return null;
        }
        if (!$result) {
            $this->addError($attribute, 'Не удалось проверить адрес (timeout)');

            return null;
        }

        $yandexGeocoderResult = $result['response']['GeoObjectCollection'];
        if ($yandexGeocoderResult['metaDataProperty']['GeocoderResponseMetaData']['found'] != "0") {
            $yandexGeoObject = $yandexGeocoderResult['featureMember'][0]['GeoObject'];
            $meta = $yandexGeoObject['metaDataProperty']['GeocoderMetaData'];
            if ($meta['kind'] == 'house') {
                if ($meta['precision'] == 'exact') {
                    $address = $meta['text'];
                    $this->$attribute = $address;
                    list($longitude, $latitude) = explode(' ', $yandexGeoObject['Point']['pos']);
                    $this->address_longitude = (float)$longitude;
                    $this->address_latitude = (float)$latitude;

                    return null;
                } else {
                    $this->addError($attribute, 'Данный дом не найден на Яндекс.Картах');
                }
            } else {
                $this->addError($attribute, 'Адрес должен быть с точностью до дома');
            }
        } else {
            $this->addError($attribute, 'Адрес не найден на Яндекс.Картах');
        }

        return null;
    }

    /**
     * Создание нового заказа
     * @param $providerId
     * @param $validate
     * @param $deviceAssignIds array|string
     * @return boolean
     */
    public function create($providerId, $validate = true, $deviceAssignIds = null)
    {
        if (!$validate || $this->validate()) {
            $this->order_status_id = OrderStatus::STATUS_NEW;
            $this->order_provider_id = $providerId;
            $this->save(false);

            if ($deviceAssignIds) {
                if (!is_array($deviceAssignIds)) {
                    $deviceAssignIds = [$deviceAssignIds];
                }

                foreach ($deviceAssignIds as $deviceAssignId) {
                    $orderService = new OrderService();
                    $orderService->device_assign_id = $deviceAssignId;
                    $orderService->order_id = $this->id;
                    $orderService->save(false);
                }

            }

            return true;
        }

        return false;
    }
}
