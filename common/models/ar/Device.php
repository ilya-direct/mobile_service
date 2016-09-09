<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\helpers\FileHelper;
use common\components\behaviors\RevisionBehavior;
use common\components\db\ActiveRecord;

/**
 * This is the model class for table "device".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $image
 * @property integer $device_category_id
 * @property integer $enabled
 * @property integer $vendor_id
 * @property string $alias уникальное имя для URL
 *
 * @property DeviceCategory $deviceCategory
 * @property DeviceAssign[] $deviceAssigns
 * @property Vendor $vendor
 * @property string|boolean $imageWebPath
 *
 * @method mixed revisionValue(string $attribute,string $dateTime)
 */
class Device extends ActiveRecord
{
    const IMAGE_WEB_PATH = '/images/devices';
    const IMAGE_SAVE_PATH = '@frontend/web/images/devices';

    public $image;

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
                    'image',
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
            [['name', 'alias'], 'unique'],
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
            ['image', 'image'],
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

    /**
     * Относительный путь до изображения устройства. Или false если изображение не найдено
     * Пример: /images/devices/nokia-7220.jpg
     * @return bool|string
     */
    public function getImageWebPath()
    {
        $path = Yii::getAlias(Device::IMAGE_SAVE_PATH);
        $alias = $this->alias;
        $images = FileHelper::findFiles($path, ['filter' => function ($path) use ($alias) {

            return (boolean)preg_match('/'. preg_quote($this->alias, '/') . '\.\w{3,4}$/u', $path);
        }]);

        return empty($images) ? false : Device::IMAGE_WEB_PATH . '/' . basename($images[0]);

    }

}
