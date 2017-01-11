<?php

namespace common\models\ar;

use common\components\behaviors\RevisionBehavior;
use common\components\db\ActiveRecord;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Производители
 * This is the model class for table "{{%vendor}}".
 *
 * @property integer $id
 * @property string $name
 * @property boolean $enabled
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 * @property integer $alias
 *
 * @property Device[] $devices
 * @property User $createdBy
 * @property User $updatedBy
 */
class Vendor extends ActiveRecord
{
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            'blameable' => [
                'class' => BlameableBehavior::className(),
            ],
            'slug' => [
                'class' => SluggableBehavior::className(),
                'slugAttribute' => 'alias',
                'attribute' => 'name',
                'ensureUnique' => true,
            ],
            'revision' => [
                'class' => RevisionBehavior::className(),
                'attributes' => [
                    'enabled',
                    'name',
                    'alias',
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%vendor}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'enabled'], 'required'],
            [['enabled'], 'boolean'],
            [['name', 'alias'], 'string', 'max' => 100],
            [['name', 'alias'], 'unique'],
            ['enabled', 'filter', 'filter' => 'boolval'],
            ['alias', 'default'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название производителя',
            'enabled' => 'Активен (отображать ли на сайте)',
            'created_at' => 'Время создание',
            'created_by' => 'Кем создан(id)',
            'updated_at' => 'Время последнего изменения',
            'updated_by' => 'Кем последний раз изменён(id)',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevices()
    {
        return $this->hasMany(Device::className(), ['vendor_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
}
