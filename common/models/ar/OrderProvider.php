<?php

namespace common\models\ar;

use Yii;

/**
 * This is the model class for table "{{%order_provider}}".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Order[] $orders
 */
class OrderProvider extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_provider}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Источник заказа',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['order_provider_id' => 'id']);
    }

    private static $providers = [
        'top_form' => 'Верхняя форма "Оформить заявку" модальная',
        'top_form_full' => 'Верхняя форма "Оформить заявку" на отдельной странице',
        'admin_panel' => 'Административная панель',
        'footer_callback_form' => 'Нижняя форма "Заявка на звонок"',
        'calculator' => 'Калькулятор услуг на главной странице',
    ];

    public static function get($providerAlias)
    {
        $provider = self::$providers[$providerAlias];
        $provider_id = self::find()
            ->select('id')
            ->where(['name' => $provider])
            ->scalar();

        return $provider_id;
    }
}
