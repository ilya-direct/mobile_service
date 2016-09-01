<?php

namespace common\models\ar;

use Yii;
use common\components\behaviors\RevisionBehavior;
use common\components\db\ActiveRecord;

/**
 * This is the model class for table "{{%service}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $small_description
 * @property integer $service_category_id
 * @property integer $position
 * @property boolean $enabled
 *
 * @property DeviceAssign[] $deviceAssigns
 * @property ServiceCategory $serviceCategory
 *
 * @method mixed revisionValue(string $attribute,string $dateTime)
 */
class Service extends ActiveRecord
{
    public function behaviors()
    {
        return [
            'revision' => [
                'class' => RevisionBehavior::className(),
                'attributes' => [
                    'name',
                    'small_description',
                    'service_category_id',
                    'position',
                    'enabled',
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%service}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['small_description'], 'string'],
            [['service_category_id', 'position'], 'integer'],
            [['enabled'], 'boolean'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['service_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceCategory::className(), 'targetAttribute' => ['service_category_id' => 'id']],
            ['enabled', 'filter', 'filter' => 'boolval'],
            ['position', 'filter', 'filter' => 'intval'],
            ['small_description', 'default'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'small_description' => 'Описание',
            'position' => 'Позиция',
            'enabled' => 'Активна',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeviceAssigns()
    {
        return $this->hasMany(DeviceAssign::className(), ['service_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceCategory()
    {
        return $this->hasOne(ServiceCategory::className(), ['id' => 'service_category_id']);
    }

}
