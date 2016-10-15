<?php

namespace frontend\models;

use common\models\ar\Order;

/**
 * Форма "Оставьте нам сообщение" на странице Контакты
 * Class ContactUsForm
 *
 * @package frontend\models
 */
class ContactUsForm extends Order
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
            ['email', 'email', 'message' => 'Email некорректен'],
            ['first_name', 'string', 'max' => 30],
            ['email', 'string', 'max' => 50],
            ['client_comment', 'string', 'max' => 255],
            [['client_comment', 'email'], 'default'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Ваше имя *',
            'phone' => 'Ваш телефон',
            'email' => 'Email',
            'message' => 'Ваше сообщение',
        ];
    }

}
