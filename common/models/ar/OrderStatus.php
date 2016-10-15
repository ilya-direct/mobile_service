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
    const STATUS_NULL = 0;
    const STATUS_NEW = 20;
    const STATUS_ANNULLED = 21;
    const STATUS_CONFIRMED = 22;
    const STATUS_DELEGATED = 23; // назначен мастер
    const STATUS_AMENDING = 24;
    const STATUS_WRONG = 25;
    const STATUS_COMPLETED = 26;
    const STATUS_PAID = 27;
    const STATUS_NOT_PAID = 28;

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

    /**
     * Таблица переходов статусов для каждой роли
     * @return array
     */
    private static function statusTransitions()
    {
        return [
            User::ROLE_ADMIN => [
                self::STATUS_NULL => [
                    self::STATUS_AMENDING,
                    self::STATUS_CONFIRMED,
                ],
                self::STATUS_NEW => [
                    self::STATUS_NEW,
                    self::STATUS_AMENDING,
                    self::STATUS_WRONG,
                    self::STATUS_ANNULLED,
                    self::STATUS_CONFIRMED,
                ],
                self::STATUS_AMENDING => [
                    self::STATUS_AMENDING,
                    self::STATUS_CONFIRMED,
                    self::STATUS_ANNULLED,
                ],
                self::STATUS_CONFIRMED => [
                    self::STATUS_CONFIRMED,
                    self::STATUS_ANNULLED,
                    self::STATUS_DELEGATED,
                ],
                self::STATUS_DELEGATED => [
                    self::STATUS_DELEGATED,
                    self::STATUS_CONFIRMED,
                    self::STATUS_ANNULLED,
                    self::STATUS_COMPLETED,
                ],
                self::STATUS_COMPLETED => [
                    self::STATUS_COMPLETED,
                    self::STATUS_PAID,
                    self::STATUS_NOT_PAID,
                ],
                self::STATUS_NOT_PAID => [
                    self::STATUS_NOT_PAID,
                    self::STATUS_PAID,
                ],
                self::STATUS_ANNULLED => [
                    self::STATUS_ANNULLED,
                ],
                self::STATUS_WRONG => [
                    self::STATUS_WRONG
                ],
            ],
            User::ROLE_OPERATOR => [
                self::STATUS_NULL => [
                    self::STATUS_AMENDING,
                    self::STATUS_CONFIRMED,
                ],
                self::STATUS_NEW => [
                    self::STATUS_AMENDING,
                    self::STATUS_WRONG,
                    self::STATUS_ANNULLED,
                    self::STATUS_CONFIRMED,
                ],
                self::STATUS_AMENDING => [
                    self::STATUS_AMENDING,
                    self::STATUS_CONFIRMED,
                    self::STATUS_ANNULLED,
                ],
                self::STATUS_CONFIRMED => [
                    self::STATUS_CONFIRMED,
                    self::STATUS_ANNULLED,
                    self::STATUS_DELEGATED,
                ],
            ],
            User::ROLE_WORKER => [
                self::STATUS_DELEGATED => [
                    self::STATUS_DELEGATED,
                    self::STATUS_CONFIRMED,
                    self::STATUS_ANNULLED,
                    self::STATUS_COMPLETED,
                ],
            ],

        ];
    }

    /**
     * Название статусов в зависимости от роли
     * @param null|string $role
     * @return array
     */
    public static function statusLabels($role = null)
    {
        $labels = [
            self::STATUS_NEW => 'Новый',
            self::STATUS_CONFIRMED => 'Подтверждён',
            self::STATUS_WRONG => 'Ошибка',
            self::STATUS_ANNULLED => 'Анулирован',
            self::STATUS_AMENDING => 'Уточняется',
            self::STATUS_DELEGATED => 'Назначен мастер',
            self::STATUS_COMPLETED => 'Выполнен',
            self::STATUS_PAID => 'Оплачен',
            self::STATUS_NOT_PAID => 'Не оплачен',
        ];

        if ($role === User::ROLE_WORKER) {
            $labels = [
                self::STATUS_ANNULLED => 'Анулирован',
                self::STATUS_COMPLETED => 'Выполнен',
                self::STATUS_PAID => 'Оплачен',
                self::STATUS_NOT_PAID => 'Не оплачен',
                self::STATUS_DELEGATED => 'Новый для мастера',
                self::STATUS_CONFIRMED => 'Отказ от заказа',
            ];
        }

        return $labels;
    }


    /**
     * Возможные переходы статусов для данной роли и текущего статуса [id => name]
     * @param $statusId
     * @param $role
     * @return array
     */
    public static function availableStatuses($statusId, $role)
    {
        if (is_null($statusId)) {
            $statusId = self::STATUS_NULL;
        }

        $availableTransitions = isset(self::statusTransitions()[$role][$statusId])
            ? self::statusTransitions()[$role][$statusId]
            : [];

        $statusLabels = self::statusLabels($role);

        $return = [];
        foreach ($availableTransitions as $availableStatusId) {
            $return[$availableStatusId] = $statusLabels[$availableStatusId];
        }

        return $return;
    }

}
