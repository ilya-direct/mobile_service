<?php

namespace frontend\models;

use common\models\ar\Order;

/**
 * Форма на странице вызова мастера
 *
 * Class CourierOrderForm
 *
 * @package frontend\models
 */
class CourierOrderForm extends Order
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
            ['first_name', 'string', 'max' => 30],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя *',
            'phone' => 'Телефон *',
            'time_from' => 'Когда вам перезвонить?',
        ];
    }
}
