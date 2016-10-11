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
    const STATUS_NULL = 'null';
    const STATUS_NEW = 'new';
    const STATUS_ANNULLED = 'annulled';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_DELEGATED = 'delegated'; // назначен мастер
    const STATUS_AMENDING = 'amending';
    const STATUS_WRONG = 'wrong';
    const STATUS_COMPLETED = 'completed';
    const STATUS_PAID = 'paid';
    const STATUS_NOT_PAID = 'not_paid';

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

    public static function getId($status)
    {
        $statuses = self::statusLabels();

        if (isset($statuses[$status])) {
            return self::findOrCreateReturnScalar(['name' => $statuses[$status]]);
        } else {
            throw new Exception('Undefined OderStatus: ' . $status);
        }
    }

    public static function getName($id)
    {
        $status = self::findOne($id);

        if ($status) {
            return $status->name;
        } else {
            throw new Exception('Undefined OderStatusId: ' . $id);
        }
    }

    public static function getAlias($id)
    {
        $status = self::findOne($id);

        if ($status) {
            return array_search($status->name, self::statusLabels());
        } else {
            throw new Exception('Undefined OderStatusId: ' . $id);
        }
    }

    private static function statusTransitions()
    {
        return [
            User::ROLE_ADMIN => [
                self::STATUS_NULL => [
                    self::STATUS_NEW,
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
                /*self::STATUS_PAID => [
                    self::STATUS_PAID,
                ],*/
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
    private static function statusLabels($role = null)
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
            $labels[self::STATUS_DELEGATED] = 'Новый *';
            $labels[self::STATUS_CONFIRMED] = 'Отказ от заказа';
        }

        return $labels;
    }


    public static function availableStatuses($statusId, $role) {
        $defaultStatusLabels = self::statusLabels();
        if ($statusId) {
            $statusName = self::getName($statusId);
            $statusAlias = array_search($statusName, $defaultStatusLabels);
        } else {
            $statusAlias = self::STATUS_NULL;
        }

        $availableTransitions = isset(self::statusTransitions()[$role][$statusAlias])
            ? self::statusTransitions()[$role][$statusAlias]
            : [];

        $statusLabels = self::statusLabels($role);
        $statusIds = self::getList();

        $return = [];
        foreach ($availableTransitions as $st) {
            $id = array_search($defaultStatusLabels[$st], $statusIds);

            if (!$id) {
                $newStatus = new self(['name' => $defaultStatusLabels[$st]]);
                $newStatus->save(false);
                $id = $newStatus->id;
            }
            $return[$id] = $statusLabels[$st];
        }

        return $return;
    }

}
