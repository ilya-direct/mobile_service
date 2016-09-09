<?php

namespace frontend\models;

use yii\base\Model;

/**
 * Class NotFoundDeviceForm
 * Форма : Не нашли нужную модель?
 * @package frontend\models
 */
class NotFoundDeviceForm extends Model
{
    public $db;
    public $name;
    public $phone;
    public $device; // Нужная модель для ремонта

    public function rules()
    {
        return [
            [['name', 'phone'], 'required', 'message' => 'Необходимо заполнить'],
            [['name', 'device'], 'string', 'max' => 30],
            ['phone', 'match',
                'pattern' => '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/',
                'message' => '+7 (XXX) XXX-XX-XX',
            ],
            ['db', 'string'], // Поле для отображения ошибок (Костыль)
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
