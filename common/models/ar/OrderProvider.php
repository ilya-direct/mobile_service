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
    const PROVIDER_TOP_FORM = 'top_form';
    const PROVIDER_TOP_FORM_FULL = 'top_form_full';
    const PROVIDER_ADMIN_PANEL = 'admin_panel';
    const PROVIDER_FOOTER_CALLBACK_FORM = 'footer_callback_form';
    const PROVIDER_CALCULATOR = 'calculator';
    const PROVIDER_DEVICE_FORM = 'device_form';
    const PROVIDER_NOT_FOUND_DEVICE_FORM = 'not_found_device_form';
    const PROVIDER_CONTACT_US_FORM = 'contact_us_form';
    const PROVIDER_ORDER_WITH_DISCOUNT = 'order_with_discount';
    const PROVIDER_COURIER_FORM = 'courier_form' ;

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

    public static function getProviders()
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

    /**
     * Возвращает id провайдера из БД
     * @param string $provider псевдоним провайдера
     * @return bool|int|string
     * @throws Exception
     */
    public static function getId($provider)
    {
        $providers = self::getProviders();

        if (isset($providers[$provider])) {
            return self::findOrCreateReturnScalar(['name' => $providers[$provider]]);
        } else {
            throw new Exception('Unknown OrderProvider: ' . $provider);
        }
    }
}
