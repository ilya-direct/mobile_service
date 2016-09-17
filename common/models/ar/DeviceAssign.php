<?php

namespace common\models\ar;

use Yii;
use common\components\behaviors\RevisionBehavior;
use common\components\db\ActiveRecord;

/**
 * This is the model class for table "{{%device_assign}}".
 *
 * @property integer $id
 * @property integer $device_id
 * @property integer $service_id
 * @property integer $price
 * @property integer $price_old
 * @property boolean $enabled
 *
 * @property Device $device
 * @property Service $service
 *
 * @method mixed revisionValue(string $attribute,string $dateTime)
 */
class DeviceAssign extends ActiveRecord
{
    public function behaviors()
    {
        return [
            'revision' => [
                'class' => RevisionBehavior::className(),
                'attributes' => [
                    'enabled',
                    'price',
                    'price_old',
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%device_assign}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['device_id', 'service_id', 'price'], 'required'],
            [['device_id', 'service_id', 'price', 'price_old'], 'integer'],
            ['enabled', 'boolean'],
            [['device_id'], 'exist', 'skipOnError' => true, 'targetClass' => Device::className(), 'targetAttribute' => ['device_id' => 'id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'device_id' => 'Device ID',
            'service_id' => 'Service ID',
            'price' => 'Цена',
            'price_old' => 'Старая цена',
            'enabled' => 'Активна',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevice()
    {
        return $this->hasOne(Device::className(), ['id' => 'device_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::className(), ['id' => 'service_id']);
    }

    public static function validateCommaStr($str)
    {
        $assigns = explode(',', $str);
        $errors = [];

        $deviceAssignIds = [];
        foreach ($assigns as $assign) {
            $assign = intval(trim($assign));
            if ($assign) {
                if (static::findOne($assign)) {
                    $deviceAssignIds[] = $assign;
                } else {
                    $errors[] = $assign;
                }
            }
        }
        $deviceAssignIds = array_unique($deviceAssignIds);

        $success = empty($errors);

        return [
            'valid' => $success,
            'ids' => $deviceAssignIds,
            'errors' => $errors,
        ];
    }

}
