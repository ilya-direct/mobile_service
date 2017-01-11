<?php

namespace common\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\Transaction;
use yii\helpers\Json;
use yii\base\Exception;
use yii\db\ActiveRecord;
use common\models\ar\Revision;
use common\models\ar\RevisionField;
use common\models\ar\RevisionOperationType;
use common\models\ar\RevisionRecord;
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
    
    /** @var  Transaction */
    private $internalTransaction;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'onBeforeInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'onBeforeUpdate',
            ActiveRecord::EVENT_BEFORE_DELETE => 'onBeforeDelete',
    
            ActiveRecord::EVENT_AFTER_INSERT => 'onAfterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'endTransaction',
            ActiveRecord::EVENT_AFTER_DELETE => 'endTransaction',
        ];
    }
    
    public function onBeforeInsert()
    {
        $this->internalTransaction = Yii::$app->db->beginTransaction();
    }
    
    public function onAfterInsert()
    {
        $this->deleteOrInsertInternal(RevisionOperationType::TYPE_INSERT);
        $this->internalTransaction->commit();
    }

    /**
     * Создание ревизии по событию
     * @param $event
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function onBeforeUpdate()
    {
        $this->internalTransaction = Yii::$app->db->beginTransaction();
        $changedAttributes = $this->owner->getDirtyAttributes($this->attributes);
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        $revisionTableId = RevisionTable::findOrCreateReturnScalar([
            'name' => $owner->getTableSchema()->name,
        ]);
        
        foreach ($changedAttributes as $attribute => $value) {
            $fieldId = RevisionField::findOrCreateReturnScalar(['name' => $attribute]);
            $typeId = RevisionValueType::findOrCreateReturnScalar(['name' => gettype($value)]);
            (new Revision([
                'revision_table_id' => $revisionTableId,
                'revision_field_id' => $fieldId,
                'revision_value_type_id' => $typeId,
                'record_id' => $owner->id,
                'value' => $value,
            ]))->save(false);
        }
    }

    /**
     * Ревизиия на удаление
     *
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function onBeforeDelete()
    {
        $this->internalTransaction = Yii::$app->db->beginTransaction();
        $this->deleteOrInsertInternal(RevisionOperationType::TYPE_DELETE);
        
    }
    
    /**
     *
     * @param $type integer RevisionOperationType
     */
    private function deleteOrInsertInternal($type)
    {
        $revisionTableId = RevisionTable::findOrCreateReturnScalar([
            'name' => $this->owner->getTableSchema()->name,
        ]);
    
        (new RevisionRecord([
            'revision_table_id' => $revisionTableId,
            'record_id' => $this->owner->id,
            'value' => Json::encode($this->owner->attributes),
            'revision_operation_type_id' => $type,
        ]))->save(false);
        
    }
    
    public function endTransaction()
    {
        $this->internalTransaction->commit();
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
            $tableName = $this->owner->getTableSchema()->name;
            $value = Revision::getValue($tableName, $this->owner->id, $attribute, $dateTime);
            return $value;
        } else {
            throw new Exception('Attribute is not under revision in config');
        }

    }
}
