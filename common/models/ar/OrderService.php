<?php

namespace common\models\ar;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use common\components\behaviors\RevisionBehavior;
use common\components\db\ActiveRecord;

/**
 * This is the model class for table "{{%order_service}}".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $device_assign_id
 * @property boolean $deleted
 * @property string $created_at
 *
 * @property DeviceAssign $deviceAssign
 * @property Order $order
 */
class OrderService extends ActiveRecord
{
    public function behaviors()
    {
        return [
            'attribute' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'deleted'
                ],
                'value' => false,
            ],
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
                'updatedAtAttribute' => false,
            ],
            'revision' => [
                'class' => RevisionBehavior::className(),
                'attributes' => [
                    'deleted',
                ]
            ],

        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_service}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'device_assign_id'], 'required'],
            [['order_id', 'device_assign_id'], 'integer'],
            [['device_assign_id'], 'exist', 'skipOnError' => true, 'targetClass' => DeviceAssign::className(), 'targetAttribute' => ['device_assign_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'device_assign_id' => 'Device Assign ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeviceAssign()
    {
        return $this->hasOne(DeviceAssign::className(), ['id' => 'device_assign_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public static function deleteAll($condition = '', $soft = true)
    {
        $tr = Yii::$app->db->beginTransaction();
        /** @var self $models */
        $models = self::find()->where($condition)->all();
        foreach ($models as $model) {
            $model->delete($soft);
        }
        $tr->commit();

        return true;
    }

    public function delete($soft = true)
    {
        if ($soft) {
            $this->deleted = true;
            return $this->update(false);
        }

        return parent::delete();
    }
}
