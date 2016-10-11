<?php

namespace common\models\ar;

use Yii;
use yii\base\Exception;
use common\components\db\ActiveRecord;

/**
 * This is the model class for table "{{%order_provider}}".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Order[] $orders
 */
class OrderProvider extends ActiveRecord
{
    const PROVIDER_TOP_FORM = 1;
    const PROVIDER_TOP_FORM_FULL = 2;
    const PROVIDER_ADMIN_PANEL = 3;
    const PROVIDER_FOOTER_CALLBACK_FORM = 4;
    const PROVIDER_CALCULATOR = 5;
    const PROVIDER_DEVICE_FORM = 6;
    const PROVIDER_NOT_FOUND_DEVICE_FORM = 7;
    const PROVIDER_CONTACT_US_FORM = 8;
    const PROVIDER_ORDER_WITH_DISCOUNT = 9;
    const PROVIDER_COURIER_FORM = 10;

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

    public static function providerLabels()
    {
        return [
            self::PROVIDER_TOP_FORM => 'Верхняя форма "Оформить заявку" модальная',
            self::PROVIDER_TOP_FORM_FULL => 'Верхняя форма "Оформить заявку" на отдельной странице',
            self::PROVIDER_ADMIN_PANEL => 'Административная панель',
            self::PROVIDER_FOOTER_CALLBACK_FORM => 'Нижняя форма "Заявка на звонок"',
            self::PROVIDER_CALCULATOR => 'Калькулятор услуг на главной странице',
            self::PROVIDER_DEVICE_FORM => 'Форма на странице отдельного устройства',
            self::PROVIDER_NOT_FOUND_DEVICE_FORM => 'Форма "Не нашёл нужную модель"',
            self::PROVIDER_CONTACT_US_FORM => 'Форма "Оставьте нам сообщение"',
            self::PROVIDER_ORDER_WITH_DISCOUNT => 'Форма "Заявка на ремонт со скидкой"',
            self::PROVIDER_COURIER_FORM => 'Форма заказа мастера на дом',
        ];
    }
}
