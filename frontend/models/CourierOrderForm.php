<?php

namespace frontend\models;

use yii\base\Model;

/**
 * Форма на странице вызова мастера
 *
 * Class CourierOrderForm
 *
 * @package frontend\models
 */
class CourierOrderForm extends Model
{
    public $name;
    public $phone;
    public $db;

    public function rules()
    {
        return [
            [['name', 'phone'], 'required', 'message' => 'Необходимо заполнить'],
            ['phone', 'match',
                'pattern' => '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/',
                'message' => '+7 (XXX) XXX-XX-XX'],
            ['db', 'string'],
            ['name', 'string', 'max' => 30],
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
