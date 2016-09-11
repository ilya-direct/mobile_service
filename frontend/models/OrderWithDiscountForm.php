<?php


namespace frontend\models;


use yii\base\Model;

class OrderWithDiscountForm extends Model
{
    public $name;
    public $phone;
    public $device_id;
    public $db; // свойство для вывода ошибок
    public $time;


    public function rules()
    {
        return [
            [['name', 'phone'], 'required', 'message' => 'Необходимо заполнить'],
            ['phone', 'match',
                'pattern' => '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/',
                'message' => '+7 (XXX) XXX-XX-XX'],
            ['time', 'match',
                'pattern' => '/^\d{2}:\d{2} \d{2}.\d{2}.\d{4}$/',
                'message' => 'XX:XX XX.XX.XXXX'],
            ['db', 'string'],
            ['device_id', 'filter', 'filter' => 'intval'],
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
