<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\ar\Device;
use common\models\ar\DeviceAssign;
use common\models\ar\DeviceCategory;
use common\models\ar\News;
use common\models\ar\Order;
use common\models\ar\OrderPerson;
use common\models\ar\OrderService;
use common\models\ar\Service;
use common\models\ar\User;

class FixController extends Controller
{
    public function actionSequence()
    {
        $tables = [
            Device::className(),
            DeviceAssign::className(),
            DeviceCategory::className(),
            Order::className(),
            OrderPerson::className(),
            OrderService::className(),
            News::className(),
            Service::className(),
            User::className(),
        ];

        foreach ($tables as $table) {
            $tableName = $table::getTableSchema()->name;
            $column = 'id';
            Yii::$app->db->createCommand("SELECT setval('{$tableName}_{$column}_seq',( SELECT MAX([[$column]]) FROM {$tableName}))")->execute();
        }

    }
    
}
