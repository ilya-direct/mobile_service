<?php

namespace console\controllers;

use Yii;
use yii\base\ErrorException;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\Json;
use common\models\ar\Device;
use common\models\ar\DeviceAssign;
use common\models\ar\DeviceCategory;
use common\models\ar\News;
use common\models\ar\Order;
use common\models\ar\OrderProvider;
use common\models\ar\OrderService;
use common\models\ar\OrderStatus;
use common\models\ar\Revision;
use common\models\ar\RevisionOperationType;
use common\models\ar\RevisionRecord;
use common\models\ar\RevisionTable;
use common\models\ar\Service;
use common\models\ar\User;
use common\models\ar\Vendor;

class FixController extends Controller
{
    protected $tables = [];

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
    public function actionRevision($table = null)
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
            Console::output('Revisioning table ' . $table::tableName());
            $models = $table::find()->all();
            foreach ($models as $model) {
                if (!isset($model->behaviors['revision'])) {
                    continue;
                }
                $revisionTableId = RevisionTable::findOrCreateReturnScalar(['name' => $table::getTableSchema()->name]);
                $searchAttributes = [
                    'revision_table_id' => $revisionTableId,
                    'record_id' => $model->id,
                    'revision_operation_type_id' => RevisionOperationType::TYPE_INSERT,
                ];
                $revisionRecord = RevisionRecord::findOne($searchAttributes);
                
                if (!$revisionRecord) {
                    /** @var Revision $maxTimeModel */
                    $maxTimeModel = Revision::find()
                        ->where([
                            'revision_table_id' => $revisionTableId,
                            'record_id' => $model->id,
                        ])
                        ->orderBy(['created_at' => SORT_DESC])
                        ->one();
                    
                    $revisionRecord = new RevisionRecord(
                        $searchAttributes + [
                            'value' => Json::encode($model->attributes),
                            'place' => 'fix/revision'
                        ]
                    );
                    if ($maxTimeModel) {
                        $revisionRecord->detachBehavior('timestamp');
                        $revisionRecord->created_at = $maxTimeModel->created_at;
                    }
                    
                    $revisionRecord->save();
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
    
    /**
     * Заполнение типов ревизий в БД
     */
    public function actionFillRevisionOperationTypes()
    {
        $types = RevisionOperationType::typeNames();
        foreach ($types as $typeId => $name) {
            /** @var RevisionOperationType $revision */
            $revision = RevisionOperationType::findOrNew(['id' => $typeId]);
            $revision->name = $name;
            if ($revision->dirtyAttributes) {
                Console::output('Type ' . $revision->name . ' has changed');
            }
            $revision->save(false);
        }
        
        Console::output('Types have filled');
    }
    
}
