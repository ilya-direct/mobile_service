<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use common\components\db\ActiveRecord;
use common\helpers\SystemHelper;

/**
 * Ревизия Записей
 *
 * @property integer $id
 * @property integer $revision_table_id
 * @property integer $record_id
 * @property string $value
 * @property integer $user_id
 * @property string $created_at
 * @property integer $revision_operation_type_id
 *
 * @property RevisionOperationType $revisionOperationType
 * @property RevisionTable $revisionTable
 * @property User $user
 */
class RevisionRecord extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            'attribute' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['user_id'],
                ],
                'value' => function() {
                    // Если изменения в БД происходят через консольный скрипт, то пользователь console
                    if (SystemHelper::isConsole()) {
                        $value = SystemHelper::getConsoleUserId();
                    } else {
                        $user = Yii::$app->user;
                        $value = $user && !$user->isGuest ? $user->id : null;
                    }
                    
                    return $value;
                }
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%revision_record}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'revision_table_id' => 'Revision Table ID',
            'record_id' => 'Record ID',
            'value' => 'Value',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'revision_operation_type_id' => 'Revision Operation Type ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRevisionOperationType()
    {
        return $this->hasOne(RevisionOperationType::className(), ['id' => 'revision_operation_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRevisionTable()
    {
        return $this->hasOne(RevisionTable::className(), ['id' => 'revision_table_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
}
