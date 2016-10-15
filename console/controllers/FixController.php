<?php

namespace console\controllers;

use Yii;
use yii\base\ErrorException;
use yii\console\Controller;
use yii\helpers\Console;
use yii\db\Expression;
use common\models\ar\Device;
use common\models\ar\DeviceAssign;
use common\models\ar\DeviceCategory;
use common\models\ar\News;
use common\models\ar\Order;
use common\models\ar\OrderProvider;
use common\models\ar\OrderService;
use common\models\ar\OrderStatus;
use common\models\ar\Revision;
use common\models\ar\RevisionField;
use common\models\ar\RevisionTable;
use common\models\ar\RevisionValueType;
use common\models\ar\Service;
use common\models\ar\User;
use common\models\ar\Vendor;

class FixController extends Controller
{
    protected $tables = [];
    protected $consoleUserId;

    public function init()
    {
        $this->tables = [
            Device::className(),
            DeviceAssign::className(),
            DeviceCategory::className(),
            Order::className(),
            OrderService::className(),
            News::className(),
            Service::className(),
            User::className(),
            Vendor::className(),
        ];

        $user = User::findOne(['email' => 'console@console.ru']);
        if (is_null($user)) {
            $user = (new User([
                'first_name' => 'console',
                'last_name' => 'console',
                'auth_key' => '',
                'email' => 'console@console.ru',
                'phone' => '+77777777777',
                'enabled' => false,
            ]));
            $user->save(false);
        }

        $this->consoleUserId = $user->id;
    }

    /**
     * Исправляет значения последовательностей, делая их максимальными
     * @throws \yii\db\Exception
     */
    public function actionSequence()
    {
        foreach ($this->tables as $table) {
            $tableName = $table::getTableSchema()->name;
            $column = 'id';
            Yii::$app->db->createCommand("SELECT setval('{$tableName}_{$column}_seq',( SELECT MAX([[$column]]) FROM {$tableName}))")->execute();
        }

    }


    /**
     * Создаёт начальную ревизию всех полей, которые не были проинициализированны
     *
     * @param string $table конкретная таблица
     * @param string $attribute конкретное поле
     * @throws ErrorException
     */
    public function actionRevision($table = null, $attribute = null)
    {
        $tables = $this->tables;
        if ($table) {
            if (in_array($table, $tables)) {
                $tables = [$table];
            } else {
                throw new ErrorException('Undefined table class name : ' . $table);
            }
        }
        foreach ($tables as $table) {

            $models = $table::find()->all();
            foreach ($models as $model) {
                if (!isset($model->behaviors['revision'])) {
                    continue;
                }
                $revisionTableId = RevisionTable::findOrCreateReturnScalar(['name' => $table::getTableSchema()->name]);
                $attributes = $model->behaviors['revision']->attributes;
                if ($attribute) {
                    $attributes = array_intersect($attributes, [$attribute]);
                }
                $revisionTableName = Revision::tableName();
                $versionedFields = Revision::find()
                    ->select(RevisionField::tableName() . '.name')
                    ->joinWith('revisionField')
                    ->where([
                        $revisionTableName . '.revision_table_id' => $revisionTableId,
                        $revisionTableName . '.operation_type' => Revision::OPERATION_INSERT,
                        $revisionTableName . '.record_id' => $model->id,
                        RevisionField::tableName() . '.name' => $attributes,
                    ])
                    ->distinct()
                    ->column();

                $notVersionedFields = array_diff($attributes, $versionedFields);

                foreach ($notVersionedFields as $field) {
                    Yii::$app->db->createCommand()
                        ->insert($revisionTableName, [
                                'revision_table_id' => $revisionTableId,
                                'revision_field_id' => RevisionField::findOrCreateReturnScalar(['name' => $field]),
                                'record_id' => $model->id,
                                'revision_value_type_id' => RevisionValueType::findOrCreateReturnScalar(['name' => gettype($model->{$field})]),
                                'value' => $model->{$field},
                                'user_id' => $this->consoleUserId,
                                'operation_type' => Revision::OPERATION_INSERT,
                                'created_at' => new Expression('NOW()'),
                            ]
                        )->execute();
                }
            }
        }
    }

    /**
     * Заполнение названий источников заказов в БД
     */
    public function actionFillOrderProviders()
    {
        $providers = OrderProvider::providerLabels();

        foreach ($providers as $providerId => $name) {
            /** @var OrderProvider $orderProvider */
            $orderProvider = OrderProvider::findOrNew(['name' => $name]);
            $orderProvider->id = $providerId;
            if ($orderProvider->dirtyAttributes) {
                Console::output('Provider ' . $orderProvider->name . ' has changed');
            }
            $orderProvider->save(false);

        }
        Console::output('Providers have filled');
    }

    /**
     * Заполнение статусов заказов в БД
     */
    public function actionFillOrderStatuses()
    {
        $statuses = OrderStatus::statusLabels();

        foreach ($statuses as $statusId => $name) {
            /** @var OrderStatus $orderStatus */
            $orderStatus = OrderStatus::findOrNew(['name' => $name]);
            $orderStatus->id = $statusId;
            if ($orderStatus->dirtyAttributes) {
                Console::output('Status ' . $orderStatus->name . ' has changed');
            }
            $orderStatus->save(false);

        }
        Console::output('Statuses have filled');
    }
    
}
