<?php


namespace frontend\models;


use yii\base\Model;

/**
 * Class FooterCallbackForm
 *
 * Нижняя форма
 *
 * @package frontend\models
 */
class FooterCallbackForm extends Model
{
    public $first_name;
    public $phone;

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
