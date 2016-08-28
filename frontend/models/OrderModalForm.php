<?php

namespace frontend\models;


use yii\base\Model;

class OrderModalForm extends Model
{
    public $name;
    public $phone;
    public $email;
    public $comment;
    // Полная форма на странице site/quick-order
    public $fullForm = false;

    public function rules()
    {
        return [
            [['name', 'phone'], 'required'],
            ['email', 'email'],
            ['name', 'string', 'max' => 20],
            ['phone', 'match', 'pattern' => '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/', 'message' => 'Формат +7 (XXX) XXX-XX-XX'],
            ['comment', 'string', 'max' => 100],
            ['phone', 'filter', 'filter' => function ($value) {
                $newValue = '+' . preg_replace('/\D/', '', $value);
                return $newValue;
            }],
            ['fullForm', 'boolean'],
            [['email', 'comment', 'fullForm'], 'default'],
        ];
    }

    public function phoneFilter($value)
    {
        $newValue = '+' . preg_replace('/\D/','', $value);
        return $newValue;
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'email' => 'Email',
            'phone' => 'Телефон',
            'comment' => 'Комментарий',
        ];
    }
    
}
