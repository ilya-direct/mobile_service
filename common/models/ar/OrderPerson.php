<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%order_person}}".
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $phone
 * @property string $email
 * @property string $address
 *
 * @property Order[] $orders
 */
class OrderPerson extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_person}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'phone'], 'required'],
            [['first_name', 'last_name', 'middle_name'], 'string', 'max' => 30],
            [['phone'], 'string', 'max' => 12],
            [['email'], 'string', 'max' => 50],
            [['address'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'middle_name' => 'Отчество',
            'phone' => 'Phone',
            'email' => 'Email',
            'address' => 'Адрес проживания',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['order_person_id' => 'id']);
    }
}
