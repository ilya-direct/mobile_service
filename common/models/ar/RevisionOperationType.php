<?php

namespace common\models\ar;

use common\components\db\ActiveRecord;

/**
 * Тип Ревизии INSERT, DELETE ..
 * @property integer $id
 * @property string $name
 *
 * @property RevisionRecord[] $revisionRecords
 */
class RevisionOperationType extends ActiveRecord
{
    const TYPE_INSERT = 1;
    const TYPE_DELETE = 2;
    const TYPE_UPDATE = 3;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%revision_operation_type}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название операции',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRevisionRecords()
    {
        return $this->hasMany(RevisionRecord::className(), ['revision_operation_type_id' => 'id']);
    }
    
    /**
     * Название типов для заполнения таблицы
     *
     * @return array
     */
    public static function typeNames()
    {
        return [
            self::TYPE_INSERT => 'Create',
            self::TYPE_DELETE => 'Delete',
            self::TYPE_UPDATE => 'Update',
        ];
    }
}
