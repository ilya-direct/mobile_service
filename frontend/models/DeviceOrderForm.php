<?php

namespace frontend\models;

use yii\base\Model;

/**
 * Class DeviceOrderForm
 *
 *  Форма заказа со страницы Device
 * @package frontend\models
 */
class DeviceOrderForm extends Model
{
    public $name;
    public $phone;
    public $email;
    public $device_id;
    public $service_id;
    public $db; // свойство для вывода ошибок
    public $time_from;


    public function rules()
    {
        return [
            [['name', 'phone'], 'required', 'message' => 'Необходимо заполнить'],
            ['phone', 'match',
                'pattern' => '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/',
                'message' => '+7 (XXX) XXX-XX-XX'],
            ['phone', 'filter', 'filter' => function ($value) {
                $newValue = '+' . preg_replace('/\D/', '', $value);
                return $newValue;
            }],
            ['email', 'email', 'message' => 'Email некорректен'],
            [['device_id', 'service_id'], 'filter', 'filter' => 'intval'],
            ['db', 'string'],
            ['time_from', 'match',
                'pattern' => '/^\d{2}:\d{2} \d{2}.\d{2}.\d{4}$/',
                'message' => 'XX:XX XX.XX.XXXX'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя *',
            'phone' => 'Телефон',
            'time_from' => 'Когда вам перезвонить?',
        ];
    }
    
}
