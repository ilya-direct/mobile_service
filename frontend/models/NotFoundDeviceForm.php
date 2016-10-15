<?php

namespace frontend\models;

use common\models\ar\Order;

/**
 * Форма : Не нашли нужную модель?
 * @package frontend\models
 */
class NotFoundDeviceForm extends Order
{

    public function rules()
    {
        return [
            [['first_name', 'phone'], 'required', 'message' => 'Необходимо заполнить'],
            [['first_name', 'client_comment'], 'string', 'max' => 30],
            ['phone', 'match',
                'pattern' => '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/',
                'message' => '+7 (XXX) XXX-XX-XX',
            ],
            ['phone', 'filter', 'filter' => function ($value) {
                $newValue = '+' . preg_replace('/\D/', '', $value);
                return $newValue;
            }],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'ВАШЕ ИМЯ *',
            'phone' => 'ВАШ ТЕЛЕФОН *',
            'device' => 'ИНТЕРЕСУЮЩАЯ МОДЕЛЬ?',
        ];
    }
}
