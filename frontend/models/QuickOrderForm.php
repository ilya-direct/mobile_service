<?php

namespace frontend\models;

use common\models\ar\Order;

class QuickOrderForm extends Order
{
    public function rules()
    {
        return [
            [['first_name', 'phone'], 'required', 'message' => 'Необходимо заполнить'],
            ['phone', 'match', 'pattern' => '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/', 'message' => '+7 (XXX) XXX-XX-XX'],
            ['phone', 'filter', 'filter' => function ($value) {
                $newValue = '+' . preg_replace('/\D/', '', $value);
                return $newValue;
            }],
            ['email', 'string', 'max' => 50],
            ['email', 'email'],
            ['client_comment', 'string', 'max' => 255],
        ];
    }
}
