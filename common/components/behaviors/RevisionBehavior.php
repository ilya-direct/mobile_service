<?php

namespace common\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Exception;
use yii\db\ActiveRecord;
use common\models\ar\Revision;
use common\models\ar\RevisionField;
use common\models\ar\RevisionTable;
use common\models\ar\RevisionValueType;

/**
 * Class RevisionBehavior
 * @property ActiveRecord $owner
 */
class RevisionBehavior extends Behavior
{
    /** @var array Аттрибуты, которые находятся под ревизией */
    public $attributes = [];
    /** @var array $atributes, которые были изменены */
    private $changedAttributes = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'fillRevision',
            ActiveRecord::EVENT_AFTER_INSERT => 'fillRevision',
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteRevision',

            ActiveRecord::EVENT_BEFORE_INSERT => 'beginTransaction',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beginTransaction',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beginTransaction',
        ];
    }

    public function beginTransaction()
    {
        Yii::$app->db->beginTransaction();
        $this->changedAttributes = $this->owner->getDirtyAttributes($this->attributes);
    }

    /**
     * Создание ревизии по событию
     * @param $event
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function fillRevision($event)
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        $revisionTableId = RevisionTable::findOrCreateReturnScalar(['name' => $owner->getTableSchema()->fullName]);

        if ($event->name == ActiveRecord::EVENT_AFTER_UPDATE) {
            $operationType = Revision::OPERATION_UPDATE;
            // Проверка все ли изменнённые поля были проинициализированы консольным скриптом
            $initializedFields = (integer)Revision::find()
                ->select('revision_field_id')
                ->joinWith('revisionField')
                ->distinct()
                ->where([
                    'revision_table_id' =>  $revisionTableId,
                    'record_id' => $owner->id,
                    'operation_type' => Revision::OPERATION_INSERT,
                    'revision_field.name' => array_keys($this->changedAttributes),
                ])
                ->count();
            if (count($this->changedAttributes) != $initializedFields) {
                throw new Exception('Not all fields under revision. Revision_table_id:' . $revisionTableId . ', Record_id:' . $owner->id);
            }
        } elseif ($event->name == ActiveRecord::EVENT_AFTER_INSERT) {
            $operationType = Revision::OPERATION_INSERT;
        } else {
            throw new Exception('Undefined Revision Operation ' . $event->name);
        }

        foreach ($this->changedAttributes as $changedAttribute => $value) {
            $fieldId = RevisionField::findOrCreateReturnScalar(['name' => $changedAttribute]);
            $typeId = RevisionValueType::findOrCreateReturnScalar(['name' => gettype($value)]);
            (new Revision([
                'revision_table_id' => $revisionTableId,
                'revision_field_id' => $fieldId,
                'revision_value_type_id' => $typeId,
                'record_id' => $owner->id,
                'value' => $value,
                'operation_type' => $operationType,
            ]))->save(false);
        }

        Yii::$app->db->transaction->commit();
    }

    /**
     * Удаление всех ревизий по записи
     *
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function deleteRevision()
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        /** @var RevisionTable $revisionTable */
        $revisionTable = RevisionTable::findOne(['name' => $owner->getTableSchema()->fullName]);
        // Условие нужно, потому что на ревизия была применена не сразу(некоторые таблицы вообще не присутствовали в ревизии на момент удаление записи)
        if (!is_null($revisionTable)) {
            Revision::deleteAll([
                'revision_table_id' => $revisionTable->id,
                'record_id' => $owner->id,
            ]);
        }

        Yii::$app->db->transaction->commit();
    }

    /**
     * Значение атрибута в определённое время
     *
     * @param string $attribute
     * @param string $dateTime время, на которое нужно значение
     * @return bool|mixed|string
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function revisionValue($attribute, $dateTime)
    {
        if (in_array($attribute, $this->attributes)) {
            $tableName = $this->owner->getTableSchema()->fullName;
            $value = Revision::getValue($tableName, $this->owner->id, $attribute, $dateTime);
            return $value;
        } else {
            throw new Exception('Attribute is not under revision in config');
        }

    }
}
