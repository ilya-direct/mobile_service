<?php


namespace frontend\models;


use common\models\ar\Order;

/**
 * Нижняя форма Оставить заявку
 *
 * @package frontend\models
 */
class FooterCallbackForm extends Order
{

    public function rules()
    {
        return [
            ['first_name', 'required', 'message' => 'Необходимо заполнить имя'],
            ['phone', 'required', 'message' => 'Необходимо заполнить телефон'],
            ['first_name', 'string', 'max' => 30],
            ['phone', 'match',
                'pattern' => '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/',
                'message' => 'Формат +7 (XXX) XXX-XX-XX',
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
            'first_name' => 'Ваше имя *',
            'phone' => 'Ваш телефон *',
        ];
    }
    
}
