<?php

namespace frontend\models;

use common\models\ar\Device;
use common\models\ar\DeviceAssign;
use common\models\ar\Order;

/**
 * Class DeviceOrderForm
 *
 *  Форма заказа со страницы Device
 * @package frontend\models
 */
class DeviceOrderForm extends Order
{
    public $device_assign_id;


    public function rules()
    {
        return [
            [['first_name', 'phone', 'device_assign_id'], 'required', 'message' => 'Необходимо заполнить'],
            ['phone', 'match',
                'pattern' => '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/',
                'message' => '+7 (XXX) XXX-XX-XX'],
            ['phone', 'filter', 'filter' => function ($value) {
                $newValue = '+' . preg_replace('/\D/', '', $value);
                return $newValue;
            }],
            ['email', 'email', 'message' => 'Email некорректен'],
            [['device_assign_id'], 'filter', 'filter' => 'intval'],
            ['device_assign_id', 'default'],
            ['device_assign_id', function($attribute) {
                $value = (int)$this->$attribute;

                $exists  = DeviceAssign::find()
                    ->where(['id' => $value])
                    ->enabled()
                    ->exists();

                if (!$exists) {
                    $this->device_assign_id = null;
                }
            }],
            ['device_provider_id', function($attribute) {
                $value = (int)$this->$attribute;

                if (!empty($value) && !Device::find()->where(['id' => $value])->exists()) {
                    $this->device_provider_id = null;
                }
            }],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя *',
            'phone' => 'Телефон',
        ];
    }
    
}
