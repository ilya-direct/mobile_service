<?php

namespace frontend\models;

use yii\base\Model;

/**
 * Class PriceCalculatorForm
 *
 * Калькулятор услуг
 * @package frontend\models
 */
class PriceCalculatorForm extends Model
{
    public $device_category_id;
    public $vendor_id;
    public $device_id;
    public $service_id;
    public $name;
    public $phone;

    public function rules()
    {
        return [
            [['name', 'phone'], 'required', 'message' => 'Необходимо заполнить'],
            ['phone', 'match', 'pattern' => '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/', 'message' => '+7 (XXX) XXX-XX-XX'],
            ['phone', 'filter', 'filter' => function ($value) {
                $newValue = '+' . preg_replace('/\D/', '', $value);
                return $newValue;
            }],
        ];
    }
    
}
