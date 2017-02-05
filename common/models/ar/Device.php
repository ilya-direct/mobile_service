<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\SluggableBehavior;
use common\components\behaviors\RevisionBehavior;
use common\components\validators\UniqueInsensitiveValidator;
use common\components\db\ActiveRecord;

/**
 * This is the model class for table "device".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $image_name
 * @property integer $device_category_id
 * @property integer $enabled
 * @property integer $vendor_id
 * @property string $alias уникальное имя для URL
 *
 * @property DeviceCategory $deviceCategory
 * @property DeviceAssign[] $deviceAssigns
 * @property Vendor $vendor
 *
 * @method mixed revisionValue(string $attribute,string $dateTime)
 */
class Device extends ActiveRecord
{
    const IMAGE_SAVE_FOLDER = 'device-images';

    public function behaviors()
    {
        return [
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
                    'vendor_id',
                    'device_category_id',
                    'description',
                    'image_name',
                    'name',
                    'alias',
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%device}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['device_category_id', 'vendor_id'], 'integer'],
            ['enabled', 'boolean'],
            [['name', 'alias'], 'string', 'max' => 255],
            ['name', UniqueInsensitiveValidator::className()],
            ['alias', 'unique'],
            [
                'device_category_id', 'exist',
                'skipOnError' => true,
                'targetClass' => DeviceCategory::className(),
                'targetAttribute' => ['device_category_id' => 'id'],
            ],
            ['enabled', 'filter', 'filter' => 'boolval'],
            [['device_category_id', 'vendor_id'], 'filter', 'filter' => 'intval'],
            [['device_category_id', 'vendor_id', 'description', 'alias'] , 'default', 'isEmpty' => function($var) { return empty($var); }],
            [
                'vendor_id',
                'exist',
                'targetClass' => Vendor::className(),
                'targetAttribute' => ['vendor_id' => 'id']
            ],
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
            'description' => 'Описание',
            'image' => 'Изображение',
            'enabled' => 'Активен',
            'vendor_id' => 'Производитель'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeviceCategory()
    {
        return $this->hasOne(DeviceCategory::className(), ['id' => 'device_category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeviceAssigns()
    {
        return $this->hasMany(DeviceAssign::className(), ['device_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVendor()
    {
        return $this->hasOne(Vendor::className(), ['id' => 'vendor_id']);
    }

    public function getImageUrl()
    {
        $imageUrl = null;
        if (trim($this->image_name)) {
            $imageUrl = Yii::$app->storage->getUrl($this->image_name, self::IMAGE_SAVE_FOLDER);
        }
        
        return $imageUrl ?: null;
    }
}
