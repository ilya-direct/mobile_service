<?php

namespace common\models\ar;

use Yii;
use yii\base\Exception;
use linslin\yii2\curl\Curl;

/**
 * This is the model class for table "{{%order_person}}".
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $phone
 * @property string $email
 * @property string $address
 * @property float $address_longitude Долгота
 * @property float $address_latitude Широта
 *
 * @property Order[] $orders
 */
class OrderPerson extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_person}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'phone'], 'required'],
            [['first_name', 'last_name', 'middle_name'], 'string', 'max' => 30],
            ['phone', 'match', 'pattern' => '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/', 'message' => 'Формат +7 (XXX) XXX-XX-XX'],
            ['phone', 'filter', 'filter' => function ($value) {
                $newValue = '+' . preg_replace('/\D/', '', $value);
                return $newValue;
            }],
            ['email', 'string', 'max' => 50],
            ['email', 'email'],
            ['address', 'string', 'max' => 255],
            ['address', 'validateAddress'],
            [['last_name', 'middle_name', 'email', 'address'], 'default'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'middle_name' => 'Отчество',
            'phone' => 'Телефон',
            'email' => 'Email',
            'address' => 'Адрес проживания',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['order_person_id' => 'id']);
    }

    public function validateAddress($attribute)
    {
        $value = $this->$attribute;

        $yandexGeocoder = new Curl();
        // если у библиотеки Curl не установлены сертификаты раскоментировать
        $yandexGeocoder->setOption(CURLOPT_SSL_VERIFYPEER, 0);
        try {
            $result = $yandexGeocoder->get(
                'https://geocode-maps.yandex.ru/1.x/?&format=json&results=1&bbox=36.044480,54.983701~38.835007,56.495778&geocode=' . urlencode($value),
                false);
        } catch (Exception $e) {
            $this->addError($attribute, 'Не удалось подключиться к Яндекс.Картам');
            return null;
        }
        if (!$result) {
            $this->addError($attribute, 'Не удалось проверить адрес (timeout)');
            return null;
        }

        $yandexGeocoderResult = $result['response']['GeoObjectCollection'];
        if ($yandexGeocoderResult['metaDataProperty']['GeocoderResponseMetaData']['found'] != "0") {
            $yandexGeoObject = $yandexGeocoderResult['featureMember'][0]['GeoObject'];
            $meta = $yandexGeoObject['metaDataProperty']['GeocoderMetaData'];
            if ($meta['kind'] == 'house') {
                if ($meta['precision'] == 'exact') {
                    $address = $meta['text'];
                    $this->$attribute = $address;
                    list($longitude, $latitude) = explode(' ', $yandexGeoObject['Point']['pos']);
                    $this->address_longitude = $longitude;
                    $this->address_latitude = $latitude;

                    return null;
                } else {
                    $this->addError($attribute, 'Данный дом не найден на Яндекс.Картах');
                }
            } else {
                $this->addError($attribute, 'Адрес должен быть с точностью до дома');
            }
        } else {
            $this->addError($attribute, 'Адрес не найден на Яндекс.Картах');
        }

        return null;
    }

    public function beforeSave($insert)
    {
        if (!$insert && array_key_exists('address', $this->dirtyAttributes) && is_null($this->dirtyAttributes['address'])) {
            $this->address_latitude = null;
            $this->address_longitude = null;
        }

        return parent::beforeSave($insert);
    }
}
