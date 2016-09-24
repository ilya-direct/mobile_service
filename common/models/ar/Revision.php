<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\Query;
use common\components\db\ActiveRecord;
use common\helpers\SystemHelper;

/**
 * This is the model class for table "revision".
 *
 * @property integer $id
 * @property integer $revision_table_id
 * @property integer $revision_field_id
 * @property integer $record_id
 * @property integer $revision_value_type_id
 * @property string $value
 * @property integer $user_id
 * @property string $created_at
 * @property boolean $operation_type
 *
 * @property User $user
 * @property RevisionField $revisionField
 * @property RevisionTable $revisionTable
 * @property RevisionValueType $revisionValueType
 */
class Revision extends \yii\db\ActiveRecord
{
    const OPERATION_INSERT = true;
    const OPERATION_UPDATE = false;

    private static $consoleUserId;

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
            'blamable' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['user_id'],
                ],
                'value' => function() {
                    // Если изменения в БД происходят через консольный скрипт, то пользователь console
                    if (SystemHelper::isConsole()) {
                        $value = self::getConsoleUserId();
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
        return '{{%revision}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'revision_table_id' => 'Таблица по ревизии',
            'revision_field_id' => 'Поле по ревизии',
            'record_id' => 'id записи в изменённой таблице',
            'revision_value_type_id' => 'Тип данных у значения',
            'value' => 'Новое значение',
            'user_id' => 'Пользователь, изменивший значение',
            'created_at' => 'Время изменения',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRevisionField()
    {
        return $this->hasOne(RevisionField::className(), ['id' => 'revision_field_id']);
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
    public function getRevisionValueType()
    {
        return $this->hasOne(RevisionValueType::className(), ['id' => 'revision_value_type_id']);
    }

    /**
     * Невозможно изменять ревизию
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function update($runValidation = true, $attributeNames = null)
    {
        return false;
    }

    /**
     * Значение поле у таблицы в определённый момент времени. Если время неуказано или указано некорректно вернётся текущее значение
     *
     * @param string $table Название таблицы
     * @param integer $recordId id записи в таблице
     * @param string $field название поле таблицы
     * @param string $dateTime время
     * @return bool|mixed|string значение
     */
    public static function getValue($table, $recordId, $field, $dateTime = null)
    {
        if (!strtotime($dateTime)) {
            $value= (new Query())
                ->select($field)
                ->from($table)
                ->where(['id' => $recordId])
                ->scalar();
            return $value;
        }
        $tableId = RevisionTable::find()
            ->select('id')
            ->where(['name' => $table])
            ->scalar();

        $fieldId = RevisionField::find()
            ->select('id')
            ->where(['name' => $field])
            ->scalar();

        /** @var Revision $revision */
        $revision = self::find()
            ->where([
                'revision_table_id' => $tableId,
                'revision_field_id' => $fieldId,
                'record_id' => $recordId,
            ])
            ->andWhere(['<=', 'created_at', $dateTime])
            ->orderBy(['created_at' => SORT_DESC])
            ->one();

        $revisionValueType = RevisionValueType::find()
            ->select('name')
            ->where(['id' => $revision->revision_value_type_id])
            ->scalar();

        $value = $revision->value;
        if (isset(RevisionValueType::$castFunctions[$revisionValueType])) {
            $value = call_user_func(RevisionValueType::$castFunctions[$revisionValueType], $revision->value);
        }

        return $value;
    }

    private static function getConsoleUserId()
    {
        if (!self::$consoleUserId) {
            self::$consoleUserId = User::find()
                ->select('id')
                ->where(['email' => 'console@console.ru'])
                ->scalar();
        }

        return self::$consoleUserId;
    }
}
