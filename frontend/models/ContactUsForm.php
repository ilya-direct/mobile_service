<?php

namespace frontend\models;

use yii\base\Model;

/**
 * Форма "Оставьте нам сообщение" на странице Контакты
 * Class ContactUsForm
 *
 * @package frontend\models
 */
class ContactUsForm extends Model
{
    public $name;
    public $phone;
    public $email;
    public $message;
    public $db; // Для вывода ошибок БД


    public function rules()
    {
        return [
            [['name', 'phone'], 'required', 'message' => 'Необходимо заполнить'],
            ['phone', 'match',
                'pattern' => '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/',
                'message' => '+7 (XXX) XXX-XX-XX'],
            ['email', 'email', 'message' => 'Email некорректен'],
            ['db', 'string'],
            ['name', 'string', 'max' => 30],
            ['email', 'string', 'max' => 50],
            ['message', 'string', 'max' => 255],
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
