<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\db\Expression;
use common\models\ar\Device;
use common\models\ar\DeviceAssign;
use common\models\ar\Order;
use common\models\ar\OrderPerson;
use common\models\ar\OrderService;
use common\models\ar\Service;
use common\models\ar\News;
use common\models\ar\Revision;
use common\models\ar\RevisionField;
use common\models\ar\RevisionTable;
use common\models\ar\RevisionValueType;
use backend\models\ar\Admin;

class RevisionGenerateController extends Controller
{
    private $admin_id;

    public function init()
    {
        parent::init();
        $admin = Admin::findByUsername('console');

        if (is_null($admin)) {
            $admin = (new Admin([
                'username' => 'console',
                'first_name' => 'console',
                'last_name' => 'console',
                'auth_key' => '',
                'password_hash' => '',
                'email' => 'console@console.ru',
                'enabled' => true,
            ]));
            $admin->save(false);
        }

        $this->admin_id = $admin->id;
    }

    /**
     * Создаёт начальную ревизию всех полей, которые не были проинициализированны
     *
     * @throws \yii\db\Exception
     */
    public function actionIndex()
    {
        $tables = [
            Device::className(),
            DeviceAssign::className(),
            Order::className(),
            OrderPerson::className(),
            OrderService::className(),
            News::className(),
            Service::className(),
        ];

        foreach ($tables as $table) {

            $models = $table::find()->all();
            $revisionTableId = RevisionTable::findOrCreateReturnScalar(['name' => $table::getTableSchema()->fullName]);
            foreach ($models as $model) {
                if (!isset($model->behaviors['revision'])) {
                    continue;
                }
                $attributes = $model->behaviors['revision']->attributes;

                $versionedFields = Revision::find()
                    ->select('revision_field.name')
                    ->joinWith('revisionField')
                    ->where([
                        'revision.revision_table_id' => $revisionTableId,
                        'revision.operation_type' => Revision::OPERATION_INSERT,
                        'revision.record_id' => $model->id,
                        'revision_field.name' => $attributes,
                    ])
                    ->distinct()
                    ->column();

                $notVersionedFields = array_diff($attributes, $versionedFields);

                foreach ($notVersionedFields as $field) {
                    Yii::$app->db->createCommand()
                        ->insert('{{%revision}}', [
                                'revision_table_id' => $revisionTableId,
                                'revision_field_id' => RevisionField::findOrCreateReturnScalar(['name' => $field]),
                                'record_id' => $model->id,
                                'revision_value_type_id' => RevisionValueType::findOrCreateReturnScalar(['name' => gettype($model->{$field})]),
                                'value' => $model->{$field},
                                'admin_id' => $this->admin_id,
                                'operation_type' => Revision::OPERATION_INSERT,
                                'created_at' => new Expression('NOW()'),
                            ]
                        )->execute();

                }
            }
        }
    }
    
}
