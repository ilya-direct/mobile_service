<?php


namespace frontend\models;


use common\models\ar\Device;
use common\models\ar\Order;

class OrderWithDiscountForm extends Order
{

    public function rules()
    {
        return [
            [['first_name', 'phone'], 'required', 'message' => 'Необходимо заполнить'],
            ['phone', 'match',
                'pattern' => '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/',
                'message' => '+7 (XXX) XXX-XX-XX'],
            ['phone', 'filter', 'filter' => function ($value) {
                $newValue = '+' . preg_replace('/\D/', '', $value);
                return $newValue;
            }],
            ['time_to', 'match',
                'pattern' => '/^\d{2}:\d{2}$/',
                'message' => 'XX:XX XX.XX.XXXX'],
            ['device_provider_id', function($attribute) {
                $value = (int)$this->$attribute;

                if (!empty($value) && !Device::find()->where(['id' => $value])->exists()) {
                    $this->device_provider_id = null;
                }
            }],
            [['time_to', 'device_provider_id'], 'default'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'first_name' => 'Имя *',
            'phone' => 'Телефон *',
            'time_to' => 'Когда вам перезвонить?',
        ];
    }

    
}
